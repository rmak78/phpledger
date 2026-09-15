<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/functions/web_functions.php';

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$basePath = pl_base_path();
if ($basePath !== '' && ($path === $basePath || str_starts_with($path, $basePath . '/'))) {
    $path = substr($path, strlen($basePath)) ?: '/';
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($path === '/health') {
    header('Content-Type: application/json; charset=utf-8');
    header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'");
    if ($method !== 'GET') {
        http_response_code(405);
        header('Allow: GET');
        echo json_encode(['error' => 'Method not allowed.']);
        exit;
    }
    try {
        require_once dirname(__DIR__) . '/includes/bootstrap.php';
        DB::queryFirstField('SELECT 1');
        echo json_encode(['status' => 'ok', 'stage' => 'working-accounting-preview']);
    } catch (Throwable $error) {
        http_response_code(503);
        error_log('PHP Ledger health dependency unavailable (' . get_class($error) . ').');
        echo json_encode(['status' => 'unavailable']);
    }
    exit;
}

header('Content-Type: text/html; charset=utf-8');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'; object-src 'none'");
$routes = [
    '/' => ['GET'], '/login' => ['GET', 'POST'], '/logout' => ['POST'], '/start' => ['POST'],
    '/companies' => ['GET'], '/company/select' => ['POST'], '/onboarding' => ['GET', 'POST'],
    '/setup/review' => ['GET', 'POST'], '/transactions' => ['GET'], '/transactions/detail' => ['GET'],
    '/opening-balances' => ['GET', 'POST'], '/periods' => ['GET', 'POST'], '/bank-reconciliation' => ['GET', 'POST'],
    '/transactions/new' => ['GET'], '/transactions/edit' => ['GET'], '/transactions/save' => ['POST'],
    '/transactions/post' => ['POST'], '/transactions/reverse' => ['POST'],
    '/reports/trial-balance' => ['GET'], '/reports/account' => ['GET'], '/journals/detail' => ['GET'],
    '/reports' => ['GET'], '/reports/balance-sheet' => ['GET'], '/reports/profit-loss' => ['GET'], '/reports/cash-forecast' => ['GET', 'POST'],
    '/pos' => ['GET'], '/pos/checkout' => ['POST'], '/pos/receipt' => ['GET'],
    '/help' => ['GET'],
];
if (!isset($routes[$path]) || !in_array($method, $routes[$path], true)) {
    http_response_code(isset($routes[$path]) ? 405 : 404);
    if (isset($routes[$path])) {
        header('Allow: ' . implode(', ', $routes[$path]));
    }
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>PHP Ledger</title><link rel="stylesheet" href="' . htmlspecialchars(pl_url('/assets/app.css'), ENT_QUOTES, 'UTF-8') . '"><main class="standalone"><h1>'
        . (isset($routes[$path]) ? 'That action needs a different request.' : 'Page not found.')
        . '</h1><p><a href="' . htmlspecialchars(pl_url('/'), ENT_QUOTES, 'UTF-8') . '">Return to PHP Ledger</a></p></main></html>';
    exit;
}

try {
    // Resolve the once-per-session country hint before acquiring the demo database lock.
    require_once dirname(__DIR__) . '/includes/functions/demo_functions.php';
    require_once dirname(__DIR__) . '/includes/functions/security_functions.php';
    require_once dirname(__DIR__) . '/includes/functions/regional_functions.php';
    $localDemoHttp = pl_web_local_demo_http($_SERVER);
    pl_session_start(!in_array(getenv('PL_ENV') ?: 'production', ['local', 'test'], true) && !$localDemoHttp);
    pl_regional_suggestion();
    require_once dirname(__DIR__) . '/includes/bootstrap.php';
    $actorId = pl_current_user_id();
    $user = $actorId ? DB::queryFirstRow('SELECT id, display_name, email FROM pl_users WHERE id = %i', $actorId) : null;
    if ($path === '/') {
        pl_redirect($actorId ? (pl_demo_enabled() ? '/transactions' : '/companies') : '/login');
    }
    if ($method === 'POST') {
        pl_require_post();
        pl_require_csrf(pl_web_text($_POST, 'csrf'));
    }
    if (pl_demo_enabled() && in_array($path, ['/onboarding', '/setup/review', '/company/select'], true)) {
        throw new DomainException('Business setup and administration are disabled in the public sample.');
    }
    if ($path === '/start') {
        $visit = pl_demo_begin_visit(pl_web_text($_POST, 'csrf'), pl_web_text($_POST, 'currency', 'USD'));
        $_SESSION['company_id'] = $visit['company_id'];
        pl_redirect('/reports');
    }
    if ($path === '/login') {
        if ($actorId) {
            pl_redirect(pl_demo_enabled() ? '/transactions' : '/companies');
        }
        if (pl_demo_enabled()) {
            if ($method !== 'GET') {
                throw new DomainException('Use Start my sample to enter the public demo.');
            }
            pl_render('demo', ['title' => 'Try PHP Ledger']);
        }
        if ($method === 'POST') {
            $authenticated = pl_authenticate(pl_web_text($_POST, 'email'), is_string($_POST['password'] ?? null) ? $_POST['password'] : '', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
            if (!$authenticated) {
                pl_form_failure('/login', ['email' => pl_web_text($_POST, 'email')], 'We could not sign you in. Check your details, or wait a few minutes before trying again.');
            }
            pl_login_session($authenticated);
            pl_redirect('/companies');
        }
        pl_render('login', ['title' => 'Sign in', 'form' => pl_form_state('/login')]);
    }
    if (!$actorId) {
        pl_notice('Please sign in to continue. Your session may have expired.');
        pl_redirect('/login');
    }
    if ($path === '/logout') {
        pl_logout_session();
        pl_redirect('/login');
    }
    if ($path === '/company/select') {
        $selected = pl_company_context($actorId, pl_web_id($_POST, 'company_id'));
        $_SESSION['company_id'] = (int) $selected['id'];
        pl_redirect('/transactions');
    }
    if ($path === '/companies') {
        if (pl_demo_enabled()) {
            pl_redirect('/transactions');
        }
        pl_render('companies', ['title' => 'Your businesses', 'user' => $user, 'companies' => pl_list_companies($actorId)]);
    }
    if ($path === '/onboarding') {
        $template = pl_starter_template();
        if ($method === 'POST') {
            $action = pl_web_text($_POST, 'action');
            if ($action === 'preview') {
                $input = [
                    'name' => pl_web_text($_POST, 'name'), 'currency' => pl_web_text($_POST, 'currency'),
                    'start_date' => pl_web_text($_POST, 'start_date'), 'fiscal_year_end' => pl_web_text($_POST, 'fiscal_year_end'),
                    'start_mode' => pl_web_text($_POST, 'start_mode', 'fresh'),
                    'template_digest' => (string) $template['digest'],
                    'zero_balances_confirmed' => pl_web_text($_POST, 'zero_balances_confirmed') === '1',
                ];
                try {
                    if (!in_array($input['start_mode'], ['fresh', 'existing', 'sample'], true)) {
                        throw new DomainException('Choose how you want to start.');
                    }
                    if ($input['start_mode'] === 'sample') {
                        $input['name'] = 'Core accounting sample';
                        $input['zero_balances_confirmed'] = true;
                    }
                    pl_ledger_text($input['name'], 'Business name', 160);
                    pl_ledger_date($input['start_date']);
                    if (!isset(pl_base_currency_options()[$input['currency']])) {
                        throw new DomainException('Choose one of the supported base currencies.');
                    }
                    pl_ledger_date('2001-' . $input['fiscal_year_end']);
                    if ($input['start_mode'] === 'fresh' && !$input['zero_balances_confirmed']) {
                        throw new DomainException('Confirm that this business starts with no prior balances, or choose Bring past records.');
                    }
                    $_SESSION['onboarding'] = ['input' => $input, 'request_key' => bin2hex(random_bytes(24))];
                    pl_redirect('/onboarding?step=preview');
                } catch (DomainException $error) {
                    pl_form_failure('/onboarding', $_POST, $error->getMessage());
                }
            }
            if ($action === 'confirm') {
                $draft = $_SESSION['onboarding'] ?? null;
                if (!is_array($draft)) {
                    pl_notice('Please review your business details again before confirming.');
                    pl_redirect('/onboarding');
                }
                try {
                    $created = pl_setup_company($actorId, $draft['input'], $draft['request_key']);
                    $_SESSION['company_id'] = (int) $created['id'];
                    unset($_SESSION['onboarding']);
                    pl_notice($created['is_sample'] ? 'Your separate sample company is ready to explore.' : 'Your business and account template have been saved.');
                    pl_redirect($created['setup_status'] === 'opening_required' ? '/opening-balances' : '/transactions');
                } catch (DomainException $error) {
                    pl_form_failure('/onboarding?step=preview', [], $error->getMessage());
                }
            }
            throw new DomainException('Choose a valid setup action.');
        }
        $preview = pl_web_text($_GET, 'step') === 'preview' && isset($_SESSION['onboarding']);
        $form = pl_form_state($preview ? '/onboarding?step=preview' : '/onboarding');
        $input = $form['input'] ?: ($_SESSION['onboarding']['input'] ?? []);
        pl_render('onboarding', ['title' => $preview ? 'Review your setup' : 'Set up a business', 'user' => $user, 'template' => $template, 'preview' => $preview, 'form' => $form, 'input' => $input]);
    }
    if ($path === '/help') {
        pl_render('help', ['title' => 'Getting started', 'user' => $user]);
    }
    $company = pl_web_context($actorId);
    $companyId = (int) $company['id'];
    $bookId = (int) $company['book_id'];
    if ($path === '/opening-balances') {
        require_once dirname(__DIR__) . '/includes/functions/opening_web_functions.php';
        pl_web_opening($actorId, $companyId, $bookId, $user, $company, $method);
    }
    if ($path === '/periods') {
        require_once dirname(__DIR__) . '/includes/functions/period_web_functions.php';
        pl_web_periods($actorId, $companyId, $bookId, $user, $company, $method);
    }
    if ($path === '/bank-reconciliation') {
        require_once dirname(__DIR__) . '/includes/functions/reconciliation_web_functions.php';
        pl_web_reconciliation($actorId, $companyId, $bookId, $user, $company, $method);
    }
    if ($path === '/pos' || $path === '/pos/checkout' || $path === '/pos/receipt') {
        require_once dirname(__DIR__) . '/includes/functions/pos_functions.php';
        if ($path === '/pos/checkout') {
            try {
                if (pl_web_text($_POST, 'checkout_intent') !== 'record_cash_sale') {
                    throw new DomainException('Choose Record cash sale to confirm this cash sale.');
                }
                pl_web_assert_scope($company, $_POST);
                $checkoutInput = $_POST;
                unset($checkoutInput['csrf'], $checkoutInput['company_id'], $checkoutInput['book_id'], $checkoutInput['checkout_intent']);
                $receipt = pl_checkout_pos($actorId, $companyId, $bookId, $checkoutInput);
                pl_notice('Sale completed. Your receipt and balanced journal are saved.');
                pl_redirect(pl_url('/pos/receipt', ['id' => $receipt['document_id']]));
            } catch (DomainException $error) {
                pl_form_failure('/pos', $_POST, $error->getMessage());
            }
        }
        $receipt = $path === '/pos/receipt' ? pl_get_pos_receipt($actorId, $companyId, $bookId, pl_web_id($_GET, 'id')) : null;
        $form = pl_form_state('/pos');
        pl_render('pos', ['title' => $receipt ? 'Sale receipt' : 'Point of sale', 'user' => $user, 'company' => $company, 'catalog' => pl_pos_catalog(), 'form' => $form, 'input' => $form['input'], 'receipt' => $receipt]);
    }
    if ($path === '/reports') {
        $today = gmdate('Y-m-d');
        $periodFrom = min($company['start_date'], $today);
        $profit = pl_profit_loss($actorId, $companyId, $bookId, $periodFrom, $today);
        $balance = pl_balance_sheet($actorId, $companyId, $bookId, $today);
        $overview = ['as_of' => $today, 'period_from' => $periodFrom, 'cash' => pl_cash_balance($actorId, $companyId, $bookId, $today), 'income' => $profit['total_income'], 'expenses' => $profit['total_expenses'], 'profit' => $profit['net_profit'], 'assets' => $balance['total_assets'], 'liabilities' => $balance['total_liabilities'], 'equity' => $balance['total_equity']];
        pl_render('reports', ['title' => 'Your business in numbers', 'user' => $user, 'company' => $company, 'overview' => $overview]);
    }
    if ($path === '/reports/balance-sheet') {
        $asOf = pl_web_text($_GET, 'as_of', gmdate('Y-m-d'));
        $report = pl_balance_sheet($actorId, $companyId, $bookId, $asOf);
        pl_render('balance-sheet', ['title' => 'Balance sheet', 'user' => $user, 'company' => $company, 'report' => $report, 'asOf' => $asOf]);
    }
    if ($path === '/reports/profit-loss') {
        $to = pl_web_text($_GET, 'to', gmdate('Y-m-d'));
        $from = pl_web_text($_GET, 'from', $company['start_date']);
        $report = pl_profit_loss($actorId, $companyId, $bookId, $from, $to);
        pl_render('profit-loss', ['title' => 'Profit & loss', 'user' => $user, 'company' => $company, 'report' => $report, 'from' => $from, 'to' => $to]);
    }
    if ($path === '/reports/cash-forecast') {
        $asOf = gmdate('Y-m-d');
        $opening = pl_cash_balance($actorId, $companyId, $bookId, $asOf);
        $input = $_SESSION['cash_scenarios'][$companyId] ?? ['weekly_in' => $company['is_sample'] ? '250.00' : '0.00', 'weekly_out' => $company['is_sample'] ? '175.00' : '0.00', 'weeks' => '12'];
        $form = ['message' => '', 'input' => []];
        if ($method === 'POST') {
            pl_web_assert_scope($company, $_POST);
            $input = ['weekly_in' => pl_web_text($_POST, 'weekly_in'), 'weekly_out' => pl_web_text($_POST, 'weekly_out'), 'weeks' => pl_web_text($_POST, 'weeks')];
        }
        try {
            $forecast = pl_cash_forecast($opening, $input['weekly_in'], $input['weekly_out'], pl_web_id($input, 'weeks'));
            $_SESSION['cash_scenarios'][$companyId] = $input;
        } catch (DomainException|InvalidArgumentException $error) {
            http_response_code(422);
            $form['message'] = $error->getMessage();
            $forecast = null;
        }
        pl_render('cash-forecast', ['title' => 'Cash forecast', 'user' => $user, 'company' => $company, 'asOf' => $asOf, 'opening' => $opening, 'forecast' => $forecast, 'input' => $input, 'form' => $form]);
    }
    if ($path === '/setup/review') {
        if ($method === 'POST') {
            try {
                pl_web_assert_scope($company, $_POST);
                $assignments = is_array($_POST['roles'] ?? null) ? $_POST['roles'] : [];
                $mapped = [];
                foreach ($assignments as $key => $value) {
                    if (is_string($key) && is_scalar($value) && ctype_digit((string) $value)) {
                        $mapped[$key] = (int) $value;
                    }
                }
                pl_confirm_existing_setup($actorId, $companyId, $bookId, $mapped, pl_web_text($_POST, 'reviewed') === '1');
                pl_notice('Your existing accounts and balances are preserved. Setup review is complete.');
                pl_redirect('/transactions');
            } catch (DomainException $error) {
                pl_form_failure('/setup/review', $_POST, $error->getMessage());
            }
        }
        pl_render('setup-review', ['title' => 'Review business setup', 'user' => $user, 'company' => $company, 'template' => pl_starter_template(), 'form' => pl_form_state('/setup/review')]);
    }
    if ($path === '/transactions/save') {
        $documentId = pl_web_id($_POST, 'id');
        $return = $documentId ? pl_url('/transactions/edit', ['id' => $documentId]) : '/transactions/new';
        try {
            pl_web_assert_scope($company, $_POST);
            $input = [
                'kind' => pl_web_text($_POST, 'kind'), 'date' => pl_web_text($_POST, 'date'),
                'amount' => pl_web_text($_POST, 'amount'), 'money_account_id' => pl_web_id($_POST, 'money_account_id'),
                'category_account_id' => pl_web_id($_POST, 'category_account_id'), 'counterparty' => pl_web_text($_POST, 'counterparty'),
                'reference' => pl_web_text($_POST, 'reference'), 'memo' => pl_web_text($_POST, 'memo'),
                'creation_key' => pl_web_text($_POST, 'creation_key'),
            ];
            $saved = pl_save_document($actorId, $companyId, $bookId, $input, $documentId ?: null, $documentId ? pl_web_id($_POST, 'revision') : null);
            pl_notice('Draft saved. Your accounts have not changed.');
            pl_redirect(pl_url('/transactions', ['id' => $saved['id'], 'status' => 'draft']));
        } catch (DomainException $error) {
            pl_form_failure($return, $_POST, $error->getMessage());
        }
    }
    if ($path === '/transactions/post' || $path === '/transactions/reverse') {
        $id = pl_web_id($_POST, 'id');
        $return = pl_url('/transactions/detail', ['id' => $id]);
        try {
            pl_web_assert_scope($company, $_POST);
            if ($path === '/transactions/post') {
                $saved = pl_post_document($actorId, $companyId, $bookId, $id, pl_web_id($_POST, 'revision'));
                pl_notice('Transaction posted. Its balanced journal is now included in your reports.');
            } else {
                $saved = pl_reverse_document($actorId, $companyId, $bookId, $id, pl_web_text($_POST, 'date'), pl_web_text($_POST, 'reason'));
                pl_notice('Reversal posted. The original transaction and its history are preserved.');
            }
            pl_redirect(pl_url('/transactions', ['id' => $id, 'status' => $saved['status']]));
        } catch (DomainException $error) {
            pl_form_failure($return, $_POST, $error->getMessage());
        }
    }
    if ($path === '/transactions/new' || $path === '/transactions/edit') {
        if (!pl_can_write($company)) {
            throw new DomainException('Your role can view these books, but cannot edit transactions.');
        }
        $id = pl_web_id($_GET, 'id');
        $document = $path === '/transactions/edit' ? pl_get_document($actorId, $companyId, $bookId, $id) : null;
        if ($document && $document['status'] !== 'draft') {
            pl_redirect(pl_url('/transactions/detail', ['id' => $id]));
        }
        $form = pl_form_state($document ? pl_url('/transactions/edit', ['id' => $id]) : '/transactions/new');
        $input = $form['input'] ?: ($document ?? ['kind' => pl_web_text($_GET, 'kind', 'expense'), 'date' => gmdate('Y-m-d'), 'creation_key' => bin2hex(random_bytes(24))]);
        pl_render('editor', ['title' => $document ? 'Edit draft' : 'New transaction', 'user' => $user, 'company' => $company, 'document' => $document, 'input' => $input, 'form' => $form]);
    }
    if ($path === '/transactions' || $path === '/transactions/detail') {
        $filters = pl_filters($_GET);
        $list = pl_list_documents($actorId, $companyId, $bookId, $filters);
        $id = pl_web_id($_GET, 'id');
        if (!$id && $list['documents']) {
            $id = (int) $list['documents'][0]['id'];
        }
        $document = $id ? pl_get_document($actorId, $companyId, $bookId, $id) : null;
        $form = $id ? pl_form_state(pl_url('/transactions/detail', ['id' => $id])) : ['message' => '', 'input' => []];
        pl_render('transactions', ['title' => 'Transactions', 'user' => $user, 'company' => $company, 'list' => $list, 'filters' => $filters, 'document' => $document, 'form' => $form, 'detailOnly' => $path === '/transactions/detail']);
    }
    if ($path === '/reports/trial-balance') {
        $asOf = pl_web_text($_GET, 'as_of', gmdate('Y-m-d'));
        $report = pl_trial_balance($actorId, $companyId, $bookId, $asOf);
        pl_render('trial-balance', ['title' => 'Trial balance', 'user' => $user, 'company' => $company, 'report' => $report, 'asOf' => $asOf]);
    }
    if ($path === '/reports/account') {
        $asOf = pl_web_text($_GET, 'as_of', gmdate('Y-m-d'));
        $activity = pl_account_activity($actorId, $companyId, $bookId, pl_web_id($_GET, 'id'), $asOf, max(1, pl_web_id($_GET, 'page', 1)), pl_web_text($_GET, 'from') ?: null);
        pl_render('account', ['title' => 'Account activity', 'user' => $user, 'company' => $company, 'activity' => $activity, 'asOf' => $asOf]);
    }
    // The remaining method-checked route is /journals/detail.
    $journal = pl_get_journal($actorId, $companyId, $bookId, pl_web_id($_GET, 'id'));
    pl_render('journal', ['title' => 'Journal entry', 'user' => $user, 'company' => $company, 'journal' => $journal]);
} catch (PlDemoUnavailable $error) {
    http_response_code(503);
    header('Retry-After: 10');
    pl_render('error', ['title' => 'Your sample will be ready shortly', 'message' => $error->getMessage(), 'user' => null]);
} catch (DomainException $error) {
    http_response_code(403);
    pl_render('error', ['title' => 'This action is unavailable', 'message' => $error->getMessage(), 'user' => $user ?? null]);
} catch (Throwable $error) {
    http_response_code(503);
    error_log('PHP Ledger request unavailable (' . get_class($error) . ').');
    echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>PHP Ledger unavailable</title><link rel="stylesheet" href="' . htmlspecialchars(pl_url('/assets/app.css'), ENT_QUOTES, 'UTF-8') . '"><main class="standalone"><h1>PHP Ledger is temporarily unavailable.</h1><p>Your request could not be completed. Please try again. If this is a new installation, check its setup and migration status.</p><a href="' . htmlspecialchars(pl_url('/'), ENT_QUOTES, 'UTF-8') . '">Try again</a></main></html>';
}
