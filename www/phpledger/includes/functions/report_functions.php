<?php
declare(strict_types=1);

/** Home is a read-only composition of the same reports and source lists. */
function pl_home_overview(int $actorId, int $companyId, int $bookId, string $asOf): array
{
    pl_require_company_access($actorId, $companyId);
    $book = pl_ledger_book($companyId, $bookId);
    $asOf = pl_ledger_date($asOf);
    $receivables = pl_ar_ap_open_items($actorId, $companyId, $bookId, 'receivable', $asOf);
    $payables = pl_ar_ap_open_items($actorId, $companyId, $bookId, 'payable', $asOf);
    $dueThrough = (new DateTimeImmutable($asOf))->modify('+3 days')->format('Y-m-d');
    $drafts = pl_list_documents($actorId, $companyId, $bookId, ['status' => 'draft', 'page_size' => 25]);
    $journals = pl_list_general_drafts($actorId, $companyId, $bookId, 1, ['status' => 'draft', 'page_size' => 25]);
    $recent = pl_list_documents($actorId, $companyId, $bookId, ['page_size' => 25]);
    $activity = [];
    foreach ($recent['documents'] as $row) {
        $activity[] = ['id'=>$row['id'], 'date'=>$row['date'], 'number'=>$row['number'], 'description'=>$row['counterparty'], 'kind'=>ucfirst($row['kind']), 'amount'=>$row['amount'], 'currency'=>$book['currency'], 'status'=>$row['status'], 'path'=>'/transactions/detail'];
    }
    foreach (pl_list_general_drafts($actorId, $companyId, $bookId, 1, ['page_size'=>25])['rows'] as $row) {
        $activity[] = ['id'=>$row['id'], 'date'=>$row['document_date'], 'number'=>$row['number'], 'description'=>$row['description'], 'kind'=>'Journal', 'amount'=>$row['totals']['debit'], 'currency'=>$book['currency'], 'status'=>$row['status'], 'path'=>'/general-journals/detail'];
    }
    $arDrafts = 0; $apDrafts = 0;
    foreach (pl_list_ar_documents($actorId, $companyId, $bookId)['documents'] as $row) {
        $sales = in_array($row['kind'], ['invoice','customer_credit'], true);
        if ($row['status'] === 'draft') { if ($sales) { $arDrafts++; } else { $apDrafts++; } }
        $activity[] = ['id'=>$row['id'], 'date'=>$row['date'], 'number'=>$row['number'], 'description'=>$row['party']['legal_name'], 'kind'=>ucfirst(str_replace('_',' ',$row['kind'])), 'amount'=>$row['total'], 'currency'=>$row['currency'], 'status'=>$row['reversal_journal_id'] !== null ? 'reversed' : $row['status'], 'path'=>$sales ? '/ar' : '/ap'];
    }
    usort($activity, static fn (array $a, array $b): int => [$b['date'],$b['number']] <=> [$a['date'],$a['number']]);
    return [
        'as_of' => $asOf, 'currency' => $book['currency'],
        'cash' => pl_cash_balance($actorId, $companyId, $bookId, $asOf),
        'drafts' => $drafts, 'journal_drafts' => $journals['total'], 'ar_drafts'=>$arDrafts, 'ap_drafts'=>$apDrafts,
        'receivables' => $receivables, 'payables' => $payables,
        'overdue_invoices' => array_values(array_filter($receivables['items'], static fn (array $item): bool => $item['age_days'] > 0)),
        'bills_due' => array_values(array_filter($payables['items'], static fn (array $item): bool => $item['due_date'] !== null && $item['due_date'] <= $dueThrough)),
        'bank_lines' => pl_bank_pending_review_count($actorId, $companyId, $bookId),
        'recent' => array_slice($activity, 0, 5),
    ];
}

/** Posted income and expense movements for an inclusive business-date range. */
function pl_profit_loss(int $actorId, int $companyId, int $bookId, string $from, string $to): array
{
    pl_require_company_access($actorId, $companyId);
    $book = pl_ledger_book($companyId, $bookId);
    pl_ledger_date($from);
    pl_ledger_date($to);
    if ($from > $to) {
        throw new DomainException('The report start date must be on or before its end date.');
    }
    $rows = DB::query("SELECT a.id, a.code, a.name, a.type,
        COALESCE(SUM(CASE WHEN j.id IS NOT NULL THEN l.debit ELSE 0 END), 0) AS debit,
        COALESCE(SUM(CASE WHEN j.id IS NOT NULL THEN l.credit ELSE 0 END), 0) AS credit
        FROM pl_accounts a
        LEFT JOIN pl_journal_lines l ON l.account_id = a.id AND l.company_id = a.company_id AND l.book_id = a.book_id
        LEFT JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = a.company_id AND j.book_id = a.book_id AND j.journal_date >= %s AND j.journal_date <= %s
        WHERE a.company_id = %i AND a.book_id = %i AND a.type IN ('income','expense')
        GROUP BY a.id, a.code, a.name, a.type ORDER BY a.code", $from, $to, $companyId, $bookId);
    $income = [];
    $expenses = [];
    $totalIncome = '0.0000';
    $totalExpenses = '0.0000';
    foreach ($rows as $row) {
        $amount = $row['type'] === 'income' ? bcsub((string) $row['credit'], (string) $row['debit'], 4) : bcsub((string) $row['debit'], (string) $row['credit'], 4);
        $entry = ['id' => (int) $row['id'], 'code' => $row['code'], 'name' => $row['name'], 'amount' => $amount];
        if ($row['type'] === 'income') {
            $income[] = $entry;
            $totalIncome = bcadd($totalIncome, $amount, 4);
        } else {
            $expenses[] = $entry;
            $totalExpenses = bcadd($totalExpenses, $amount, 4);
        }
    }
    return ['from' => $from, 'to' => $to, 'currency' => $book['currency'], 'income' => $income, 'expenses' => $expenses,
        'total_income' => $totalIncome, 'total_expenses' => $totalExpenses, 'net_profit' => bcsub($totalIncome, $totalExpenses, 4)];
}

/** Chart-classified balances; accumulated unclosed earnings appear once beside recorded equity. */
function pl_balance_sheet(int $actorId, int $companyId, int $bookId, string $asOf): array
{
    pl_require_company_access($actorId, $companyId);
    $book = pl_ledger_book($companyId, $bookId);
    pl_ledger_date($asOf);
    $trial = pl_trial_balance($actorId, $companyId, $bookId, $asOf);
    $groups = ['assets' => [], 'liabilities' => [], 'equity' => []];
    $totals = ['assets' => '0.0000', 'liabilities' => '0.0000', 'equity' => '0.0000'];
    $earnedProfit = '0.0000';
    foreach ($trial['accounts'] as $account) {
        if (in_array($account['type'], ['income', 'expense'], true)) {
            $earnedProfit = bcsub($earnedProfit, $account['balance'], 4);
            continue;
        }
        $group = match ($account['type']) {
            'asset' => 'assets', 'liability' => 'liabilities', 'equity' => 'equity',
            default => throw new DomainException('An account has an unsupported report classification.'),
        };
        $amount = $group === 'assets' ? $account['balance'] : bcsub('0', $account['balance'], 4);
        $groups[$group][] = ['id' => $account['id'], 'code' => $account['code'], 'name' => $account['name'], 'amount' => $amount];
        $totals[$group] = bcadd($totals[$group], $amount, 4);
    }
    $totalEquity = bcadd($totals['equity'], $earnedProfit, 4);
    $liabilitiesEquity = bcadd($totals['liabilities'], $totalEquity, 4);
    return ['as_of' => $asOf, 'currency' => $book['currency']] + $groups + [
        'total_assets' => $totals['assets'], 'total_liabilities' => $totals['liabilities'],
        'recorded_equity' => $totals['equity'], 'earned_profit' => $earnedProfit, 'total_equity' => $totalEquity,
        'total_liabilities_equity' => $liabilitiesEquity, 'balanced' => $trial['balanced'] && bccomp($totals['assets'], $liabilitiesEquity, 4) === 0,
    ];
}

function pl_cash_balance(int $actorId, int $companyId, int $bookId, string $asOf): string
{
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    pl_ledger_date($asOf);
    $row = DB::queryFirstRow("SELECT COALESCE(SUM(CASE WHEN j.id IS NOT NULL THEN l.debit ELSE 0 END), 0) AS debit,
        COALESCE(SUM(CASE WHEN j.id IS NOT NULL THEN l.credit ELSE 0 END), 0) AS credit
        FROM pl_accounts a LEFT JOIN pl_journal_lines l ON l.account_id = a.id AND l.company_id = a.company_id AND l.book_id = a.book_id
        LEFT JOIN pl_journals j ON j.id = l.journal_id AND j.company_id = a.company_id AND j.book_id = a.book_id AND j.journal_date <= %s
        WHERE a.company_id = %i AND a.book_id = %i AND a.type = 'asset' AND a.role = 'cash_bank'", $asOf, $companyId, $bookId);
    return bcsub((string) $row['debit'], (string) $row['credit'], 4);
}

/** An explicit scenario, never an inferred forecast or a ledger write. */
function pl_cash_forecast(string $opening, string $weeklyIn, string $weeklyOut, int $weeks = 12): array
{
    $negative = str_starts_with($opening, '-');
    $amount = pl_amount($negative ? substr($opening, 1) : $opening);
    $opening = $negative ? bcsub('0', $amount, 4) : $amount;
    $inflow = pl_amount($weeklyIn);
    $outflow = pl_amount($weeklyOut);
    if ($weeks < 1 || $weeks > 52) {
        throw new DomainException('Choose a cash scenario between one and 52 weeks.');
    }
    $balance = $opening;
    $firstNegative = bccomp($opening, '0', 4) < 0 ? 0 : null;
    $rows = [];
    for ($week = 1; $week <= $weeks; $week++) {
        $closing = bcsub(bcadd($balance, $inflow, 4), $outflow, 4);
        $rows[] = ['week' => $week, 'opening' => $balance, 'inflow' => $inflow, 'outflow' => $outflow, 'closing' => $closing];
        if ($firstNegative === null && bccomp($closing, '0', 4) < 0) {
            $firstNegative = $week;
        }
        $balance = $closing;
    }
    return ['opening' => $opening, 'weekly_in' => $inflow, 'weekly_out' => $outflow, 'weeks' => $weeks, 'rows' => $rows, 'closing' => $balance, 'first_negative_week' => $firstNegative];
}
