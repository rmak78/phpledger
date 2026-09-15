<?php
declare(strict_types=1);

// Read-only catalog checks. No application bootstrap, database, network or activation code.
const TAX_CATALOG_COUNTRIES = [
    'PK' => ['pakistan', 'PKR'], 'GB' => ['united-kingdom', 'GBP'],
    'AE' => ['united-arab-emirates', 'AED'], 'MY' => ['malaysia', 'MYR'],
    'BD' => ['bangladesh', 'BDT'], 'LK' => ['sri-lanka', 'LKR'],
    'NP' => ['nepal', 'NPR'], 'SG' => ['singapore', 'SGD'],
];
const TAX_CATALOG_INDUSTRIES = ['restaurant', 'membership_club', 'pharmacy', 'trader', 'distributor', 'retail_shop', 'workshop'];
const TAX_CATALOG_FAMILIES = ['advance_income_tax', 'corporate_income_tax', 'gst', 'income_tax', 'input_tax', 'minimum_tax', 'personal_income_tax', 'sales_tax', 'service_tax', 'supplementary_duty', 'turnover_levy', 'turnover_tax', 'vat', 'withholding_tax'];

function tax_catalog_check(bool $ok, string $message): void
{
    if (!$ok) { throw new RuntimeException($message); }
}

function tax_catalog_object(mixed $value, array $keys, string $label): array
{
    tax_catalog_check(is_array($value) && !array_is_list($value), $label . ' must be an object.');
    $actual = array_keys($value);
    sort($actual); sort($keys);
    tax_catalog_check($actual === $keys, $label . ' has missing or unsupported schema-1 fields.');
    return $value;
}

function tax_catalog_text(mixed $value, string $label): string
{
    tax_catalog_check(is_string($value) && trim($value) !== '', $label . ' must be non-empty text.');
    return $value;
}

function tax_catalog_list(mixed $value, string $label, int $minimum = 1): array
{
    tax_catalog_check(is_array($value) && array_is_list($value) && count($value) >= $minimum, $label . ' must be a non-empty list.');
    return $value;
}

function tax_catalog_strings(mixed $value, string $label, int $minimum = 1): array
{
    $items = tax_catalog_list($value, $label, $minimum);
    $seen = [];
    foreach ($items as $item) {
        $text = tax_catalog_text($item, $label);
        tax_catalog_check(!isset($seen[$text]), $label . ' contains a duplicate.');
        $seen[$text] = true;
    }
    return $items;
}

function tax_catalog_date(mixed $value, string $label, bool $nullable = false): ?string
{
    if ($nullable && $value === null) { return null; }
    if (!is_string($value) || preg_match('/^(\d{4})-(\d{2})-(\d{2})$/D', $value, $parts) !== 1) {
        throw new RuntimeException($label . ' must be an ISO calendar date.');
    }
    tax_catalog_check(checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1]), $label . ' is not a valid calendar date.');
    return $value;
}

function tax_catalog_rate(mixed $value): void
{
    if ($value === null) { return; }
    // This representation bound is not a legal rate ceiling; duties can exceed 100 percent.
    tax_catalog_check(is_string($value) && preg_match('/^(?:0|[1-9]\d{0,3})(?:\.\d{1,4})?$/D', $value) === 1, 'Rate must be null or a canonical nonnegative decimal string with up to four whole and four fractional digits.');
}

function tax_catalog_id(mixed $value, string $country, array &$seen): string
{
    $id = tax_catalog_text($value, 'Identifier');
    tax_catalog_check(preg_match('/^[A-Za-z]{2}-[A-Za-z0-9][A-Za-z0-9._-]{0,125}$/D', $id) === 1 && strcasecmp(substr($id, 0, 2), $country) === 0, 'Identifier must have its own country prefix: ' . $id);
    $key = strtolower($id);
    tax_catalog_check(!isset($seen[$key]), 'Duplicate identifier: ' . $id);
    $seen[$key] = true;
    return $id;
}

function tax_catalog_validate(array $data, string $filename): array
{
    $data = tax_catalog_object($data, ['schema_version', 'country', 'status', 'enabled', 'research_date', 'review_required', 'sources', 'regimes', 'industry_profiles', 'open_questions'], 'Catalog');
    tax_catalog_check($data['schema_version'] === 1, 'Unsupported schema version.');
    tax_catalog_check($data['status'] === 'research_only' && $data['enabled'] === false && $data['review_required'] === true, 'Catalog must remain disabled research requiring review.');
    $country = tax_catalog_object($data['country'], ['code', 'name', 'currency'], 'Country');
    tax_catalog_check(is_string($country['code']) && isset(TAX_CATALOG_COUNTRIES[$country['code']]), 'Unknown schema-1 country code.');
    $code = $country['code'];
    tax_catalog_text($country['name'], 'Country name');
    tax_catalog_check($country['currency'] === TAX_CATALOG_COUNTRIES[$code][1] && $filename === TAX_CATALOG_COUNTRIES[$code][0] . '.json', 'Country currency or filename does not match the catalog.');
    $researchDate = tax_catalog_date($data['research_date'], 'Research date');
    tax_catalog_strings($data['open_questions'], 'Open questions');
    $seen = $sources = [];
    foreach (tax_catalog_list($data['sources'], 'Sources') as $source) {
        $source = tax_catalog_object($source, ['id', 'title', 'url', 'publisher', 'checked_on'], 'Source');
        $id = tax_catalog_id($source['id'], $code, $seen);
        tax_catalog_text($source['title'], 'Source title');
        tax_catalog_text($source['publisher'], 'Source publisher');
        $url = tax_catalog_text($source['url'], 'Source URL');
        $parts = parse_url($url);
        tax_catalog_check(filter_var($url, FILTER_VALIDATE_URL) !== false && is_array($parts) && ($parts['scheme'] ?? '') === 'https' && isset($parts['host']) && !isset($parts['user']) && !isset($parts['pass']), 'Source must have an HTTPS URL without credentials.');
        $checked = tax_catalog_date($source['checked_on'], 'Source check date');
        tax_catalog_check($checked <= $researchDate, 'Source check date cannot follow the catalog research date.');
        $sources[$id] = $source;
    }
    $regimes = [];
    foreach (tax_catalog_list($data['regimes'], 'Regimes') as $regime) {
        $regime = tax_catalog_object($regime, ['id', 'name', 'authority', 'tax_family', 'jurisdiction', 'rate_percent', 'effective_from', 'effective_to', 'applicability', 'industry_codes', 'conditions', 'source_ids', 'review_status'], 'Regime');
        $id = tax_catalog_id($regime['id'], $code, $seen);
        foreach (['name', 'authority', 'jurisdiction', 'applicability'] as $field) { tax_catalog_text($regime[$field], $id . ' ' . $field); }
        tax_catalog_check(in_array($regime['tax_family'], TAX_CATALOG_FAMILIES, true), 'Unknown tax family: ' . $id);
        tax_catalog_check($regime['review_status'] === 'unreviewed', 'Every candidate must remain unreviewed: ' . $id);
        tax_catalog_rate($regime['rate_percent']);
        $from = tax_catalog_date($regime['effective_from'], $id . ' effective_from', true);
        $to = tax_catalog_date($regime['effective_to'], $id . ' effective_to', true);
        tax_catalog_check($from === null || $to === null || $from <= $to, 'Effective date range is reversed: ' . $id);
        // Matching research/commencement dates may be legitimate. Their legal provenance is a human review, not a date inequality.
        tax_catalog_strings($regime['conditions'], $id . ' conditions');
        foreach (tax_catalog_strings($regime['source_ids'], $id . ' sources') as $sourceId) {
            tax_catalog_check(isset($sources[$sourceId]), 'Unresolved or foreign source reference: ' . $sourceId);
        }
        foreach (tax_catalog_strings($regime['industry_codes'], $id . ' industries') as $industry) {
            tax_catalog_check(in_array($industry, TAX_CATALOG_INDUSTRIES, true), 'Unknown industry: ' . $industry);
        }
        $regimes[$id] = $regime;
    }
    $profiles = [];
    foreach (tax_catalog_list($data['industry_profiles'], 'Industry profiles') as $profile) {
        $profile = tax_catalog_object($profile, ['industry_code', 'regime_ids', 'classification_questions'], 'Industry profile');
        $industry = tax_catalog_text($profile['industry_code'], 'Industry code');
        tax_catalog_check(in_array($industry, TAX_CATALOG_INDUSTRIES, true) && !isset($profiles[$industry]), 'Unknown or duplicate industry profile: ' . $industry);
        tax_catalog_strings($profile['classification_questions'], $industry . ' questions', 2);
        foreach (tax_catalog_strings($profile['regime_ids'], $industry . ' regimes') as $id) {
            tax_catalog_check(isset($regimes[$id]), 'Unresolved or foreign regime reference: ' . $id);
            tax_catalog_check(in_array($industry, $regimes[$id]['industry_codes'], true), 'Regime does not cover its referring industry: ' . $id);
        }
        $profiles[$industry] = $profile;
    }
    tax_catalog_check(count($profiles) === count(TAX_CATALOG_INDUSTRIES), 'All seven industry profiles are required.');
    foreach ($regimes as $id => $regime) {
        foreach ($regime['industry_codes'] as $industry) {
            tax_catalog_check(in_array($id, $profiles[$industry]['regime_ids'], true), 'Industry profile omits its declared regime: ' . $id);
        }
    }
    return ['sources' => count($sources), 'regimes' => count($regimes), 'industries' => count($profiles)];
}

function tax_catalog_is_schema(array $data, string $filename): bool
{
    // Only explicitly named JSON Schema documents may be skipped; a broken candidate may not disappear.
    return ($filename === 'schema.json' || str_ends_with($filename, '.schema.json'))
        && !array_key_exists('country', $data) && !array_key_exists('regimes', $data)
        && !array_key_exists('enabled', $data) && !array_key_exists('status', $data)
        && is_string($data['$schema'] ?? null) && str_starts_with($data['$schema'], 'https://json-schema.org/')
        && ($data['type'] ?? null) === 'object' && is_array($data['properties'] ?? null);
}

function tax_catalog_self_test(array $baseline, string $filename): int
{
    $mutations = [
        'activation' => static function (array &$p): void { $p['enabled'] = true; },
        'false-like integer' => static function (array &$p): void { $p['enabled'] = 0; },
        'review bypass' => static function (array &$p): void { $p['review_required'] = false; },
        'active status' => static function (array &$p): void { $p['status'] = 'active'; },
        'unsupported field' => static function (array &$p): void { $p['auto_activate'] = true; },
        'unsupported version' => static function (array &$p): void { $p['schema_version'] = 2; },
        'wrong currency' => static function (array &$p): void { $p['country']['currency'] = 'USD'; },
        'invalid calendar date' => static function (array &$p): void { $p['research_date'] = '2026-02-30'; },
        'future source check' => static function (array &$p): void { $p['sources'][0]['checked_on'] = '9999-12-31'; },
        'insecure source' => static function (array &$p): void { $p['sources'][0]['url'] = 'http://example.invalid/law'; },
        'credential URL' => static function (array &$p): void { $p['sources'][0]['url'] = 'https://example:example@example.invalid/law'; },
        'missing publisher' => static function (array &$p): void { $p['sources'][0]['publisher'] = ' '; },
        'duplicate identifier' => static function (array &$p): void { $p['sources'][] = $p['sources'][0]; },
        'approved regime' => static function (array &$p): void { $p['regimes'][0]['review_status'] = 'approved'; },
        'regime activation' => static function (array &$p): void { $p['regimes'][0]['enabled'] = true; },
        'numeric rate' => static function (array &$p): void { $p['regimes'][0]['rate_percent'] = 15; },
        'rate outside representation' => static function (array &$p): void { $p['regimes'][0]['rate_percent'] = '10000.0000'; },
        'excess rate precision' => static function (array &$p): void { $p['regimes'][0]['rate_percent'] = '1.00001'; },
        'negative rate' => static function (array &$p): void { $p['regimes'][0]['rate_percent'] = '-0.01'; },
        'exponential rate' => static function (array &$p): void { $p['regimes'][0]['rate_percent'] = '1e1'; },
        'unsourced rate' => static function (array &$p): void { $p['regimes'][0]['source_ids'] = []; },
        'foreign source' => static function (array &$p): void { $p['regimes'][0]['source_ids'] = ['ZZ-source']; },
        'missing conditions' => static function (array &$p): void { unset($p['regimes'][0]['conditions']); },
        'empty applicability' => static function (array &$p): void { $p['regimes'][0]['applicability'] = ''; },
        'reversed effective dates' => static function (array &$p): void { $p['regimes'][0]['effective_from'] = '2024-01-01'; $p['regimes'][0]['effective_to'] = '2023-12-31'; },
        'foreign regime' => static function (array &$p): void { $p['industry_profiles'][0]['regime_ids'][] = 'ZZ-regime'; },
        'missing industry' => static function (array &$p): void { array_pop($p['industry_profiles']); },
        'missing questions' => static function (array &$p): void { $p['industry_profiles'][0]['classification_questions'] = []; },
        'unmapped industry' => static function (array &$p): void { $p['regimes'][0]['industry_codes'] = ['unknown_industry']; },
    ];
    foreach ($mutations as $name => $mutate) {
        $candidate = $baseline;
        $mutate($candidate);
        $rejected = false;
        try { tax_catalog_validate($candidate, $filename); } catch (Throwable) { $rejected = true; }
        tax_catalog_check($rejected, 'Validator failed to reject: ' . $name);
    }
    foreach ([null, '0.00', '100.0000', '150.00', '9999.9999', '0.0001'] as $rate) { tax_catalog_rate($rate); }
    $sameDay = $baseline;
    $sameDay['regimes'][0]['effective_from'] = $sameDay['research_date'];
    $sameDay['regimes'][0]['effective_to'] = null;
    tax_catalog_validate($sameDay, $filename);
    tax_catalog_check(tax_catalog_is_schema(['$schema' => 'https://json-schema.org/draft/2020-12/schema', 'type' => 'object', 'properties' => []], 'catalog.schema.json'), 'Expected explicit schema document to be skipped.');
    tax_catalog_check(!tax_catalog_is_schema(['$schema' => 'https://json-schema.org/draft/2020-12/schema', 'type' => 'object', 'properties' => [], 'enabled' => true], 'catalog.schema.json'), 'A candidate cannot hide as a schema document.');
    return count($mutations);
}

try {
    tax_catalog_check(PHP_SAPI === 'cli', 'Run this validator from the command line.');
    $arguments = $argv ?? [];
    tax_catalog_check(array_diff(array_slice($arguments, 1), ['--self-test']) === [], 'Usage: php tools/validate-tax-catalog.php [--self-test]');
    $files = glob(dirname(__DIR__) . '/resources/tax/*.json') ?: [];
    sort($files);
    $countries = $catalogs = [];
    $totals = ['countries' => 0, 'sources' => 0, 'regimes' => 0, 'industry_profiles' => 0, 'schema_documents_skipped' => 0];
    foreach ($files as $file) {
        $filename = basename($file);
        try {
            $data = json_decode((string) file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);
            tax_catalog_check(is_array($data) && !array_is_list($data), 'Catalog must be a JSON object.');
            if (tax_catalog_is_schema($data, $filename)) { ++$totals['schema_documents_skipped']; continue; }
            $counts = tax_catalog_validate($data, $filename);
            tax_catalog_check(!isset($countries[$data['country']['code']]), 'Duplicate country catalog.');
            $countries[$data['country']['code']] = true;
            $catalogs[$filename] = $data;
            ++$totals['countries'];
            $totals['sources'] += $counts['sources'];
            $totals['regimes'] += $counts['regimes'];
            $totals['industry_profiles'] += $counts['industries'];
        } catch (Throwable $error) {
            throw new RuntimeException($filename . ': ' . $error->getMessage(), 0, $error);
        }
    }
    tax_catalog_check(count($countries) === count(TAX_CATALOG_COUNTRIES), 'All eight schema-1 country catalogs are required.');
    echo 'Tax catalog structure valid: ' . json_encode($totals, JSON_THROW_ON_ERROR) . ".\n";
    if (in_array('--self-test', $arguments, true)) {
        $filename = array_key_first($catalogs);
        $count = tax_catalog_self_test($catalogs[$filename], $filename);
        echo 'Validator self-test: ' . $count . " invalid in-memory catalogs rejected; exact rate boundaries and schema-document handling passed.\n";
    }
    echo "All candidates remain disabled and unreviewed. Source accuracy, legal effect and eligibility require qualified human review.\n";
} catch (Throwable $error) {
    fwrite(STDERR, 'Tax catalog validation failed: ' . $error->getMessage() . "\n");
    exit(1);
}
