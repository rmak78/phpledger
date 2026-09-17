<?php
declare(strict_types=1);

function pl_web_bank_csv(array $post, array $files): string
{
    $csv = is_string($post['csv'] ?? null) ? $post['csv'] : '';
    $file = $files['statement_csv'] ?? null;
    if (is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        if ($csv !== '') {
            throw new DomainException('Paste CSV or upload a CSV file, using only one source.');
        }
        if (($file['error'] ?? -1) !== UPLOAD_ERR_OK || !is_string($file['tmp_name'] ?? null)
            || !is_string($file['name'] ?? null) || strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv'
            || (int) ($file['size'] ?? 524289) > 524288 || !is_uploaded_file($file['tmp_name'])) {
            throw new DomainException('Upload a UTF-8 .csv file no larger than 512 KiB.');
        }
        $csv = file_get_contents($file['tmp_name'], false, null, 0, 524289);
        if ($csv === false) {
            throw new DomainException('The CSV could not be read. Choose the file again.');
        }
    }
    if (strlen($csv) > 524288) {
        throw new DomainException('Use a CSV no larger than 512 KiB.');
    }
    return $csv;
}

function pl_web_reconciliation(int $actorId, int $companyId, int $bookId, array $user, array $company, string $method): never
{
    $sessionKey = $actorId . ':' . $companyId . ':' . $bookId;
    $statementId = pl_web_id($method==='POST'?$_POST:$_GET, $method==='POST'?'statement_id':'id');
    $listFilters = ($method==='POST'?pl_return_list_filters($_POST,'bank'):pl_list_filters($_GET,'bank')) + ['id'=>$statementId];
    $return = $statementId > 0 ? pl_url('/bank-reconciliation',$listFilters) : '/bank-reconciliation';
    if ($method === 'POST') {
        pl_require_csrf(pl_web_text($_POST, 'csrf'));
        $preserved = $_POST;
        try {
            pl_web_assert_scope($company, $_POST);
            $action = pl_web_text($_POST, 'action');
            if ($action === 'preview') {
                $preserved['csv'] = pl_web_bank_csv($_POST, $_FILES);
                $data = [
                    'account_id' => pl_web_id($_POST, 'account_id'),
                    'reference' => pl_web_text($_POST, 'reference'),
                    'start_date' => pl_web_text($_POST, 'start_date'),
                    'end_date' => pl_web_text($_POST, 'end_date'),
                    'opening_balance' => pl_web_text($_POST, 'opening_balance'),
                    'closing_balance' => pl_web_text($_POST, 'closing_balance'),
                    'baseline_confirmed' => pl_web_text($_POST, 'baseline_confirmed') === '1',
                    'rows' => pl_bank_parse_csv($preserved['csv']),
                ];
                $preview = pl_bank_preview_statement($actorId, $companyId, $bookId, $data);
                $_SESSION['bank_preview'] = ['scope' => $sessionKey, 'preview' => $preview, 'key' => bin2hex(random_bytes(24)), 'input' => $preserved];
                pl_redirect('/bank-reconciliation?preview=1');
            }
            if ($action === 'import') {
                $saved = $_SESSION['bank_preview'] ?? null;
                if (!is_array($saved) || ($saved['scope'] ?? '') !== $sessionKey || !hash_equals($saved['key'], pl_web_text($_POST, 'preview_key'))) {
                    throw new DomainException('This preview is no longer current. Preview the intended statement again.');
                }
                $statement = pl_bank_import_statement($actorId, $companyId, $bookId, $saved['preview']['data'], $saved['key'], $saved['preview']['digest']);
                pl_notice('Statement imported. Review and explicitly match each bank transaction.');
                pl_redirect('/bank-reconciliation?id=' . $statement['id']);
            }
            if ($action === 'match' || $action === 'unmatch') {
                pl_bank_match_row($actorId, $companyId, $bookId, $statementId, pl_web_id($_POST, 'row_id'), $action === 'match' ? pl_web_id($_POST, 'line_id') : null, pl_web_id($_POST, 'revision'));
                pl_notice($action === 'match' ? 'Match saved.' : 'Match removed; its history is retained.');
                pl_redirect($return);
            }
            if ($action === 'complete') {
                if (pl_web_text($_POST, 'review_confirmed') !== '1') {
                    throw new DomainException('Confirm that you reviewed the matches and outstanding entries.');
                }
                pl_bank_complete_statement($actorId, $companyId, $bookId, $statementId, pl_web_id($_POST, 'revision'), pl_web_text($_POST, 'completion_key'));
                pl_notice('Reconciliation completed and retained. This bank account rejects backdated entries through the statement end.');
                pl_redirect($return);
            }
            if ($action === 'cancel') {
                pl_bank_cancel_statement($actorId, $companyId, $bookId, $statementId, pl_web_id($_POST, 'revision'), pl_web_text($_POST, 'reason'), pl_web_text($_POST, 'cancellation_key'));
                pl_notice('Draft statement cancelled. Its original content and match history are retained; a corrected statement can reuse its bank references.');
                pl_redirect($return);
            }
            throw new DomainException('Choose a bank reconciliation action.');
        } catch (DomainException $error) {
            // Bound retained input even for deliberately oversized invalid submissions.
            if (is_string($preserved['csv'] ?? null)) {
                $preserved['csv'] = substr($preserved['csv'], 0, 524288);
            }
            pl_form_failure($return, $preserved, $error->getMessage());
        }
    }
    pl_require_company_access($actorId, $companyId);
    pl_ledger_book($companyId, $bookId);
    $form = pl_form_state($return);
    $saved = $_SESSION['bank_preview'] ?? null;
    $preview = pl_web_text($_GET, 'preview') === '1' && is_array($saved) && ($saved['scope'] ?? '') === $sessionKey ? $saved : null;
    $input = $form['input'] ?: ($preview['input'] ?? []);
    $accounts = DB::query('SELECT id, code, name FROM pl_accounts WHERE company_id = %i AND book_id = %i AND role = %s AND type = %s AND is_active = 1 ORDER BY code', $companyId, $bookId, 'cash_bank', 'asset');
    $page = min(1000000, max(1, pl_web_id($_GET, 'page', 1)));
    $statements = DB::query('SELECT s.id, s.reference, s.start_date, s.end_date, s.closing_balance, s.status, a.name AS account_name FROM pl_bank_statements s JOIN pl_accounts a ON a.id = s.account_id WHERE s.company_id = %i AND s.book_id = %i ORDER BY s.id DESC LIMIT 51 OFFSET %i', $companyId, $bookId, ($page - 1) * 50);
    $hasMore = count($statements) > 50;
    $statements = array_slice($statements, 0, 50);
    $statement = $statementId > 0 ? pl_list_query($actorId, $companyId, $bookId, 'bank', array_replace($_GET, ['statement_id' => $statementId])) : null;
    $summary = $statement ? pl_bank_reconciliation_summary($actorId, $companyId, $bookId, $statementId) : null;
    $selectedRowId = pl_web_id($_GET, 'row');
    $candidatePage = min(1000000, max(1, pl_web_id($_GET, 'candidate_page', 1)));
    $outstandingPage = min(1000000, max(1, pl_web_id($_GET, 'outstanding_page', 1)));
    $candidates = $statement && $selectedRowId > 0 ? pl_bank_candidates($actorId, $companyId, $bookId, $statementId, $selectedRowId, $candidatePage) : [];
    $outstandingLines = $statement ? pl_bank_outstanding_lines($actorId, $companyId, $bookId, $statementId, $outstandingPage) : [];
    $selectedRow = null;
    foreach ($statement['rows'] ?? [] as $row) {
        if ((int) $row['id'] === $selectedRowId) {
            $selectedRow = $row;
        }
    }
    pl_render('bank-reconciliation', ['title' => 'Bank reconciliation', 'user' => $user, 'company' => $company, 'form' => $form, 'input' => $input, 'preview' => $preview, 'accounts' => $accounts, 'statements' => $statements, 'statement' => $statement, 'summary' => $summary, 'selectedRow' => $selectedRow, 'candidates' => $candidates, 'candidatePage' => $candidatePage, 'outstandingPage' => $outstandingPage, 'outstandingLines' => $outstandingLines, 'page' => $page, 'hasMore' => $hasMore, 'filters' => $listFilters]);
}
