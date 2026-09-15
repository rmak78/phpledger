<?php
declare(strict_types=1);

/** This ledger is internal: no invoice, bill, payment document or write transport is introduced. */
function pl_oi_id(array $input, string $field): int
{
    if (!is_int($input[$field] ?? null) || $input[$field] < 1) {
        throw new DomainException('Supply a valid ' . $field . '.');
    }
    return $input[$field];
}

function pl_activate_open_item_account(int $actorId, int $companyId, int $bookId, int $accountId, string $reason): array
{
    pl_demo_require_setup_action();
    $reason = pl_ledger_text($reason, 'Activation reason', 500);
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $accountId, $reason): array {
        $access = pl_require_company_access($actorId, $companyId, true);
        if ($access['role'] !== 'owner') { throw new DomainException('Only an owner can activate open-item accounting.'); }
        pl_ledger_book($companyId, $bookId, true);
        $prior = DB::queryFirstRow('SELECT * FROM pl_open_item_accounts WHERE account_id = %i AND company_id = %i AND book_id = %i FOR UPDATE', $accountId, $companyId, $bookId);
        if ($prior) { return $prior; }
        $account = DB::queryFirstRow('SELECT * FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i FOR SHARE', $accountId, $companyId, $bookId);
        if (!$account || !(bool) $account['is_active'] || $account['currency'] !== null || !in_array($account['role'], ['receivables', 'payables'], true)) {
            throw new DomainException('Choose an active receivable or payable control account.');
        }
        if (DB::queryFirstField('SELECT id FROM pl_journal_lines WHERE account_id = %i LIMIT 1 FOR SHARE', $accountId)
            || DB::queryFirstField('SELECT id FROM pl_opening_documents WHERE company_id = %i AND book_id = %i AND account_id = %i LIMIT 1 FOR SHARE', $companyId, $bookId, $accountId)) {
            throw new DomainException('Only unused control accounts can be activated. Existing balances require a reviewed AR/AP cutover.');
        }
        DB::insert('pl_open_item_accounts', ['account_id' => $accountId, 'company_id' => $companyId, 'book_id' => $bookId, 'activated_by' => $actorId, 'reason' => $reason]);
        return DB::queryFirstRow('SELECT * FROM pl_open_item_accounts WHERE account_id = %i', $accountId);
    });
}

/** Outstanding is a sum of immutable referenced GL line amounts, never an independently editable total. */
function pl_open_item_state(int $companyId, int $bookId, int $itemId): array
{
    $item = DB::queryFirstRow('SELECT * FROM pl_open_items WHERE id = %i AND company_id = %i AND book_id = %i FOR UPDATE', $itemId, $companyId, $bookId);
    if (!$item) { throw new DomainException('This open item is not available in the selected book.'); }
    $entries = DB::query('SELECT e.id AS entry_id, e.kind, e.reversal_of_id AS entry_reversal_of_id, l.*, COALESCE(e.allocated_amount_fc, l.amount_fc) AS amount_fc, COALESCE(e.allocated_amount_base, l.amount_base) AS amount_base, e.opening_document_id, od.document_date AS opening_document_date, od.due_date AS opening_due_date, od.reference AS opening_reference, j.journal_date FROM pl_open_item_entries e LEFT JOIN pl_opening_documents od ON od.id = e.opening_document_id JOIN pl_journal_lines l ON l.id = e.journal_line_id AND l.company_id = e.company_id AND l.book_id = e.book_id JOIN pl_journals j ON j.id = l.journal_id WHERE e.item_id = %i AND e.company_id = %i AND e.book_id = %i ORDER BY e.id FOR SHARE', $itemId, $companyId, $bookId);
    $fc = '0.0000'; $base = '0.0000'; $recognized = null; $reversed = false; $latestDate = null;
    foreach ($entries as $entry) {
        $positive = in_array($entry['kind'], ['recognition', 'allocation_reversal'], true);
        $fc = $positive ? bcadd($fc, $entry['amount_fc'], 4) : bcsub($fc, $entry['amount_fc'], 4);
        $base = $positive ? bcadd($base, $entry['amount_base'], 4) : bcsub($base, $entry['amount_base'], 4);
        if ($entry['kind'] === 'recognition') { $recognized = $entry; }
        if ($entry['kind'] === 'recognition_reversal') { $reversed = true; }
        if ($latestDate === null || $entry['journal_date'] > $latestDate) { $latestDate = $entry['journal_date']; }
    }
    return $item + ['recognition' => $recognized, 'entries' => $entries, 'remaining_fc' => $fc, 'remaining_base' => $base, 'reversed' => $reversed, 'latest_activity_date' => $latestDate];
}

function pl_get_open_item(int $actorId, int $companyId, int $bookId, int $itemId): array
{
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $itemId): array {
        pl_require_company_access($actorId, $companyId);
        pl_ledger_book($companyId, $bookId);
        return pl_open_item_state($companyId, $bookId, $itemId);
    });
}

function pl_oi_command(int $actorId, int $companyId, int $bookId, string $key, array $payload, callable $work): array
{
    pl_demo_require_setup_action();
    $key = pl_request_key($key);
    $hash = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $key, $hash, $work): array {
        pl_require_company_access($actorId, $companyId, true);
        pl_ledger_book($companyId, $bookId, true);
        $prior = DB::queryFirstRow('SELECT payload_hash, result_json FROM pl_open_item_commands WHERE company_id = %i AND book_id = %i AND request_key = %s FOR UPDATE', $companyId, $bookId, $key);
        if ($prior) {
            if (!hash_equals($prior['payload_hash'], $hash)) { throw new DomainException('This open-item request key has different content.'); }
            $result = json_decode($prior['result_json'], true, 512, JSON_THROW_ON_ERROR);
            ksort($result);
            return $result;
        }
        $result = $work('open-item:' . hash('sha256', $key));
        ksort($result);
        DB::insert('pl_open_item_commands', ['company_id' => $companyId, 'book_id' => $bookId, 'actor_id' => $actorId, 'request_key' => $key, 'payload_hash' => $hash, 'result_json' => json_encode($result, JSON_THROW_ON_ERROR)]);
        return $result;
    });
}

/** Actual input is frozen directly in the posting; a rate-table reference retains its own provenance. */
function pl_oi_rate(int $actorId, int $companyId, int $bookId, string $currency, string $date, ?string $override, ?int $rateId, string $type): array
{
    $book = pl_ledger_book($companyId, $bookId);
    $base = (string) $book['currency'];
    if ($currency === $base) {
        if ($override !== null && bccomp(pl_fx_rate($override), '1', 12) !== 0) { throw new DomainException('Domestic amounts use rate one.'); }
        if ($rateId !== null) { throw new DomainException('Domestic entries do not need a currency-rate reference.'); }
        return ['currency' => $currency, 'rate' => '1.000000000000', 'rate_type' => 'spot', 'rate_source_id' => null, 'rate_is_stale' => false, 'ic_counterparty_entity_id' => null];
    }
    if ($override !== null) {
        return ['currency' => $currency, 'rate' => pl_fx_rate($override), 'rate_type' => $type, 'rate_source_id' => null, 'rate_is_stale' => false, 'ic_counterparty_entity_id' => null];
    }
    $row = $rateId === null
        ? pl_currency_rate_lookup($actorId, $companyId, $bookId, $currency, $base, $date, 'spot')
        : DB::queryFirstRow('SELECT * FROM pl_currency_rates WHERE id = %i AND company_id = %i AND book_id = %i FOR SHARE', $rateId, $companyId, $bookId);
    if (!$row || $row['from_currency'] !== $currency || $row['to_currency'] !== $base || $row['rate_date'] > $date
        || !in_array($row['rate_type'], ['spot', 'actual'], true)) {
        throw new DomainException('Supply an applicable manual spot or actual rate on or before the posting date.');
    }
    return ['currency' => $currency, 'rate' => (string) $row['rate'], 'rate_type' => $row['rate_type'], 'rate_source_id' => (int) $row['id'], 'rate_is_stale' => $row['rate_date'] < $date, 'ic_counterparty_entity_id' => null];
}

function pl_oi_line(int $accountId, string $amountFc, string $amountBase, bool $debit, array $snapshot, string $description): array
{
    return ['account_id' => $accountId, 'description' => $description, 'debit' => $debit ? $amountBase : '0.0000', 'credit' => $debit ? '0.0000' : $amountBase, 'amount_fc' => $amountFc, 'amount_base' => $amountBase] + $snapshot;
}

function pl_open_item_recognize(int $actorId, int $companyId, int $bookId, array $input): array
{
    $data = [
        'action' => 'recognize', 'party_id' => pl_oi_id($input, 'party_id'), 'control_account_id' => pl_oi_id($input, 'control_account_id'),
        'offset_account_id' => pl_oi_id($input, 'offset_account_id'), 'currency' => pl_currency_code(pl_ledger_text($input['currency'] ?? null, 'Currency', 3)),
        'amount_fc' => pl_amount(pl_ledger_text($input['amount_fc'] ?? null, 'Foreign amount', 30)),
        'date' => pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Recognition date', 10)),
        'source_reference' => pl_ledger_text($input['source_reference'] ?? null, 'Source reference', 120),
        'description' => pl_ledger_text($input['description'] ?? null, 'Description', 500),
        'rate' => isset($input['rate']) ? pl_fx_rate(pl_ledger_text($input['rate'], 'Rate', 40)) : null,
        'rate_source_id' => isset($input['rate_source_id']) ? pl_oi_id($input, 'rate_source_id') : null,
    ];
    if (bccomp($data['amount_fc'], '0', 4) <= 0) { throw new DomainException('Recognition needs a positive amount.'); }
    return pl_oi_command($actorId, $companyId, $bookId, pl_ledger_text($input['idempotency_key'] ?? null, 'Request key', 128), $data,
        function (string $journalKey) use ($actorId, $companyId, $bookId, $data): array {
            $party = pl_get_party($actorId, $companyId, $bookId, $data['party_id']);
            $account = DB::queryFirstRow('SELECT a.* FROM pl_open_item_accounts t JOIN pl_accounts a ON a.id = t.account_id WHERE t.account_id = %i AND t.company_id = %i AND t.book_id = %i FOR SHARE', $data['control_account_id'], $companyId, $bookId);
            if (!$account || !in_array($account['role'], ['receivables', 'payables'], true)) { throw new DomainException('Activate an unused control account before recognition.'); }
            $receivable = $account['role'] === 'receivables';
            if (!(bool) ($party[$receivable ? 'is_customer' : 'is_vendor'] ?? false)) { throw new DomainException('The party must have the corresponding customer or vendor role.'); }
            $snapshot = pl_oi_rate($actorId, $companyId, $bookId, $data['currency'], $data['date'], $data['rate'], $data['rate_source_id'], 'spot');
            if (($party['linked_entity_id'] ?? null) !== null) { $snapshot['ic_counterparty_entity_id'] = (int) $party['linked_entity_id']; }
            $base = pl_fx_convert($data['amount_fc'], $snapshot['rate']);
            DB::insert('pl_open_items', ['company_id' => $companyId, 'book_id' => $bookId, 'party_id' => $data['party_id'], 'control_account_id' => $data['control_account_id'], 'direction' => $receivable ? 'receivable' : 'payable', 'currency' => $data['currency'], 'source_reference' => $data['source_reference'], 'created_by' => $actorId]);
            $itemId = (int) DB::insertId();
            $book = pl_ledger_book($companyId, $bookId);
            $journal = pl_post_journal_locked($actorId, $companyId, $bookId, ['date' => $data['date'], 'currency' => $book['currency'], 'source_type' => 'open_item_recognition', 'source_reference' => 'open-item:' . $itemId, 'idempotency_key' => $journalKey, 'description' => $data['description'], 'lines' => [
                pl_oi_line($data['control_account_id'], $data['amount_fc'], $base, $receivable, $snapshot, $data['description']),
                pl_oi_line($data['offset_account_id'], $data['amount_fc'], $base, !$receivable, $snapshot, $data['description']),
            ]]);
            return ['item_id' => $itemId, 'journal_id' => (int) $journal['id']];
        });
}

function pl_oi_allocated_base(array $item, string $amount): string
{
    if (!$item['recognition'] || $item['reversed'] || bccomp($amount, '0', 4) <= 0 || bccomp($amount, $item['remaining_fc'], 4) > 0) {
        throw new DomainException('The open item is reversed, unavailable or would be over-allocated.');
    }
    if (bccomp($amount, $item['remaining_fc'], 4) === 0) { return $item['remaining_base']; }
    // Prorate the remaining actual carrying amount; final allocation takes the exact residual.
    $value = bcdiv(bcmul($item['remaining_base'], $amount, 24), $item['remaining_fc'], 24);
    $rounded = pl_amount(bcadd($value, '0.00005', 4));
    if (bccomp($rounded, '0', 4) <= 0 || bccomp($rounded, $item['remaining_base'], 4) >= 0) {
        throw new DomainException('This partial allocation cannot be represented at ledger precision.');
    }
    return $rounded;
}

function pl_settle_open_item(int $actorId, int $companyId, int $bookId, array $input): array
{
    $data = [
        'action' => 'settle', 'item_id' => pl_oi_id($input, 'item_id'), 'bank_account_id' => pl_oi_id($input, 'bank_account_id'),
        'gain_account_id' => pl_oi_id($input, 'gain_account_id'), 'loss_account_id' => pl_oi_id($input, 'loss_account_id'),
        'amount_fc' => pl_amount(pl_ledger_text($input['amount_fc'] ?? null, 'Allocated amount', 30)),
        'date' => pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Settlement date', 10)),
        'actual_rate' => isset($input['actual_rate']) ? pl_fx_rate(pl_ledger_text($input['actual_rate'], 'Actual rate', 40)) : null,
        'rate_source_id' => isset($input['rate_source_id']) ? pl_oi_id($input, 'rate_source_id') : null,
        'description' => pl_ledger_text($input['description'] ?? null, 'Description', 500),
    ];
    return pl_oi_command($actorId, $companyId, $bookId, pl_ledger_text($input['idempotency_key'] ?? null, 'Request key', 128), $data,
        function (string $journalKey) use ($actorId, $companyId, $bookId, $data): array {
            $item = pl_open_item_state($companyId, $bookId, $data['item_id']);
            $carrying = pl_oi_allocated_base($item, $data['amount_fc']);
            if ($data['date'] < $item['latest_activity_date']) { throw new DomainException('Settlement cannot precede the latest open-item activity.'); }
            $book = pl_ledger_book($companyId, $bookId);
            $bank = DB::queryFirstRow('SELECT * FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i FOR SHARE', $data['bank_account_id'], $companyId, $bookId);
            if (!$bank || $bank['role'] !== 'cash_bank' || !(bool) $bank['is_active']) { throw new DomainException('Choose an active cash/bank account in this book.'); }
            foreach (['gain_account_id' => 'income', 'loss_account_id' => 'expense'] as $field => $type) {
                if (!DB::queryFirstRow('SELECT id FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i AND type = %s AND is_active = 1 FOR SHARE', $data[$field], $companyId, $bookId, $type)) { throw new DomainException('Choose scoped, active realised gain and loss accounts.'); }
            }
            $snapshot = pl_oi_rate($actorId, $companyId, $bookId, $item['currency'], $data['date'], $data['actual_rate'], $data['rate_source_id'], 'actual');
            $settlementBase = pl_fx_convert($data['amount_fc'], $snapshot['rate']);
            $bankCurrency = $bank['currency'] ?? $book['currency'];
            if (!in_array($bankCurrency, [$book['currency'], $item['currency']], true)) { throw new DomainException('A third-currency bank conversion requires a separate conversion transaction.'); }
            $domestic = pl_oi_rate($actorId, $companyId, $bookId, $book['currency'], $data['date'], null, null, 'spot');
            $receipt = $item['direction'] === 'receivable';
            if (!$receipt && $bankCurrency !== $book['currency']) { throw new DomainException('Outgoing settlements require a functional-currency bank until foreign-bank carrying-value allocation is implemented.'); }
            $recognition = $item['recognition'];
            $historical = array_intersect_key($recognition, array_flip(['currency', 'rate', 'rate_type', 'rate_source_id', 'rate_is_stale', 'ic_counterparty_entity_id']));
            $historical['rate_source_id'] = $historical['rate_source_id'] === null ? null : (int) $historical['rate_source_id'];
            $historical['rate_is_stale'] = (bool) $historical['rate_is_stale'];
            $historical['ic_counterparty_entity_id'] = $historical['ic_counterparty_entity_id'] === null ? null : (int) $historical['ic_counterparty_entity_id'];
            $lines = [pl_oi_line((int) $item['control_account_id'], $data['amount_fc'], $carrying, !$receipt, $historical, 'Historic open-item carrying value'),
                pl_oi_line($data['bank_account_id'], $bankCurrency === $book['currency'] ? $settlementBase : $data['amount_fc'], $settlementBase, $receipt, $bankCurrency === $book['currency'] ? $domestic : $snapshot, $data['description'])];
            $difference = bcsub($settlementBase, $carrying, 4);
            if (bccomp($difference, '0', 4) !== 0) {
                $gain = ($receipt && bccomp($difference, '0', 4) > 0) || (!$receipt && bccomp($difference, '0', 4) < 0);
                $magnitude = ltrim($difference, '-');
                $lines[] = pl_oi_line($gain ? $data['gain_account_id'] : $data['loss_account_id'], $magnitude, $magnitude, !$gain, $domestic, $gain ? 'Realised FX gain' : 'Realised FX loss');
            }
            $journal = pl_post_journal_locked($actorId, $companyId, $bookId, ['date' => $data['date'], 'currency' => $book['currency'], 'source_type' => 'open_item_settlement', 'source_reference' => 'open-item:' . $item['id'], 'idempotency_key' => $journalKey, 'description' => $data['description'], 'lines' => $lines], null, (int) $item['id']);
            return ['item_id' => (int) $item['id'], 'journal_id' => (int) $journal['id'], 'allocated_fc' => $data['amount_fc'], 'allocated_base' => $carrying, 'settlement_base' => $settlementBase,
                'transaction_currency' => $item['currency'], 'settlement_rate' => $snapshot['rate'], 'settlement_rate_type' => $snapshot['rate_type'],
                'settlement_rate_source_id' => $snapshot['rate_source_id'], 'settlement_rate_is_stale' => $snapshot['rate_is_stale']];
        });
}

/** Called by the posting funnel under its book lock, never an unchecked conversion override. */
function pl_open_item_validate_settlement_basis(int $companyId, int $bookId, array $payload, int $itemId): int
{
    if ($payload['source_type'] !== 'open_item_settlement' || $payload['source_reference'] !== 'open-item:' . $itemId) { throw new DomainException('Invalid settlement source identity.'); }
    $item = pl_open_item_state($companyId, $bookId, $itemId);
    $lines = array_values(array_filter($payload['lines'], static fn (array $line): bool => (int) $line['account_id'] === (int) $item['control_account_id']));
    if (count($lines) !== 1) { throw new DomainException('A settlement needs exactly one open-item control line.'); }
    $line = $lines[0];
    $expected = pl_oi_allocated_base($item, $line['amount_fc']);
    $debit = $item['direction'] === 'payable';
    if ($payload['date'] < $item['latest_activity_date'] || $line['amount_base'] !== $expected
        || $line[$debit ? 'debit' : 'credit'] !== $expected) { throw new DomainException('Settlement must relieve the persisted historic carrying amount.'); }
    foreach (['currency', 'rate', 'rate_type', 'rate_source_id', 'rate_is_stale', 'ic_counterparty_entity_id'] as $field) {
        $matches = $field === 'rate_is_stale' ? (bool) $line[$field] === (bool) $item['recognition'][$field] : (string) $line[$field] === (string) $item['recognition'][$field];
        if (!$matches) { throw new DomainException('Settlement must retain the recognition currency snapshot.'); }
    }
    return (int) $item['control_account_id'];
}

function pl_open_item_assert_correction_allowed(int $companyId, int $bookId, int $journalId): void
{
    if (DB::queryFirstField('SELECT e.id FROM pl_open_item_entries e JOIN pl_journal_lines l ON l.id = e.journal_line_id WHERE l.journal_id = %i AND e.company_id = %i AND e.book_id = %i AND e.opening_document_id IS NOT NULL LIMIT 1 FOR SHARE', $journalId, $companyId, $bookId)) {
        throw new DomainException('Converted opening debt retains its journal basis; use reviewed correcting transactions rather than reversing the cutover.');
    }
    $entries = DB::query("SELECT e.* FROM pl_open_item_entries e JOIN pl_journal_lines l ON l.id = e.journal_line_id WHERE l.journal_id = %i AND e.company_id = %i AND e.book_id = %i AND e.kind = 'recognition'", $journalId, $companyId, $bookId);
    foreach ($entries as $entry) {
        $item = pl_open_item_state($companyId, $bookId, (int) $entry['item_id']);
        if ($item['recognition'] && (bccomp($item['remaining_fc'], $item['recognition']['amount_fc'], 4) !== 0 || bccomp($item['remaining_base'], $item['recognition']['amount_base'], 4) !== 0)) {
            throw new DomainException('Reverse the active allocations explicitly before reversing this recognition.');
        }
    }
}

function pl_open_item_assert_generic_reversal_allowed(int $companyId, int $bookId, int $journalId): void
{
    pl_open_item_assert_correction_allowed($companyId, $bookId, $journalId);
}

function pl_open_item_validate_posting(int $companyId, int $bookId, array $payload, ?int $reversalOf = null, ?int $settlementItemId = null): void
{
    $tracked = DB::query('SELECT account_id FROM pl_open_item_accounts WHERE company_id = %i AND book_id = %i FOR SHARE', $companyId, $bookId);
    $ids = array_map('intval', array_column($tracked, 'account_id'));
    $controls = array_values(array_filter($payload['lines'], static fn (array $line): bool => in_array((int) $line['account_id'], $ids, true)));
    if ($controls === []) {
        if (in_array($payload['source_type'], ['open_item_recognition', 'open_item_settlement'], true)) { throw new DomainException('An open-item source must post to its tracked control account.'); }
        return;
    }
    if ($reversalOf !== null) {
        pl_open_item_assert_correction_allowed($companyId, $bookId, $reversalOf);
        $entries = DB::query('SELECT e.item_id FROM pl_open_item_entries e JOIN pl_journal_lines l ON l.id = e.journal_line_id WHERE l.journal_id = %i AND e.company_id = %i AND e.book_id = %i', $reversalOf, $companyId, $bookId);
        if ($entries === []) { throw new DomainException('Tracked control reversals require their open-item source.'); }
        foreach ($entries as $entry) {
            if ($payload['date'] < pl_open_item_state($companyId, $bookId, (int) $entry['item_id'])['latest_activity_date']) { throw new DomainException('A reversal cannot precede the latest open-item activity.'); }
        }
        return;
    }
    if (count($controls) !== 1 || !preg_match('/^open-item:([1-9][0-9]*)$/D', $payload['source_reference'], $match)) { throw new DomainException('Tracked control accounts require one explicit open-item source.'); }
    $item = pl_open_item_state($companyId, $bookId, (int) $match[1]);
    $line = $controls[0];
    if ((int) $line['account_id'] !== (int) $item['control_account_id'] || $line['currency'] !== $item['currency']) { throw new DomainException('The control account and currency must match the open item.'); }
    if ($payload['source_type'] === 'open_item_recognition') {
        if ($item['entries'] !== [] || bccomp($line[$item['direction'] === 'receivable' ? 'debit' : 'credit'], '0', 4) <= 0) { throw new DomainException('An open item may be recognized once with the correct direction.'); }
    } elseif ($payload['source_type'] === 'open_item_settlement' && $settlementItemId === (int) $item['id']) {
        pl_open_item_validate_settlement_basis($companyId, $bookId, $payload, $settlementItemId);
    } else { throw new DomainException('Use the internal open-item service for tracked control accounts.'); }
}

/** Atomic construction ties the only outstanding calculator to the exact posted GL lines. */
function pl_open_item_track_posting(int $companyId, int $bookId, int $journalId, array $payload, ?int $reversalOf = null): void
{
    if ($reversalOf !== null) {
        $entries = DB::query('SELECT e.*, l.line_number FROM pl_open_item_entries e JOIN pl_journal_lines l ON l.id = e.journal_line_id WHERE l.journal_id = %i AND e.company_id = %i AND e.book_id = %i', $reversalOf, $companyId, $bookId);
        foreach ($entries as $entry) {
            if (!in_array($entry['kind'], ['recognition', 'allocation'], true)) { throw new DomainException('An open-item reversal cannot itself be reversed.'); }
            $lineId = DB::queryFirstField('SELECT id FROM pl_journal_lines WHERE journal_id = %i AND line_number = %i', $journalId, $entry['line_number']);
            DB::insert('pl_open_item_entries', ['company_id' => $companyId, 'book_id' => $bookId, 'item_id' => $entry['item_id'], 'kind' => $entry['kind'] === 'recognition' ? 'recognition_reversal' : 'allocation_reversal', 'journal_line_id' => $lineId, 'reversal_of_id' => $entry['id']]);
        }
        return;
    }
    if (!in_array($payload['source_type'], ['open_item_recognition', 'open_item_settlement'], true)) { return; }
    $itemId = (int) substr($payload['source_reference'], strlen('open-item:'));
    $item = pl_open_item_state($companyId, $bookId, $itemId);
    $lineId = DB::queryFirstField('SELECT id FROM pl_journal_lines WHERE journal_id = %i AND account_id = %i', $journalId, $item['control_account_id']);
    DB::insert('pl_open_item_entries', ['company_id' => $companyId, 'book_id' => $bookId, 'item_id' => $itemId, 'kind' => $payload['source_type'] === 'open_item_recognition' ? 'recognition' : 'allocation', 'journal_line_id' => $lineId, 'reversal_of_id' => null]);
}
