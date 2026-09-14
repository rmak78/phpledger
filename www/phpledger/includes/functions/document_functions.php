<?php
declare(strict_types=1);

function pl_normalize_document(array $input): array
{
    $kind = $input['kind'] ?? null;
    if (!in_array($kind, ['receipt', 'expense'], true)) {
        throw new DomainException('Choose a receipt or an expense.');
    }
    if (!is_string($input['amount'] ?? null)) {
        throw new DomainException('Enter the amount as a decimal number.');
    }
    $amount = pl_amount($input['amount']);
    if (bccomp($amount, '0', 4) <= 0) {
        throw new DomainException('The amount must be greater than zero.');
    }
    foreach (['money_account_id', 'category_account_id'] as $field) {
        if (!is_int($input[$field] ?? null) || $input[$field] < 1) {
            throw new DomainException('Choose both a cash/bank account and a category.');
        }
    }
    return [
        'kind' => $kind,
        'document_date' => pl_ledger_date(pl_ledger_text($input['date'] ?? null, 'Transaction date', 10)),
        'amount' => $amount,
        'money_account_id' => $input['money_account_id'],
        'category_account_id' => $input['category_account_id'],
        'counterparty' => pl_ledger_text($input['counterparty'] ?? null, 'Paid to / received from', 160),
        'reference' => pl_ledger_text($input['reference'] ?? '', 'Reference', 120, false),
        'memo' => pl_ledger_text($input['memo'] ?? '', 'Memo', 500, false),
    ];
}

function pl_validate_document_accounts(int $companyId, int $bookId, array $document): void
{
    $money = DB::queryFirstRow('SELECT id FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i AND is_active = 1 AND type = %s AND role = %s FOR SHARE', $document['money_account_id'], $companyId, $bookId, 'asset', 'cash_bank');
    $type = $document['kind'] === 'receipt' ? 'income' : 'expense';
    $category = DB::queryFirstRow('SELECT id FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i AND is_active = 1 AND type = %s AND role = %s FOR SHARE', $document['category_account_id'], $companyId, $bookId, $type, $type);
    if (!$money || !$category || $document['money_account_id'] === $document['category_account_id']) {
        throw new DomainException('Choose an active cash/bank account and a matching income or expense category from this company.');
    }
}

function pl_document_number(int $id, string $kind): string
{
    return ($kind === 'receipt' ? 'REC-' : 'EXP-') . str_pad((string) $id, 6, '0', STR_PAD_LEFT);
}

function pl_document_row(array $row): array
{
    foreach (['id', 'company_id', 'book_id', 'money_account_id', 'category_account_id', 'revision'] as $field) {
        $row[$field] = (int) $row[$field];
    }
    $row['journal_id'] = $row['journal_id'] === null ? null : (int) $row['journal_id'];
    $row['reversal_journal_id'] = ($row['reversal_journal_id'] ?? null) === null ? null : (int) $row['reversal_journal_id'];
    $row['amount'] = bcadd((string) $row['amount'], '0', 4);
    $row['date'] = $row['document_date'];
    $row['number'] = pl_document_number($row['id'], $row['kind']);
    $row['status'] = $row['journal_id'] === null ? 'draft' : ($row['reversal_journal_id'] === null ? 'posted' : 'reversed');
    unset($row['creation_hash'], $row['creation_key']);
    return $row;
}

function pl_get_document(int $actorId, int $companyId, int $bookId, int $documentId): array
{
    pl_require_company_access($actorId, $companyId);
    $book = pl_ledger_book($companyId, $bookId);
    $row = DB::queryFirstRow('SELECT d.*, m.code AS money_account_code, m.name AS money_account_name, c.code AS category_account_code, c.name AS category_account_name, r.id AS reversal_journal_id FROM pl_documents d JOIN pl_accounts m ON m.id = d.money_account_id JOIN pl_accounts c ON c.id = d.category_account_id LEFT JOIN pl_journals r ON r.reversal_of_id = d.journal_id WHERE d.id = %i AND d.company_id = %i AND d.book_id = %i FOR SHARE', $documentId, $companyId, $bookId);
    if (!$row) {
        throw new DomainException('This transaction is not available in the selected company and book.');
    }
    $document = pl_document_row($row);
    $document['journal'] = $document['journal_id'] === null ? null : pl_get_journal($actorId, $companyId, $bookId, $document['journal_id']);
    $payload = pl_document_posting_payload($document, $book['currency']);
    foreach ($payload['lines'] as &$line) {
        $money = $line['account_id'] === $document['money_account_id'];
        $line['code'] = $document[$money ? 'money_account_code' : 'category_account_code'];
        $line['name'] = $document[$money ? 'money_account_name' : 'category_account_name'];
    }
    unset($line);
    $document['journal_preview'] = ['date' => $payload['date'], 'currency' => $payload['currency'], 'description' => $payload['description'], 'lines' => $payload['lines']];
    return $document;
}

function pl_save_document(int $actorId, int $companyId, int $bookId, array $input, ?int $documentId = null, ?int $expectedRevision = null): array
{
    $data = pl_normalize_document($input);
    $key = $documentId === null ? pl_request_key(pl_ledger_text($input['creation_key'] ?? null, 'Request identity', 128)) : null;
    $hash = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $data, $key, $hash, $documentId, $expectedRevision): array {
        pl_require_company_access($actorId, $companyId, true);
        pl_ledger_book($companyId, $bookId, true);
        pl_require_book_ready($companyId);
        pl_validate_document_accounts($companyId, $bookId, $data);
        if ($documentId === null) {
            $existing = DB::queryFirstRow('SELECT id, creation_hash FROM pl_documents WHERE company_id = %i AND book_id = %i AND creation_key = %s FOR UPDATE', $companyId, $bookId, $key);
            if ($existing) {
                if (!hash_equals((string) $existing['creation_hash'], $hash)) {
                    throw new DomainException('This form already created a different draft. Open the saved draft or start a new transaction.');
                }
                return pl_get_document($actorId, $companyId, $bookId, (int) $existing['id']);
            }
            pl_demo_require_document_capacity($companyId, $bookId);
            DB::insert('pl_documents', $data + [
                'company_id' => $companyId, 'book_id' => $bookId, 'creation_key' => $key, 'creation_hash' => $hash,
                'created_by' => $actorId, 'updated_by' => $actorId,
            ]);
            $id = (int) DB::insertId();
        } else {
            $existing = DB::queryFirstRow('SELECT id, revision, kind, journal_id FROM pl_documents WHERE id = %i AND company_id = %i AND book_id = %i FOR UPDATE', $documentId, $companyId, $bookId);
            if (!$existing) {
                throw new DomainException('This transaction is not available in the selected company and book.');
            }
            if ($existing['journal_id'] !== null) {
                throw new DomainException('Posted transactions cannot be edited. Use a linked reversal.');
            }
            if ($expectedRevision === null || $expectedRevision !== (int) $existing['revision']) {
                throw new DomainException('Someone changed this draft. Your entered values are preserved; compare them with the latest saved version before saving again.');
            }
            if ($data['kind'] !== $existing['kind']) {
                throw new DomainException('A saved receipt cannot be changed into an expense, or vice versa. Start a new transaction.');
            }
            DB::update('pl_documents', $data + ['revision' => $expectedRevision + 1, 'updated_by' => $actorId, 'updated_at' => gmdate('Y-m-d H:i:s')], 'id = %i', $documentId);
            $id = $documentId;
        }
        return pl_get_document($actorId, $companyId, $bookId, $id);
    });
}

/** The browser never supplies financial journal lines or a posting identity. */
function pl_document_posting_payload(array $document, string $currency): array
{
    $money = ['account_id' => (int) $document['money_account_id'], 'debit' => '0.0000', 'credit' => '0.0000', 'description' => (string) $document['counterparty']];
    $category = ['account_id' => (int) $document['category_account_id'], 'debit' => '0.0000', 'credit' => '0.0000', 'description' => (string) $document['memo']];
    $money[$document['kind'] === 'receipt' ? 'debit' : 'credit'] = (string) $document['amount'];
    $category[$document['kind'] === 'receipt' ? 'credit' : 'debit'] = (string) $document['amount'];
    return [
        'date' => $document['document_date'], 'currency' => $currency,
        'source_type' => $document['kind'], 'source_reference' => 'document:' . $document['id'],
        'idempotency_key' => 'document:' . $document['id'] . ':post',
        'description' => pl_document_number((int) $document['id'], $document['kind']) . ' — ' . $document['counterparty'],
        'lines' => $document['kind'] === 'receipt' ? [$money, $category] : [$category, $money],
    ];
}

function pl_post_document(int $actorId, int $companyId, int $bookId, int $documentId, int $expectedRevision): array
{
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $documentId, $expectedRevision): array {
        pl_require_company_access($actorId, $companyId, true);
        $book = pl_ledger_book($companyId, $bookId, true);
        pl_require_book_ready($companyId);
        $document = DB::queryFirstRow('SELECT * FROM pl_documents WHERE id = %i AND company_id = %i AND book_id = %i FOR UPDATE', $documentId, $companyId, $bookId);
        if (!$document) {
            throw new DomainException('This transaction is not available in the selected company and book.');
        }
        if ($document['journal_id'] !== null) {
            return pl_get_document($actorId, $companyId, $bookId, $documentId);
        }
        if ((int) $document['revision'] !== $expectedRevision) {
            throw new DomainException('This draft changed after you reviewed it. Review the latest saved values before posting.');
        }
        pl_validate_document_accounts($companyId, $bookId, $document);
        $journal = pl_post_journal($actorId, $companyId, $bookId, pl_document_posting_payload($document, $book['currency']));
        DB::update('pl_documents', ['journal_id' => $journal['id'], 'updated_by' => $actorId, 'updated_at' => gmdate('Y-m-d H:i:s')], 'id = %i', $documentId);
        return pl_get_document($actorId, $companyId, $bookId, $documentId);
    });
}

function pl_reverse_document(int $actorId, int $companyId, int $bookId, int $documentId, string $date, string $reason): array
{
    return pl_ledger_transaction(function () use ($actorId, $companyId, $bookId, $documentId, $date, $reason): array {
        pl_require_company_access($actorId, $companyId, true);
        pl_ledger_book($companyId, $bookId, true);
        pl_require_book_ready($companyId);
        $document = pl_get_document($actorId, $companyId, $bookId, $documentId);
        if ($document['journal_id'] === null) {
            throw new DomainException('Only a posted transaction can be reversed.');
        }
        pl_reverse_journal($actorId, $companyId, $bookId, $document['journal_id'], $date, 'document:' . $documentId . ':reverse', $reason);
        return pl_get_document($actorId, $companyId, $bookId, $documentId);
    });
}

function pl_list_documents(int $actorId, int $companyId, int $bookId, array $filters = []): array
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    $where = ['d.company_id = %i', 'd.book_id = %i'];
    $args = [$companyId, $bookId];
    $status = $filters['status'] ?? 'all';
    if (!in_array($status, ['all', 'draft', 'posted', 'reversed'], true)) {
        throw new DomainException('Choose a valid transaction status.');
    }
    if ($status === 'draft') {
        $where[] = 'd.journal_id IS NULL';
    } elseif ($status === 'posted') {
        $where[] = 'd.journal_id IS NOT NULL AND r.id IS NULL';
    } elseif ($status === 'reversed') {
        $where[] = 'r.id IS NOT NULL';
    }
    $kind = $filters['kind'] ?? 'all';
    if (!in_array($kind, ['all', 'receipt', 'expense'], true)) {
        throw new DomainException('Choose receipts, expenses, or all transactions.');
    }
    if ($kind !== 'all') {
        $where[] = 'd.kind = %s';
        $args[] = $kind;
    }
    foreach (['from' => '>=', 'to' => '<='] as $key => $operator) {
        if (($filters[$key] ?? '') !== '') {
            $where[] = 'd.document_date ' . $operator . ' %s';
            $args[] = pl_ledger_date(pl_ledger_text($filters[$key], 'Filter date', 10));
        }
    }
    if (($filters['from'] ?? '') !== '' && ($filters['to'] ?? '') !== '' && $filters['from'] > $filters['to']) {
        throw new DomainException('The beginning of the date range must be on or before its end.');
    }
    $search = pl_ledger_text($filters['search'] ?? '', 'Search', 160, false);
    if ($search !== '') {
        // Bound literal substring comparison does not treat percent/underscore as wildcards.
        $where[] = '(LOCATE(%s, d.counterparty) > 0 OR LOCATE(%s, d.reference) > 0 OR LOCATE(%s, d.memo) > 0 OR LOCATE(%s, CONCAT(IF(d.kind = \'receipt\', \'REC-\', \'EXP-\'), LPAD(d.id, 6, \'0\'))) > 0)';
        array_push($args, $search, $search, $search, strtoupper($search));
    }
    $condition = implode(' AND ', $where);
    $join = ' FROM pl_documents d LEFT JOIN pl_journals r ON r.reversal_of_id = d.journal_id WHERE ' . $condition;
    $totals = DB::queryFirstRow('SELECT COUNT(*) AS total, COALESCE(SUM(d.amount), 0) AS total_amount' . $join, ...$args);
    $total = (int) $totals['total'];
    $pages = max(1, (int) ceil($total / 50));
    $page = min($pages, max(1, (int) ($filters['page'] ?? 1)));
    $rows = DB::query('SELECT d.*, r.id AS reversal_journal_id' . $join . ' ORDER BY d.document_date DESC, d.id ASC LIMIT 50 OFFSET %i', ...array_merge($args, [($page - 1) * 50]));
    return ['documents' => array_map('pl_document_row', $rows), 'total' => $total, 'total_amount' => bcadd((string) $totals['total_amount'], '0', 4), 'page' => $page, 'pages' => $pages];
}

function pl_account_activity(int $actorId, int $companyId, int $bookId, int $accountId, ?string $asOf = null, int $page = 1, ?string $from = null): array
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    $account = DB::queryFirstRow('SELECT id, code, name, type FROM pl_accounts WHERE id = %i AND company_id = %i AND book_id = %i', $accountId, $companyId, $bookId);
    if (!$account) {
        throw new DomainException('This account is not available in the selected company and book.');
    }
    $account['id'] = (int) $account['id'];
    $date = $asOf === null ? '9999-12-31' : pl_ledger_date($asOf);
    $args = [$accountId, $companyId, $bookId, $date];
    $queryFrom = ' FROM pl_journal_lines l JOIN pl_journals j ON j.id = l.journal_id WHERE l.account_id = %i AND l.company_id = %i AND l.book_id = %i AND j.journal_date <= %s';
    if ($from !== null) {
        pl_ledger_date($from);
        if ($from > $date) {
            throw new DomainException('The activity start date must be on or before its end date.');
        }
        $queryFrom .= ' AND j.journal_date >= %s';
        $args[] = $from;
    }
    $totals = DB::queryFirstRow('SELECT COUNT(*) AS total, COALESCE(SUM(l.debit), 0) AS debit_movement, COALESCE(SUM(l.credit), 0) AS credit_movement' . $queryFrom, ...$args);
    $pages = max(1, (int) ceil((int) $totals['total'] / 50));
    $page = min($pages, max(1, $page));
    $rows = DB::query('SELECT j.id AS journal_id, j.journal_date AS date, j.description, j.source_type, j.source_reference, j.reversal_of_id, l.debit, l.credit' . $queryFrom . ' ORDER BY j.journal_date, j.id, l.line_number LIMIT 50 OFFSET %i', ...array_merge($args, [($page - 1) * 50]));
    foreach ($rows as &$row) {
        $row['journal_id'] = (int) $row['journal_id'];
        $row['journal_reference'] = 'PL-' . str_pad((string) $row['journal_id'], 8, '0', STR_PAD_LEFT);
        $source = DB::queryFirstField('SELECT id FROM pl_documents WHERE company_id = %i AND book_id = %i AND journal_id = %i', $companyId, $bookId, $row['reversal_of_id'] === null ? $row['journal_id'] : (int) $row['reversal_of_id']);
        $row['document_id'] = $source === null ? null : (int) $source;
        $row['debit'] = bcadd((string) $row['debit'], '0', 4);
        $row['credit'] = bcadd((string) $row['credit'], '0', 4);
    }
    unset($row);
    $debit = bcadd((string) $totals['debit_movement'], '0', 4);
    $credit = bcadd((string) $totals['credit_movement'], '0', 4);
    return ['account' => $account, 'movements' => $rows, 'debit_movement' => $debit, 'credit_movement' => $credit, 'balance' => bcsub($debit, $credit, 4), 'total' => (int) $totals['total'], 'page' => $page, 'pages' => $pages, 'from' => $from, 'as_of' => $asOf];
}
