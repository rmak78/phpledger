<?php
declare(strict_types=1);

function pl_web_modules(int $actorId, int $companyId, int $bookId, array $user, array $company, string $method): never
{
    if (pl_demo_enabled()) { throw new DomainException('Module administration is unavailable in the public sample.'); }
    if ($method === 'POST') {
        try {
            pl_web_assert_scope($company, $_POST);
            $enable = pl_web_text($_POST, 'enabled');
            if (!in_array($enable, ['0', '1'], true)) { throw new DomainException('Choose enable or disable.'); }
            $result = pl_set_company_module($actorId, $companyId, pl_web_text($_POST, 'module_id'), $enable === '1',
                pl_web_id($_POST, 'revision'), pl_web_text($_POST, 'digest'), pl_web_text($_POST, 'reason'), pl_web_text($_POST, 'request_key'));
            pl_notice($result['enabled'] ? 'Module enabled. Its current version is ready.' : 'Module disabled. Posted history remains available.');
            pl_redirect('/modules');
        } catch (DomainException $error) { pl_form_failure(pl_url('/modules'), $_POST, $error->getMessage()); }
    }
    $modules = [];
    foreach (pl_module_registry() as $manifest) {
        if (!$manifest['optional']) { continue; }
        $state = pl_module_state($companyId, $manifest['id']);
        $problem = '';
        try { pl_module_installed($manifest); } catch (DomainException $error) { $problem = $error->getMessage(); }
        $modules[] = ['manifest' => $manifest, 'state' => $state, 'problem' => $problem,
            'current' => $state['enabled'] && $state['version'] === $manifest['version'] && $state['manifest_hash'] === $manifest['digest']];
    }
    pl_render('modules', ['title' => 'Modules', 'user' => $user, 'company' => $company, 'modules' => $modules,
        'form' => pl_form_state(pl_url('/modules')), 'history' => pl_module_history($actorId, $companyId)]);
}
