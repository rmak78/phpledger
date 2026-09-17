<?php
declare(strict_types=1);

/** Count the same unmatched statement rows shown by reconciliation, across draft statements. */
function pl_bank_pending_review_count(int $actorId, int $companyId, int $bookId): int
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    return (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_bank_statement_rows r JOIN pl_bank_statements s ON s.id=r.statement_id AND s.company_id=r.company_id AND s.book_id=r.book_id LEFT JOIN pl_bank_matches m ON m.row_id=r.id WHERE s.company_id=%i AND s.book_id=%i AND s.status=%s AND m.journal_line_id IS NULL', $companyId, $bookId, 'draft');
}

/** A statement balance may be overdrawn; transaction columns remain unsigned. */
function pl_bank_signed_amount(string $value): string
{
    $negative = str_starts_with($value, '-');
    $amount = pl_amount($negative ? substr($value, 1) : $value);
    return $negative ? bcsub('0', $amount, 4) : $amount;
}

/** Strict original CSV contract. No encoding, date, separator, or column guessing. */
function pl_bank_parse_csv(string $csv): array
{
    if (strlen($csv) > 524288 || !mb_check_encoding($csv, 'UTF-8') || str_contains($csv, "\0")) {
        throw new DomainException('Use UTF-8 CSV no larger than 512 KiB.');
    }
    $csv = preg_replace('/^\xEF\xBB\xBF/', '', $csv);
    // PHP's CSV reader tolerates unterminated quotes; reject malformed syntax first.
    $state = 'start';
    for ($i = 0, $length = strlen($csv); $i < $length; $i++) {
        $character = $csv[$i];
        if ($state === 'quoted') {
            if ($character === '"') {
                $state = 'after_quote';
            }
        } elseif ($state === 'after_quote') {
            if ($character === '"') {
                $state = 'quoted';
            } elseif ($character === ',' || $character === "\r" || $character === "\n") {
                $state = 'start';
            } else {
                throw new DomainException('CSV contains malformed quoted fields.');
            }
        } elseif ($character === '"') {
            if ($state !== 'start') {
                throw new DomainException('CSV contains malformed quoted fields.');
            }
            $state = 'quoted';
        } elseif ($character === ',' || $character === "\r" || $character === "\n") {
            $state = 'start';
        } else {
            $state = 'unquoted';
        }
    }
    if ($state === 'quoted') {
        throw new DomainException('CSV contains an unterminated quoted field.');
    }
    $stream = fopen('php://temp', 'w+');
    if ($stream === false) {
        throw new RuntimeException('CSV preview is unavailable.');
    }
    try {
        fwrite($stream, $csv);
        rewind($stream);
        if (fgetcsv($stream, 0, ',', '"', '') !== ['date', 'reference', 'description', 'money_in', 'money_out']) {
            throw new DomainException('CSV headers must be date,reference,description,money_in,money_out in that order.');
        }
        $rows = [];
        while (($row = fgetcsv($stream, 0, ',', '"', '')) !== false) {
            if ($row === [null]) {
                continue;
            }
            if (count($row) !== 5 || count($rows) >= 500) {
                throw new DomainException('Use five columns per row and at most 500 transactions.');
            }
            $rows[] = ['date' => $row[0], 'reference' => $row[1], 'description' => $row[2], 'money_in' => $row[3], 'money_out' => $row[4]];
        }
        return $rows;
    } finally {
        fclose($stream);
    }
}

function pl_bank_normalize_statement(array $input): array
{
    if (!is_int($input['account_id'] ?? null) || $input['account_id'] < 1) {
        throw new DomainException('Choose the cash/bank account.');
    }
    $result = [
        'account_id' => $input['account_id'],
        'reference' => pl_ledger_text($input['reference'] ?? null, 'Statement reference', 120),
        'start_date' => pl_ledger_date(pl_ledger_text($input['start_date'] ?? null, 'Statement start', 10)),
        'end_date' => pl_ledger_date(pl_ledger_text($input['end_date'] ?? null, 'Statement end', 10)),
        'opening_balance' => pl_bank_signed_amount(pl_ledger_text($input['opening_balance'] ?? null, 'Opening balance', 22)),
        'closing_balance' => pl_bank_signed_amount(pl_ledger_text($input['closing_balance'] ?? null, 'Closing balance', 22)),
        'baseline_confirmed' => ($input['baseline_confirmed'] ?? false) === true,
        'rows' => [],
    ];
    if ($result['start_date'] > $result['end_date']) {
        throw new DomainException('Statement end must be on or after its start.');
    }
    if (!is_array($input['rows'] ?? null) || !array_is_list($input['rows']) || count($input['rows']) > 500) {
        throw new DomainException('A statement supports up to 500 transaction rows.');
    }
    $total = $result['opening_balance'];
    $seen = [];
    foreach ($input['rows'] as $row) {
        if (!is_array($row)) {
            throw new DomainException('Every statement row must contain the five required fields.');
        }
        $date = pl_ledger_date(pl_ledger_text($row['date'] ?? null, 'Transaction date', 10));
        $ref = pl_ledger_text($row['reference'] ?? null, 'Transaction reference', 120);
        $in = pl_amount(pl_ledger_text($row['money_in'] ?? null, 'Money in', 21));
        $out = pl_amount(pl_ledger_text($row['money_out'] ?? null, 'Money out', 21));
        if ($date < $result['start_date'] || $date > $result['end_date']) {
            throw new DomainException('Every transaction date must be inside the statement dates.');
        }
        if (isset($seen[$ref])) {
            throw new DomainException('Transaction references must be unique, including across statements for this bank account.');
        }
        if ((bccomp($in, '0', 4) > 0) === (bccomp($out, '0', 4) > 0)) {
            throw new DomainException('Each row needs positive money in or money out; put 0 in the other column.');
        }
        $seen[$ref] = true;
        $result['rows'][] = ['date' => $date, 'reference' => $ref, 'description' => pl_ledger_text($row['description'] ?? null, 'Description', 500), 'money_in' => $in, 'money_out' => $out];
        $total = bcadd($total, bcsub($in, $out, 4), 4);
    }
    if (bccomp($total, $result['closing_balance'], 4) !== 0) {
        throw new DomainException('Opening balance plus money in minus money out must equal the statement closing balance.');
    }
    return $result;
}

function pl_bank_require_write(int $actorId, int $companyId, int $bookId): void
{
    if (pl_demo_enabled()) {
        throw new DomainException('Bank imports and reconciliation changes are disabled in the public sample.');
    }
    pl_require_company_access($actorId, $companyId, true);
    pl_ledger_book($companyId, $bookId, true);
    pl_require_book_ready($companyId);
}

function pl_bank_balance(int $companyId, int $bookId, int $accountId, string $date, bool $before = false): string
{
    return bcadd((string) DB::queryFirstField('SELECT COALESCE(SUM(l.debit - l.credit), 0) FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = l.company_id AND j.book_id = l.book_id WHERE l.company_id = %i AND l.book_id = %i AND l.account_id = %i AND j.journal_date ' . ($before ? '<' : '<=') . ' %s FOR SHARE', $companyId, $bookId, $accountId, $date), '0', 4);
}

/** Called with the book lock, including when importing a previously reviewed preview. */
function pl_bank_validate_import(int $companyId, int $bookId, array $data): array
{
    if (!DB::queryFirstField('SELECT id FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i AND role = %s AND type = %s AND is_active = 1 FOR SHARE', $data['account_id'], $companyId, $bookId, 'cash_bank', 'asset')) {
        throw new DomainException('Choose an active cash/bank account in this company and book.');
    }
    $previous = DB::queryFirstRow("SELECT * FROM pl_bank_statements WHERE company_id = %i AND book_id = %i AND account_id = %i AND status <> 'cancelled' ORDER BY end_date DESC, id DESC LIMIT 1 FOR UPDATE", $companyId, $bookId, $data['account_id']);
    if ($previous) {
        $next = (new DateTimeImmutable($previous['end_date'], new DateTimeZone('UTC')))->modify('+1 day')->format('Y-m-d');
        if ($previous['status'] !== 'completed' || $data['start_date'] !== $next || bccomp($data['opening_balance'], (string) $previous['closing_balance'], 4) !== 0) {
            throw new DomainException('Complete the previous statement first. The next statement must start the following day with the previous closing balance.');
        }
        $baseline = ['baseline_date' => $previous['baseline_date'], 'baseline_balance' => $previous['baseline_balance'], 'previous_statement_id' => (int) $previous['id']];
    } else {
        $start = DB::queryFirstField('SELECT start_date FROM pl_companies WHERE id = %i FOR SHARE', $companyId);
        if ($data['start_date'] < $start || !$data['baseline_confirmed']) {
            throw new DomainException('For the first statement, confirm all cash entries before its start are already cleared. Start on or after the company start date.');
        }
        $balance = pl_bank_balance($companyId, $bookId, $data['account_id'], $data['start_date'], true);
        if (bccomp($balance, $data['opening_balance'], 4) !== 0) {
            throw new DomainException('The first statement opening balance must equal the ledger balance before its start. Resolve opening balances or earlier outstanding items first.');
        }
        $baseline = ['baseline_date' => $data['start_date'], 'baseline_balance' => $balance, 'previous_statement_id' => null];
    }
    if (DB::queryFirstField('SELECT id FROM pl_bank_statements WHERE company_id = %i AND book_id = %i AND account_id = %i AND active_reference = %s FOR UPDATE', $companyId, $bookId, $data['account_id'], $data['reference'])) {
        throw new DomainException('This statement reference has already been imported.');
    }
    if ($data['rows'] !== [] && DB::queryFirstField('SELECT id FROM pl_bank_statement_rows WHERE company_id = %i AND book_id = %i AND account_id = %i AND active_reference IN %ls LIMIT 1 FOR UPDATE', $companyId, $bookId, $data['account_id'], array_column($data['rows'], 'reference'))) {
        throw new DomainException('A transaction reference has already been imported for this bank account.');
    }
    return $baseline;
}

function pl_bank_preview_statement(int $actorId, int $companyId, int $bookId, array $input): array
{
    $data = pl_bank_normalize_statement($input);
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $data): array {
        pl_bank_require_write($actorId, $companyId, $bookId);
        $baseline = pl_bank_validate_import($companyId, $bookId, $data);
        return ['data' => $data, 'baseline' => $baseline, 'digest' => hash('sha256', json_encode([$companyId, $bookId, $data, $baseline], JSON_THROW_ON_ERROR))];
    });
}

function pl_bank_import_statement(int $actorId, int $companyId, int $bookId, array $input, string $key, string $reviewedDigest): array
{
    $data = pl_bank_normalize_statement($input);
    $key = pl_request_key($key);
    $hash = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $data, $key, $hash, $reviewedDigest): array {
        pl_bank_require_write($actorId, $companyId, $bookId);
        $existing = DB::queryFirstRow('SELECT id, payload_hash FROM pl_bank_statements WHERE company_id = %i AND book_id = %i AND import_key = %s FOR UPDATE', $companyId, $bookId, $key);
        if ($existing) {
            if (!hash_equals($existing['payload_hash'], $hash)) {
                throw new DomainException('This import request already belongs to different statement content.');
            }
            return pl_bank_get_statement($actorId, $companyId, $bookId, (int) $existing['id']);
        }
        $baseline = pl_bank_validate_import($companyId, $bookId, $data);
        $digest = hash('sha256', json_encode([$companyId, $bookId, $data, $baseline], JSON_THROW_ON_ERROR));
        if (!hash_equals($digest, $reviewedDigest)) {
            throw new DomainException('The statement or ledger baseline changed since preview. Preview again before importing.');
        }
        $header = $data;
        unset($header['rows']);
        $header['baseline_confirmed'] = $header['baseline_confirmed'] ? 1 : 0;
        DB::insert('pl_bank_statements', $header + $baseline + ['company_id' => $companyId, 'book_id' => $bookId, 'import_key' => $key, 'payload_hash' => $hash, 'imported_by' => $actorId]);
        $id = (int) DB::insertId();
        foreach ($data['rows'] as $index => $row) {
            $row['transaction_date'] = $row['date'];
            $row['active_reference'] = $row['reference'];
            unset($row['date']);
            DB::insert('pl_bank_statement_rows', $row + ['statement_id' => $id, 'company_id' => $companyId, 'book_id' => $bookId, 'account_id' => $data['account_id'], 'line_number' => $index + 1]);
        }
        return pl_bank_get_statement($actorId, $companyId, $bookId, $id);
    });
}

function pl_bank_get_statement(int $actorId, int $companyId, int $bookId, int $statementId, ?array $options = null): array
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    $statement = DB::queryFirstRow('SELECT s.*, a.name AS account_name, a.code AS account_code FROM pl_bank_statements s JOIN pl_accounts a ON a.id = s.account_id AND a.company_id = s.company_id AND a.book_id = s.book_id WHERE s.id = %i AND s.company_id = %i AND s.book_id = %i FOR SHARE', $statementId, $companyId, $bookId);
    if (!$statement) {
        throw new DomainException('This statement is not available in the selected company and book.');
    }
    foreach (['id', 'company_id', 'book_id', 'account_id', 'revision', 'imported_by'] as $field) {
        $statement[$field] = (int) $statement[$field];
    }
    $where = ' FROM pl_bank_statement_rows r LEFT JOIN pl_bank_matches m ON m.row_id = r.id LEFT JOIN pl_journal_lines l ON l.id = m.journal_line_id LEFT JOIN pl_journals j ON j.id = l.journal_id WHERE r.statement_id = %i AND r.company_id = %i AND r.book_id = %i';
    $args = [$statementId, $companyId, $bookId];
    $limit = '';
    $order = 'r.line_number';
    if ($options !== null) {
        $size = pl_table_size($options['page_size'] ?? 25);
        $statement['records_total'] = (int) DB::queryFirstField('SELECT COUNT(*)' . $where, ...$args);
        $search = pl_ledger_text($options['search'] ?? '', 'Search', 160, false);
        if ($search !== '') {
            $where .= ' AND (LOCATE(%s, r.reference) > 0 OR LOCATE(%s, r.description) > 0)';
            array_push($args, $search, $search);
        }
        $statement['filtered_total'] = $search === '' ? $statement['records_total'] : (int) DB::queryFirstField('SELECT COUNT(*)' . $where, ...$args);
        $order = pl_table_order($options, 'bank');
        $limit = ' LIMIT %i OFFSET %i';
        $statement['pages'] = max(1, (int)ceil($statement['filtered_total'] / $size));
        $statement['page'] = min($statement['pages'], max(1, (int)($options['page'] ?? 1)));
        array_push($args, $size, ($statement['page'] - 1) * $size);
    }
    $statement['rows'] = DB::query('SELECT r.*, m.journal_line_id, m.matched_at, m.matched_by, l.journal_id, j.journal_date' . $where . ' ORDER BY ' . $order . $limit . ' FOR SHARE', ...$args);
    return $statement;
}

/** All exact-amount candidates are suggestions only; older outstanding entries remain selectable. */
function pl_bank_candidates(int $actorId, int $companyId, int $bookId, int $statementId, int $rowId, int $page = 1): array
{
    $statement = pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
    $row = null;
    foreach ($statement['rows'] as $candidateRow) {
        if ((int) $candidateRow['id'] === $rowId) {
            $row = $candidateRow;
        }
    }
    if (!$row) {
        throw new DomainException('Choose a row from this statement.');
    }
    return DB::query('SELECT l.id, l.journal_id, j.journal_date, j.description, j.source_reference, l.debit, l.credit FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = l.company_id AND j.book_id = l.book_id LEFT JOIN pl_bank_matches m ON m.journal_line_id = l.id WHERE l.company_id = %i AND l.book_id = %i AND l.account_id = %i AND l.debit = %s AND l.credit = %s AND j.journal_date >= %s AND j.journal_date <= %s AND m.row_id IS NULL ORDER BY ABS(DATEDIFF(j.journal_date, %s)), l.id LIMIT 101 OFFSET %i', $companyId, $bookId, $statement['account_id'], $row['money_in'], $row['money_out'], $statement['baseline_date'], $statement['end_date'], $row['transaction_date'], (min(1000000, max(1, $page)) - 1) * 100);
}

function pl_bank_outstanding_lines(int $actorId, int $companyId, int $bookId, int $statementId, int $page = 1): array
{
    $statement = pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
    return DB::query('SELECT l.id, l.journal_id, j.journal_date, j.source_reference, j.description, l.debit, l.credit FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = l.company_id AND j.book_id = l.book_id LEFT JOIN pl_bank_matches m ON m.journal_line_id = l.id LEFT JOIN pl_bank_statement_rows r ON r.id = m.row_id LEFT JOIN pl_bank_statements s ON s.id = r.statement_id WHERE l.company_id = %i AND l.book_id = %i AND l.account_id = %i AND j.journal_date >= %s AND j.journal_date <= %s AND (m.row_id IS NULL OR s.end_date > %s) ORDER BY j.journal_date, l.id LIMIT 51 OFFSET %i', $companyId, $bookId, $statement['account_id'], $statement['baseline_date'], $statement['end_date'], $statement['end_date'], (min(1000000, max(1, $page)) - 1) * 50);
}

function pl_bank_match_row(int $actorId, int $companyId, int $bookId, int $statementId, int $rowId, ?int $lineId, int $expectedRevision): array
{
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $statementId, $rowId, $lineId, $expectedRevision): array {
        pl_bank_require_write($actorId, $companyId, $bookId);
        $statement = pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
        if ($statement['status'] !== 'draft' || (int) $statement['revision'] !== $expectedRevision) {
            throw new DomainException('The statement changed or is completed. Review the latest statement before changing a match.');
        }
        $row = DB::queryFirstRow('SELECT r.*, m.journal_line_id FROM pl_bank_statement_rows r LEFT JOIN pl_bank_matches m ON m.row_id = r.id WHERE r.id = %i AND r.statement_id = %i AND r.company_id = %i AND r.book_id = %i FOR UPDATE', $rowId, $statementId, $companyId, $bookId);
        if (!$row) {
            throw new DomainException('Choose a row from this statement.');
        }
        if ($lineId !== null) {
            $line = DB::queryFirstRow('SELECT l.id FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = l.company_id AND j.book_id = l.book_id WHERE l.id = %i AND l.company_id = %i AND l.book_id = %i AND l.account_id = %i AND l.debit = %s AND l.credit = %s AND j.journal_date >= %s AND j.journal_date <= %s FOR SHARE', $lineId, $companyId, $bookId, $statement['account_id'], $row['money_in'], $row['money_out'], $statement['baseline_date'], $statement['end_date']);
            if (!$line || DB::queryFirstField('SELECT row_id FROM pl_bank_matches WHERE journal_line_id = %i AND row_id <> %i FOR UPDATE', $lineId, $rowId)) {
                throw new DomainException('Choose an unused journal line with the same signed amount from this bank account and through the statement end.');
            }
        }
        $oldId = $row['journal_line_id'] === null ? null : (int) $row['journal_line_id'];
        if ($oldId === $lineId) {
            return $statement;
        }
        if ($oldId !== null) {
            DB::delete('pl_bank_matches', 'row_id = %i', $rowId);
            DB::insert('pl_bank_match_events', ['row_id' => $rowId, 'action' => 'unmatch', 'journal_line_id' => $oldId, 'actor_id' => $actorId]);
        }
        if ($lineId !== null) {
            DB::insert('pl_bank_matches', ['row_id' => $rowId, 'company_id' => $companyId, 'book_id' => $bookId, 'account_id' => $statement['account_id'], 'journal_line_id' => $lineId, 'matched_by' => $actorId]);
            DB::insert('pl_bank_match_events', ['row_id' => $rowId, 'action' => 'match', 'journal_line_id' => $lineId, 'actor_id' => $actorId]);
        }
        DB::update('pl_bank_statements', ['revision' => $expectedRevision + 1], 'id = %i', $statementId);
        return pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
    });
}

/** Snapshot is recomputed in the same book-locked transaction as final confirmation. */
function pl_bank_reconciliation_summary(int $actorId, int $companyId, int $bookId, int $statementId): array
{
    $statement = pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
    $ledger = pl_bank_balance($companyId, $bookId, (int) $statement['account_id'], $statement['end_date']);
    $baseline = pl_bank_balance($companyId, $bookId, (int) $statement['account_id'], $statement['baseline_date'], true);
    $outstanding = DB::queryFirstRow('SELECT COALESCE(SUM(l.debit - l.credit), 0) AS amount, COUNT(*) AS count FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = l.company_id AND j.book_id = l.book_id LEFT JOIN pl_bank_matches m ON m.journal_line_id = l.id LEFT JOIN pl_bank_statement_rows r ON r.id = m.row_id LEFT JOIN pl_bank_statements s ON s.id = r.statement_id WHERE l.company_id = %i AND l.book_id = %i AND l.account_id = %i AND j.journal_date >= %s AND j.journal_date <= %s AND (m.row_id IS NULL OR s.end_date > %s) FOR SHARE', $companyId, $bookId, $statement['account_id'], $statement['baseline_date'], $statement['end_date'], $statement['end_date']);
    $unmatched = count(array_filter($statement['rows'], static fn(array $row): bool => $row['journal_line_id'] === null));
    $adjusted = bcadd((string) $statement['closing_balance'], (string) $outstanding['amount'], 4);
    $difference = bcsub($ledger, $adjusted, 4);
    return ['ledger_balance' => $ledger, 'outstanding_balance' => bcadd((string) $outstanding['amount'], '0', 4), 'outstanding_count' => (int) $outstanding['count'], 'adjusted_statement_balance' => $adjusted, 'difference' => $difference, 'unmatched_count' => $unmatched, 'baseline_unchanged' => bccomp($baseline, (string) $statement['baseline_balance'], 4) === 0, 'ready' => $unmatched === 0 && bccomp($difference, '0', 4) === 0 && bccomp($baseline, (string) $statement['baseline_balance'], 4) === 0];
}

function pl_bank_complete_statement(int $actorId, int $companyId, int $bookId, int $statementId, int $expectedRevision, string $key): array
{
    $key = pl_request_key($key);
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $statementId, $expectedRevision, $key): array {
        pl_bank_require_write($actorId, $companyId, $bookId);
        $statement = pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
        if ($statement['status'] === 'completed') {
            if ($statement['completion_key'] !== $key) {
                throw new DomainException('This statement was already completed by another confirmation.');
            }
            return $statement;
        }
        if ($statement['status'] !== 'draft' || (int) $statement['revision'] !== $expectedRevision) {
            throw new DomainException('The matches changed after review. Review the current reconciliation before confirming.');
        }
        // Matches refer to immutable lines, but recheck all financial/date/scope relations at confirmation.
        $invalid = DB::queryFirstField('SELECT r.id FROM pl_bank_statement_rows r LEFT JOIN pl_bank_matches m ON m.row_id = r.id LEFT JOIN pl_journal_lines l ON l.id = m.journal_line_id AND l.company_id = r.company_id AND l.book_id = r.book_id AND l.account_id = r.account_id LEFT JOIN pl_journals j ON j.id = l.journal_id WHERE r.statement_id = %i AND (l.id IS NULL OR l.debit <> r.money_in OR l.credit <> r.money_out OR j.journal_date < %s OR j.journal_date > %s) LIMIT 1 FOR SHARE', $statementId, $statement['baseline_date'], $statement['end_date']);
        $summary = pl_bank_reconciliation_summary($actorId, $companyId, $bookId, $statementId);
        if ($invalid || !$summary['ready']) {
            throw new DomainException('Match every statement row and resolve the baseline or adjusted balance difference before confirming.');
        }
        DB::update('pl_bank_statements', ['status' => 'completed', 'revision' => $expectedRevision + 1, 'completed_by' => $actorId, 'completed_at' => gmdate('Y-m-d H:i:s'), 'completion_key' => $key, 'ledger_balance' => $summary['ledger_balance'], 'outstanding_balance' => $summary['outstanding_balance']], 'id = %i', $statementId);
        return pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
    });
}

function pl_bank_cancel_statement(int $actorId, int $companyId, int $bookId, int $statementId, int $expectedRevision, string $reason, string $key): array
{
    $reason = pl_ledger_text($reason, 'Cancellation reason', 400);
    $key = pl_request_key($key);
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $statementId, $expectedRevision, $reason, $key): array {
        pl_bank_require_write($actorId, $companyId, $bookId);
        $statement = pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
        if ($statement['status'] === 'cancelled') {
            if ($statement['cancellation_key'] !== $key || $statement['cancellation_reason'] !== $reason) {
                throw new DomainException('This statement was already cancelled with another request or reason.');
            }
            return $statement;
        }
        if ($statement['status'] !== 'draft' || (int) $statement['revision'] !== $expectedRevision) {
            throw new DomainException('Only the current unfinished statement can be cancelled. Review its latest state.');
        }
        if (DB::queryFirstField('SELECT m.row_id FROM pl_bank_matches m JOIN pl_bank_statement_rows r ON r.id = m.row_id WHERE r.statement_id = %i LIMIT 1 FOR UPDATE', $statementId)) {
            throw new DomainException('Remove all draft matches before cancelling this statement. Their audit history will be retained.');
        }
        DB::update('pl_bank_statements', ['status' => 'cancelled', 'revision' => $expectedRevision + 1, 'cancelled_by' => $actorId, 'cancelled_at' => gmdate('Y-m-d H:i:s'), 'cancellation_reason' => $reason, 'cancellation_key' => $key], 'id = %i', $statementId);
        DB::update('pl_bank_statement_rows', ['active_reference' => null], 'statement_id = %i', $statementId);
        return pl_bank_get_statement($actorId, $companyId, $bookId, $statementId);
    });
}

/** Posting calls this after replay detection while holding the book lock. */
function pl_reconciliation_assert_posting_allowed(int $companyId, int $bookId, array $payload): void
{
    $accounts = array_values(array_unique(array_column($payload['lines'], 'account_id')));
    if ($accounts !== [] && DB::queryFirstField('SELECT id FROM pl_bank_statements WHERE company_id = %i AND book_id = %i AND account_id IN %li AND status = %s AND end_date >= %s LIMIT 1 FOR SHARE', $companyId, $bookId, $accounts, 'completed', $payload['date'])) {
        throw new DomainException('A bank account in this entry is already reconciled through that date. Use an open later date for a traceable correction.');
    }
}
