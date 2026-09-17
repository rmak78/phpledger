<?php
declare(strict_types=1);

/** Presentation only. Callbacks render trusted templates; all text is escaped here. */
function pl_ui_page_header(string $title, string $description = '', ?callable $actions = null, string $id = ''): void
{
    echo '<header class="page-header"><div><h1 class="page-title"' . ($id !== '' ? ' id="' . pl_e($id) . '"' : '') . '>' . pl_e($title) . '</h1>';
    if ($description !== '') { echo '<p class="text-sm text-ink-muted mt-1">' . pl_e($description) . '</p>'; }
    echo '</div><div class="page-header-actions" data-fold="primary actions">';
    if ($actions !== null) { $actions(); }
    echo '</div></header>';
}

function pl_ui_badge(string $status, ?string $label = null): void
{
    $allowed = ['draft','posted','reversed','unpaid','paid','partially-paid','due-soon','overdue','info','sample'];
    $kind = in_array($status, $allowed, true) ? $status : 'draft';
    echo '<span class="badge badge-' . $kind . '"><span class="badge-dot" aria-hidden="true"></span>'
        . pl_e($label ?? ucfirst(str_replace('-', ' ', $status))) . '</span>';
}

function pl_ui_empty(string $title, string $description, ?callable $action = null): void
{
    echo '<div class="empty-state"><h2>' . pl_e($title) . '</h2><p>' . pl_e($description) . '</p>';
    if ($action !== null) { $action(); }
    echo '</div>';
}

function pl_ui_strip(string $message, string $kind = 'info', ?callable $action = null): void
{
    $kind = in_array($kind, ['info','warning','sample'], true) ? $kind : 'info';
    echo '<div class="strip strip-' . $kind . '"><p>' . pl_e($message) . '</p>';
    if ($action !== null) { $action(); }
    echo '</div>';
}

function pl_ui_totals(array $rows): void
{
    echo '<dl class="doc-totals" data-fold="totals">';
    foreach ($rows as $label => $amount) {
        echo '<div class="doc-totals-row"><dt>' . pl_e((string)$label) . '</dt><dd class="amount">' . pl_e((string)$amount) . '</dd></div>';
    }
    echo '</dl>';
}

function pl_ui_table(array $headings, callable $rows, string $caption): void
{
    echo '<div class="table-wrap" tabindex="0" role="region" aria-label="' . pl_e($caption) . '"><table class="table"><caption class="sr-only">' . pl_e($caption) . '</caption><thead><tr>';
    foreach ($headings as $heading) { echo '<th scope="col">' . pl_e((string)$heading) . '</th>'; }
    echo '</tr></thead><tbody>'; $rows(); echo '</tbody></table></div>';
}

function pl_ui_field(string $id, string $label, callable $control, string $hint = '', string $error = ''): void
{
    echo '<div class="field' . ($error !== '' ? ' field-error' : '') . '"><label class="field-label" for="' . pl_e($id) . '">' . pl_e($label) . '</label>';
    $control();
    if ($hint !== '') { echo '<p class="field-hint" id="' . pl_e($id) . '-hint">' . pl_e($hint) . '</p>'; }
    if ($error !== '') { echo '<p class="field-error-text" id="' . pl_e($id) . '-error">' . pl_e($error) . '</p>'; }
    echo '</div>';
}

function pl_ui_stepper(array $steps, int $current): void
{
    echo '<ol class="stepper" aria-label="Progress">';
    foreach (array_values($steps) as $index => $label) {
        $number = $index + 1;
        echo '<li class="stepper-step' . ($number === $current ? ' is-current' : ($number < $current ? ' is-done' : '')) . '"'
            . ($number === $current ? ' aria-current="step"' : '') . '><span class="stepper-index">' . $number . '</span><span class="stepper-label">' . pl_e((string)$label) . '</span></li>';
    }
    echo '</ol>';
}

/** Native links keep all sections available without JavaScript. */
function pl_ui_tabs(array $links, string $current, bool $enhance = false): void
{
    echo '<nav class="tabs-underline" aria-label="Sections"' . ($enhance ? ' data-ui-tabs' : '') . '>';
    foreach ($links as $label => $href) {
        echo '<a class="tabs-underline-item" href="' . pl_e((string)$href) . '"' . ((string)$label === $current ? ' aria-current="page"' : '') . '>' . pl_e((string)$label) . '</a>';
    }
    echo '</nav>';
}

function pl_ui_document_header(string $title, string $status, callable $actions, string $id = ''): void
{
    echo '<header class="doc-header"><div class="doc-heading"><h1 class="doc-number"' . ($id !== '' ? ' id="' . pl_e($id) . '"' : '') . '>' . pl_e($title) . '</h1>';
    if ($status!=='') { pl_ui_badge($status); }
    echo '</div><div class="doc-actions" data-fold="primary actions">'; $actions(); echo '</div></header>';
}

function pl_ui_side_panel(string $title, callable $body): void
{
    echo '<section class="split-view-detail" aria-label="' . pl_e($title) . '">'; $body(); echo '</section>';
}

/** The confirmation is server-rendered; JavaScript may enhance its native details. */
function pl_ui_confirmation(string $title, string $consequence, callable $form): void
{
    echo '<details class="confirmation" data-confirmation><summary class="btn btn-secondary">' . pl_e($title) . '</summary><div data-confirmation-body><h2 class="section-title">' . pl_e($title) . '?</h2><p class="field-hint">' . pl_e($consequence) . '</p><div class="flex flex-wrap gap-2 mt-3">';
    $form(); echo '<button type="button" class="btn btn-ghost" data-confirmation-cancel hidden>Cancel</button></div></div></details>';
}

function pl_ui_pagination(string $path, array $filters, int $page, int $pages): void
{
    echo '<nav class="pagination" aria-label="List pages">';
    if ($page > 1) { echo '<a class="btn btn-secondary btn-sm" href="' . pl_e(pl_url($path, array_replace($filters, ['page' => $page - 1]))) . '">Previous</a>'; }
    echo '<span>Page ' . $page . ' of ' . max(1, $pages) . '</span>';
    if ($page < $pages) { echo '<a class="btn btn-secondary btn-sm" href="' . pl_e(pl_url($path, array_replace($filters, ['page' => $page + 1]))) . '">Next</a>'; }
    echo '</nav>';
}

function pl_ui_connection_scope(string $id): void
{
    pl_ui_field($id, 'Access scope', static function () use ($id): void {
        echo '<select class="select" id="' . pl_e($id) . '" name="access_mode" required><option value="reports">Report-only — summary financial reports</option><option value="full">Full read — reports and individual records</option></select>';
    });
    echo '<p class="field-hint">Report-only includes company discovery, trial balance, profit and loss, and balance sheet. Full read also includes accounts, transactions, journals and account statements. Neither permits financial writes.</p>';
}

/** Canonical list filters, never a user-controlled redirect URL. */
function pl_ui_return_filters(array $filters): void
{
    foreach ($filters as $key=>$value) {
        echo '<input type="hidden" name="return_filters[' . pl_e((string)$key) . ']" value="' . pl_e((string)$value) . '">';
    }
}

function pl_ui_sort(string $path, array $filters, string $column, string $label): void
{
    $active = ($filters['sort'] ?? '') === $column;
    $direction = $active && ($filters['dir'] ?? '') === 'asc' ? 'desc' : 'asc';
    echo '<a href="' . pl_e(pl_url($path, array_replace($filters, ['sort' => $column, 'dir' => $direction, 'page' => 1]))) . '">' . pl_e($label)
        . ($active ? (($filters['dir'] ?? '') === 'asc' ? ' ↑' : ' ↓') : '') . '</a>';
}

function pl_ui_list_controls(array $filters): void
{
    echo '<label class="field"><span class="sr-only">Search records</span><input class="input" type="search" name="q" maxlength="160" value="' . pl_e($filters['q']) . '" placeholder="Search records"></label>';
    echo '<label class="field"><span class="sr-only">Rows per page</span><select class="select" name="per_page">';
    foreach ([25,50,100] as $size) { echo '<option value="' . $size . '"' . ($size === $filters['per_page'] ? ' selected' : '') . '>' . $size . ' per page</option>'; }
    echo '</select></label><input type="hidden" name="sort" value="' . pl_e($filters['sort']) . '"><input type="hidden" name="dir" value="' . pl_e($filters['dir']) . '">';
}
