<?php
declare(strict_types=1);

/** Web presentation helpers. Financial rules remain in the existing services. */
function pl_web_unavailable_page(int $status): void
{
    // Route and dependency failures must render without opening a database/session.
    require_once __DIR__ . '/security_functions.php';
    require_once dirname(__DIR__, 2) . '/templates/partials/ui/components.php';
    $title = match ($status) {
        404 => "We couldn't find that page.",
        405 => 'That action needs a different request.',
        default => 'PHP Ledger is temporarily unavailable.',
    };
    $message = match ($status) {
        404 => 'It may have moved, or the link may be out of date. Nothing was changed in your books.',
        405 => "This link only works when it is submitted from its own form. Go back and use the on-screen button instead of visiting this address directly. Nothing was saved.",
        default => 'Your request could not be completed. Please try again. If this is a new installation, check its setup and migration status.',
    };
    require dirname(__DIR__, 2) . '/templates/partials/ui/unavailable.php';
}

function pl_web_text(array $source, string $key, string $default = ''): string
{
    return isset($source[$key]) && is_string($source[$key]) ? trim($source[$key]) : $default;
}

/** The application version is a shared presentation value, not a user-controlled setting. */
function pl_app_version(): string
{
    return '0.5.0-preview';
}

function pl_web_id(array $source, string $key, int $default = 0): int
{
    $value = $source[$key] ?? null;
    return is_scalar($value) && ctype_digit((string) $value) ? (int) $value : $default;
}

function pl_url(string $path, array $query = []): string
{
    $base = pl_base_path();
    if ($base !== '' && $path !== $base && !str_starts_with($path, $base . '/')) {
        $path = $base . $path;
    }
    $query = array_filter($query, static fn ($v): bool => $v !== '' && $v !== null);
    return $path . ($query === [] ? '' : '?' . http_build_query($query));
}

function pl_base_path(): string
{
    $base = rtrim((string) (getenv('PL_BASE_PATH') ?: ''), '/');
    if ($base !== '' && !preg_match('~^/[a-zA-Z0-9_-]+(?:/[a-zA-Z0-9_-]+)*$~D', $base)) {
        throw new RuntimeException('Invalid application base path.');
    }
    return $base;
}

/** The insecure cookie exception is limited to an explicitly enabled, local demo. */
function pl_web_local_demo_http(array $server): bool
{
    if (getenv('PL_ENV') !== 'demo' || getenv('PL_DEMO_LOCAL_HTTP') !== '1') {
        return false;
    }
    $host = parse_url('http://' . ($server['HTTP_HOST'] ?? ''), PHP_URL_HOST);
    $peer = pl_normalize_ip((string) ($server['REMOTE_ADDR'] ?? ''));
    if (!in_array($host, ['127.0.0.1', 'localhost', '[::1]'], true) || $peer === null) {
        return false;
    }
    $packed = inet_pton($peer);
    if ($packed === false) {
        return false;
    }
    if (strlen($packed) === 4) {
        $first = ord($packed[0]);
        $second = ord($packed[1]);
        return $first === 127 || $first === 10 || ($first === 172 && $second >= 16 && $second <= 31)
            || ($first === 192 && $second === 168);
    }
    return $peer === '::1' || (ord($packed[0]) & 0xfe) === 0xfc;
}

function pl_redirect(string $path): never
{
    header('Location: ' . pl_url($path), true, 303);
    exit;
}

function pl_notice(string $message): void
{
    $_SESSION['notice'] = $message;
}

function pl_form_failure(string $path, array $input, string $message, int $status = 422): never
{
    unset($input['password'], $input['csrf']);
    $_SESSION['form_failure'] = ['path' => $path, 'input' => $input, 'message' => $message, 'status' => $status === 200 ? 200 : 422];
    pl_redirect($path);
}

function pl_form_state(string $path): array
{
    $failure = $_SESSION['form_failure'] ?? null;
    if (is_array($failure) && ($failure['path'] ?? null) === $path) {
        unset($_SESSION['form_failure']);
        http_response_code(($failure['status'] ?? 422) === 200 ? 200 : 422);
        return $failure;
    }
    return ['input' => [], 'message' => ''];
}

function pl_web_context(int $actorId): array
{
    $id = (int) ($_SESSION['company_id'] ?? 0);
    if ($id < 1) {
        pl_redirect('/companies');
    }
    return pl_company_context($actorId, $id);
}

function pl_web_assert_scope(array $company, array $input): void
{
    if (pl_web_id($input, 'company_id') !== (int) $company['id']
        || pl_web_id($input, 'book_id') !== (int) $company['book_id']) {
        throw new DomainException('Your company changed in another tab. Reopen this record in the intended company.');
    }
}

function pl_filters(array $input): array
{
    return [
        'status' => pl_web_text($input, 'status', 'draft'),
        'kind' => pl_web_text($input, 'kind', 'all'),
        'search' => mb_substr(pl_web_text($input, 'search'), 0, 160),
        'from' => pl_web_text($input, 'from'),
        'to' => pl_web_text($input, 'to'),
        'page' => max(1, pl_web_id($input, 'page', 1)),
    ];
}

/** Canonical bookmarkable list input; never pass a browser column name to SQL. */
function pl_list_filters(array $input, string $screen): array
{
    $sorts = match ($screen) {
        'transactions' => ['date','name','amount','status'],
        'general-journals' => ['date','description','status'],
        'account' => ['date','journal','description','source','debit','credit','balance'],
        'bank' => ['date','reference','money_in','money_out','match'],
        default => throw new DomainException('Unknown list.'),
    };
    $page = $input['page'] ?? '1';
    $size = $input['per_page'] ?? '25';
    foreach ([$page, $size] as $number) {
        if ((!is_int($number) && !is_string($number)) || !preg_match('/^[1-9][0-9]{0,5}$/D', (string)$number)) {
            throw new DomainException('Choose a valid list page and page size.');
        }
    }
    if ((int)$page > 100000 || !in_array((int)$size, [25,50,100], true)) { throw new DomainException('Choose 25, 50 or 100 rows per page.'); }
    $sort = $input['sort'] ?? 'date';
    $direction = $input['dir'] ?? ($screen === 'account' ? 'asc' : 'desc');
    if (!is_string($sort) || !in_array($sort, $sorts, true) || !in_array($direction, ['asc','desc'], true)) {
        throw new DomainException('Unsupported list order.');
    }
    $query = $input['q'] ?? $input['search'] ?? '';
    if (!is_string($query) || mb_strlen($query) > 160) { throw new DomainException('Search must be text of up to 160 characters.'); }
    $filters = ['page' => (int)$page, 'per_page' => (int)$size, 'q' => trim($query), 'sort' => $sort, 'dir' => $direction];
    if (in_array($screen, ['transactions','general-journals'], true)) {
        $status = $input['status'] ?? ($screen === 'transactions' ? 'draft' : 'all');
        if (!in_array($status, ['all','draft','posted','reversed'], true)) { throw new DomainException('Choose a valid status.'); }
        $filters['status'] = $status;
    }
    if ($screen === 'transactions') {
        $kind = $input['kind'] ?? 'all';
        if (!in_array($kind, ['all','receipt','expense'], true)) { throw new DomainException('Choose a valid transaction type.'); }
        $filters['kind'] = $kind;
        foreach (['from','to'] as $key) {
            $value = $input[$key] ?? '';
            if (!is_string($value)) { throw new DomainException('Choose a valid filter date.'); }
            $filters[$key] = $value === '' ? '' : pl_ledger_date($value);
        }
    }
    return $filters;
}

/** Carry only validated list state through editor submissions and record actions. */
function pl_return_list_filters(array $input, string $screen): array
{
    $filters = $input['return_filters'] ?? [];
    if (!is_array($filters)) { throw new DomainException('Invalid list return filters.'); }
    return pl_list_filters($filters, $screen);
}

/** Reuse the existing scoped, counted LIMIT/OFFSET services, including running balances. */
function pl_list_query(int $actorId, int $companyId, int $bookId, string $screen, array $input): array
{
    $filters = pl_list_filters($input, $screen);
    $options = $filters + ['page_size' => $filters['per_page'], 'search' => $filters['q'], 'direction' => $filters['dir']];
    return match ($screen) {
        'transactions' => pl_list_documents($actorId, $companyId, $bookId, $options),
        'general-journals' => pl_list_general_drafts($actorId, $companyId, $bookId, $filters['page'], $options),
        'account' => pl_account_activity($actorId, $companyId, $bookId, pl_web_id($input, 'id'), pl_web_text($input, 'as_of', gmdate('Y-m-d')), $filters['page'], pl_web_text($input, 'from') ?: null, $options),
        'bank' => pl_bank_get_statement($actorId, $companyId, $bookId, pl_web_id($input, 'statement_id'), $options),
        default => throw new DomainException('Unknown list.'),
    };
}

function pl_money(string $amount): string
{
    if (!preg_match('/^(-?)([0-9]+)(?:\.([0-9]{1,4}))?$/D', $amount, $match)) {
        return $amount;
    }
    $decimals = str_pad($match[3] ?? '', 4, '0');
    $decimals = substr($decimals, 2) === '00' ? substr($decimals, 0, 2) : $decimals;
    $whole = preg_replace('/\B(?=([0-9]{3})+(?![0-9]))/', ',', $match[2]);
    return $match[1] . $whole . '.' . $decimals;
}

function pl_date_label(string $date): string
{
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed ? $parsed->format('d M Y') : $date;
}

function pl_icon(string $name): string
{
    $allowed = ['search', 'plus', 'chevron-down', 'chevron-left', 'chevron-right', 'arrow-left',
        'arrow-right', 'logout', 'building', 'check', 'x', 'info-circle', 'alert-circle',
        'file-text', 'book', 'adjustments-horizontal', 'external-link', 'arrow-back-up', 'receipt', 'list', 'menu-2',
        'alert-triangle', 'arrows-shuffle', 'book-2', 'briefcase', 'building-bank', 'building-cog', 'building-store',
        'calendar', 'cash', 'cash-register', 'chart-line', 'chef-hat', 'circle-check', 'clipboard-check', 'clipboard-list',
        'copy', 'diamond', 'dots-vertical', 'download', 'file-check', 'file-dollar', 'file-invoice', 'filter', 'flag-2',
        'flask', 'git-branch', 'grid-dots', 'help-circle', 'history', 'home', 'key', 'layout-sidebar-left-collapse', 'link',
        'list-check', 'list-details', 'lock', 'package', 'pencil', 'printer', 'receipt-2', 'receipt-refund', 'receipt-tax',
        'refresh', 'report', 'rocket', 'scale', 'shopping-bag', 'stethoscope', 'trash', 'trending-up', 'truck', 'truck-delivery', 'user-plus', 'users'];
    if (!in_array($name, $allowed, true)) {
        return '';
    }
    return '<img class="icon" src="' . pl_e(pl_url('/assets/icons/' . $name . '.svg')) . '" alt="" width="20" height="20">';
}

function pl_csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . pl_e(pl_csrf_token()) . '">';
}

function pl_scope_fields(array $company): string
{
    return '<input type="hidden" name="company_id" value="' . (int) $company['id']
        . '"><input type="hidden" name="book_id" value="' . (int) $company['book_id'] . '">';
}

function pl_can_write(array $company): bool
{
    return in_array($company['role'] ?? '', ['owner', 'accountant'], true);
}

/** Convert browser rows to service input; blank spare rows are not accounting entries. */
/** A no-JavaScript line action preserves incomplete fields without saving a draft. */
function pl_web_journal_line_action(array $input): array
{
    return pl_web_line_action($input);
}

function pl_web_line_action(array $input): array
{
    $lines = $input['lines'] ?? [];
    if (!is_array($lines) || count($lines) > 100 || count(array_filter($lines, 'is_array')) !== count($lines)) {
        throw new DomainException('Use up to 100 document lines.');
    }
    $lines = array_values($lines);
    if (pl_web_text($input, 'editor_action') === 'add_line') {
        if (count($lines) >= 100) { throw new DomainException('A document supports up to 100 lines.'); }
        $lines[] = [];
    } elseif (isset($input['remove_line'])) {
        $index = pl_web_text($input, 'remove_line');
        if ($index === '' || !ctype_digit($index) || !array_key_exists((int) $index, $lines)) { throw new DomainException('Choose a current document line.'); }
        array_splice($lines, (int) $index, 1);
    } else { throw new DomainException('Choose an add or remove line action.'); }
    $input['lines'] = $lines ?: [[]];
    unset($input['editor_action'], $input['remove_line']);
    return $input;
}

function pl_web_document_input(array $input): array
{
    return [
        'kind'=>pl_web_text($input,'kind'), 'date'=>pl_web_text($input,'date'),
        'amount'=>pl_web_text($input,'amount'), 'money_account_id'=>pl_web_id($input,'money_account_id'),
        'category_account_id'=>pl_web_id($input,'category_account_id'), 'counterparty'=>pl_web_text($input,'counterparty'),
        'reference'=>pl_web_text($input,'reference'), 'memo'=>pl_web_text($input,'memo'), 'creation_key'=>pl_web_text($input,'creation_key'),
    ];
}

/** Calendar presets resolve on the server too, including without JavaScript. */
function pl_report_period(string $preset, string $today): ?array
{
    $date = new DateTimeImmutable(pl_ledger_date($today));
    $quarterMonth = intdiv((int)$date->format('n') - 1, 3) * 3 + 1;
    $start = match ($preset) {
        'month' => $date->modify('first day of this month'),
        'last_month' => $date->modify('first day of last month'),
        'quarter' => $date->setDate((int)$date->format('Y'), $quarterMonth, 1),
        'year' => $date->setDate((int)$date->format('Y'), 1, 1),
        'custom' => null,
        default => throw new DomainException('Choose a supported report period.'),
    };
    if ($start === null) { return null; }
    $end = $preset === 'last_month' ? $date->modify('last day of last month') : $date;
    return ['from'=>$start->format('Y-m-d'), 'to'=>$end->format('Y-m-d')];
}

function pl_web_general_input(array $input): array
{
    $rows = $input['lines'] ?? [];
    if (!is_array($rows) || count($rows) > 100) { throw new DomainException('Use up to 100 journal lines.'); }
    $lines = [];
    foreach ($rows as $row) {
        if (!is_array($row)) { throw new DomainException('A journal line is invalid.'); }
        foreach (['account_id', 'debit', 'credit', 'description'] as $field) {
            if (isset($row[$field]) && !is_string($row[$field])) { throw new DomainException('Journal fields must contain text or decimal amounts.'); }
        }
        $id = pl_web_id($row, 'account_id');
        $debit = pl_web_text($row, 'debit'); $credit = pl_web_text($row, 'credit');
        $description = pl_web_text($row, 'description');
        if (!$id && $debit === '' && $credit === '' && $description === '') { continue; }
        $lines[] = ['account_id' => $id, 'debit' => $debit === '' ? '0' : $debit, 'credit' => $credit === '' ? '0' : $credit, 'description' => $description];
    }
    return ['date' => pl_web_text($input, 'date'), 'reference' => pl_web_text($input, 'reference'), 'description' => pl_web_text($input, 'description'), 'creation_key' => pl_web_text($input, 'creation_key'), 'lines' => $lines];
}

function pl_render(string $view, array $data = []): never
{
    $allowed = ['home','ar','ap','parties','inventory','purchasing','tax','opening-conversion','login', 'companies', 'sample-chooser', 'onboarding', 'setup-review', 'transactions', 'editor',
        'trial-balance', 'account', 'journal', 'help', 'error', 'demo', 'reports', 'balance-sheet', 'profit-loss', 'cash-forecast', 'pos', 'ageing', 'settlement', 'stock-count', 'goods-receipt',
        'accounts', 'general-journals', 'general-editor', 'general-detail', 'modules', 'opening-balances', 'periods', 'bank-reconciliation', 'connections', 'oauth-consent', 'sample-guide'];
    if (!in_array($view, $allowed, true)) {
        throw new LogicException('Unknown template.');
    }
    extract($data, EXTR_SKIP);
    $title = $data['title'] ?? 'PHP Ledger';
    $company = $data['company'] ?? null;
    $user = $data['user'] ?? null;
    $regional = $_SESSION['regional_suggestion'] ?? [];
    $notice = $_SESSION['notice'] ?? '';
    unset($_SESSION['notice']);
    require PL_APP . '/templates/layout.php';
    exit;
}
