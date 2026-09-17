<?php
declare(strict_types=1);

    $layout = file_get_contents(dirname(__DIR__) . '/www/phpledger/templates/layout.php') . file_get_contents(dirname(__DIR__) . '/www/phpledger/templates/partials/ui/shell.php');
    $styles = file_get_contents(dirname(__DIR__) . '/www/phpledger/public/assets/app.css');
    $app = file_get_contents(dirname(__DIR__) . '/www/phpledger/public/assets/app.js');
    $guide = file_get_contents(dirname(__DIR__) . '/www/phpledger/templates/views/sample-guide.php');

test('shared shell exposes the candidate version and grouped navigation contract', function () use ($layout, $styles, $app, $guide): void {
    assert_true(is_string($layout) && str_contains($layout, 'pl_app_version()'), 'Layout does not use the shared version helper.');
    assert_true(is_string($layout) && substr_count($layout, 'pl_app_version()') >= 2, 'Version is not rendered in both workspace and focused layouts.');
    assert_true(is_string($layout) && str_contains($layout, 'class="shell-nav"'), 'Grouped navigation is missing.');
    assert_true(is_string($layout) && str_contains($layout, 'shell-sidebar'), 'Workspace sidebar hook is missing.');
    assert_true(is_string($layout) && !str_contains($layout, '<nav class="accounting-nav"'), 'The shell still renders a second administration navigation strip.');
    assert_true(is_string($layout) && str_contains($layout, 'pl_module_available'), 'Navigation lost server-side module checks.');
    assert_true(is_string($styles) && str_contains($styles, '.shell-version'), 'Brand version treatment is missing.');
    assert_true(is_string($styles) && str_contains($styles, '.menu-panel'), 'Responsive navigation panel treatment is missing.');
    assert_true(is_string($styles) && str_contains($styles, '.shell-sidebar'), 'Desktop workspace rail styling is missing.');
    assert_true(is_string($app) && str_contains($app, '[data-fiscal-year-end-choice]') && str_contains($app, 'customGroup.hidden = !isCustom'), 'Fiscal year-end progressive disclosure behavior is missing.');
    assert_true(is_string($guide) && str_contains($guide, 'sample-evidence'), 'Sample guide does not expose pinned research evidence.');
    assert_true(is_string($guide) && str_contains($guide, 'research_evidence'), 'Sample guide does not bind its evidence section to the pack contract.');
    assert_true(is_string($guide) && str_contains($guide, 'industry_profile'), 'Sample guide does not expose the selected vertical profile.');
});

test('setup shell exposes six focused decisions and an explicit chart choice', function () use ($layout, $styles): void {
    $onboarding = file_get_contents(dirname(__DIR__) . '/www/phpledger/templates/views/onboarding.php');
    $controller = file_get_contents(dirname(__DIR__) . '/www/phpledger/public/index.php');
    $chooser = file_get_contents(dirname(__DIR__) . '/www/phpledger/templates/views/sample-chooser.php');
    assert_true(is_string($onboarding) && str_contains($onboarding, 'Step <?= $preview ? \'5 of 6\''), 'Onboarding is not a six-step flow.');
    assert_true(is_string($onboarding) && str_contains($onboarding, 'name="chart_choice"'), 'Neutral/bring-your-own chart choice is missing.');
    assert_true(is_string($onboarding) && str_contains($onboarding, 'data-fiscal-year-end-choice') && str_contains($onboarding, 'data-fiscal-custom-group'), 'Fiscal year-end choices do not provide the progressive disclosure hooks.');
    assert_true(is_string($controller) && str_contains($controller, "if (\$action === 'next')"), 'Onboarding step transitions are not server handled.');
    assert_true(is_string($controller) && str_contains($controller, "'/sample-chooser' => ['GET', 'POST']"), 'Local sample chooser route is missing.');
    assert_true(is_string($controller) && str_contains($controller, "pl_demo_sample(\$sampleId)"), 'Sample selection does not resolve through the bundled catalogue.');
    assert_true(is_string($chooser) && str_contains($chooser, 'name="sample_pack"') && str_contains($chooser, 'pl_demo_sample_choices()'), 'Sample chooser does not expose the bundled selection contract.');
    assert_true(is_string($styles) && str_contains($styles, '.setup-progress'), 'Setup progress styles are missing.');
});

test('sample import has a bounded operational replay path', function (): void {
    $demo = file_get_contents(dirname(__DIR__) . '/www/phpledger/includes/functions/demo_pack_functions.php');
    assert_true(is_string($demo) && str_contains($demo, 'function pl_demo_operational_contract'), 'Operational contract validator is missing.');
    assert_true(is_string($demo) && str_contains($demo, 'function pl_demo_operational_replay'), 'Operational replay adapter is missing.');
    assert_true(is_string($demo) && str_contains($demo, 'pl_save_ar_document') && str_contains($demo, 'pl_settle_ar_document'), 'Replay does not use the AR/AP services.');
    assert_true(is_string($demo) && str_contains($demo, 'pl_save_purchase_order') && str_contains($demo, 'pl_receive_purchase_order'), 'Replay does not use the purchasing service.');
    assert_true(is_string($demo) && str_contains($demo, 'pl_inventory_issue'), 'Replay does not use the inventory service.');
    assert_true(is_string($demo) && str_contains($demo, 'pl_record_installation_history'), 'Replay receipt is not retained in immutable installation history.');
    assert_true(is_string($demo) && str_contains($demo, 'count($events) > 32') && str_contains($demo, '$seenReferences'), 'Operational admission is not bounded by durable source identity.');
    assert_true(is_string($demo) && str_contains($demo, "['deferred_revenue', 'customer_advance', 'store_credit_liability']"), 'Deferred revenue is not classified before income roles.');
});
