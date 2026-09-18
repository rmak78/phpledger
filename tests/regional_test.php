<?php
declare(strict_types=1);

test('country suggestions use explicit trusted proxies and reject nonpublic or malformed addresses', function (): void {
    assert_same('8.8.8.8', pl_country_lookup_ip(['REMOTE_ADDR' => '8.8.8.8', 'HTTP_X_FORWARDED_FOR' => '1.1.1.1'], ''));
    assert_same('8.8.8.8', pl_country_lookup_ip(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_X_FORWARDED_FOR' => '8.8.8.8'], '127.0.0.1'));
    assert_same('1.1.1.1', pl_country_lookup_ip(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_X_FORWARDED_FOR' => '8.8.8.8, 1.1.1.1'], '127.0.0.1'));
    assert_same(null, pl_country_lookup_ip(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_X_FORWARDED_FOR' => '8.8.8.8, 10.0.0.1'], '127.0.0.1'));
    assert_same('8.8.8.8', pl_country_lookup_ip(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_X_FORWARDED_FOR' => '8.8.8.8, 10.0.0.1'], '127.0.0.1,10.0.0.1'));
    assert_same(null, pl_country_lookup_ip(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_X_FORWARDED_FOR' => 'invalid, 8.8.8.8'], '127.0.0.1'));
    assert_same(null, pl_country_lookup_ip(['REMOTE_ADDR' => '1.1.1.1'], '1.1.1.1'));
    assert_same(null, pl_country_lookup_ip(['REMOTE_ADDR' => '1.1.1.1', 'HTTP_X_FORWARDED_FOR' => '8.8.8.8'], '1.1.1.1,8.8.8.8'));
    foreach (['127.0.0.1', '10.2.3.4', '192.168.0.1', '100.64.1.2', '192.0.2.1', '198.51.100.1', '203.0.113.1', '::1', 'fc00::1', '::ffff:127.0.0.1', '8.8.8.8:443', 'not-an-ip'] as $address) {
        assert_same(null, pl_country_lookup_ip(['REMOTE_ADDR' => $address], ''));
    }
    assert_same('8.8.8.8', pl_normalize_ip('::ffff:8.8.8.8'));
});

test('country defaults retain unsupported and multiple currencies without pretending they are USD', function (): void {
    assert_same('PKR', pl_country_defaults('PK')['currency']);
    assert_same('Pakistan', pl_country_defaults('PK')['country_name']);
    assert_same('ur-Arab-PK', pl_country_defaults('PK')['locale']);
    assert_same('INR', pl_country_defaults('IN')['currency']);
    assert_same('GBP', pl_country_defaults('GB')['currency']);
    assert_same('EUR', pl_country_defaults('IE')['currency']);
    assert_same('CAD', pl_country_defaults('CA')['country_currency']);
    assert_same(null, pl_country_defaults('CA')['currency']);
    assert_same(false, pl_country_defaults('CA')['currency_supported']);
    assert_same(null, pl_country_defaults('PA')['currency']);
    assert_true(count(pl_country_defaults('PA')['country_currencies']) > 1);
    assert_same(null, pl_country_defaults('ZZ')['country_code']);
    assert_same(null, pl_country_defaults('not a country')['country_code']);
});

test('successful country lookup runs once per session and survives login and logout rotation', function (): void {
    pl_session_start(false);
    unset($_SESSION['regional_suggestion']);
    $calls = 0;
    $fetcher = static function (string $ip) use (&$calls): string {
        assert_same('8.8.8.8', $ip);
        $calls++;
        return 'PK';
    };
    $first = pl_regional_suggestion(['REMOTE_ADDR' => '8.8.8.8'], $fetcher);
    assert_same('detected', $first['status']);
    assert_same('PKR', $first['currency']);
    assert_true(!array_key_exists('ip', $_SESSION['regional_suggestion']));
    pl_login_session(['id' => 999999]);
    assert_same($first, pl_regional_suggestion(['REMOTE_ADDR' => '1.1.1.1'], $fetcher));
    pl_logout_session();
    assert_same($first, pl_regional_suggestion(['REMOTE_ADDR' => '1.1.1.1'], $fetcher));
    assert_same(1, $calls);
});

test('new Asian country defaults and cached suggestions use their supported base currency without another lookup', function (): void {
    $expected = [
        'MY' => ['Malaysia', 'ms-Latn-MY', 'MYR'],
        'BD' => ['Bangladesh', 'bn-Beng-BD', 'BDT'],
        'LK' => ['Sri Lanka', 'si-Sinh-LK', 'LKR'],
        'NP' => ['Nepal', 'ne-Deva-NP', 'NPR'],
        'SG' => ['Singapore', 'en-Latn-SG', 'SGD'],
    ];
    foreach ($expected as $code => [$name, $locale, $currency]) {
        $defaults = pl_country_defaults($code);
        assert_same($name, $defaults['country_name']);
        assert_same($locale, $defaults['locale']);
        assert_same([$currency], $defaults['country_currencies']);
        assert_same($currency, $defaults['currency']);
        assert_same(true, $defaults['currency_supported']);
        assert_true(str_contains(pl_base_currency_options()[$currency], $name));
        $_SESSION['regional_suggestion'] = array_replace($defaults, ['currency' => null, 'currency_supported' => false, 'source' => 'country.is', 'status' => 'detected']);
        $calls = 0;
        $refreshed = pl_regional_suggestion(['REMOTE_ADDR' => '8.8.8.8'], static function (string $ip) use (&$calls): string {
            $calls++;
            return 'US';
        });
        assert_same(0, $calls);
        assert_same($currency, $refreshed['currency']);
        assert_same($code, $refreshed['country_code']);
        assert_same('detected', $refreshed['status']);
    }
    unset($_SESSION['regional_suggestion']);
});

test('failed country lookup is cached and local addresses never call the provider', function (): void {
    unset($_SESSION['regional_suggestion']);
    $calls = 0;
    $failure = static function (string $ip) use (&$calls): never {
        $calls++;
        throw new RuntimeException('Sample network failure');
    };
    assert_same('unavailable', pl_regional_suggestion(['REMOTE_ADDR' => '8.8.8.8'], $failure)['status']);
    assert_same('unavailable', pl_regional_suggestion(['REMOTE_ADDR' => '8.8.8.8'], $failure)['status']);
    assert_same(1, $calls);
    unset($_SESSION['regional_suggestion']);
    assert_same('not_public', pl_regional_suggestion(['REMOTE_ADDR' => '127.0.0.1'], $failure)['status']);
    assert_same(1, $calls);
    assert_same(null, pl_fetch_country_code('127.0.0.1'));
    unset($_SESSION['regional_suggestion']);
});

test('demo configuration and service guards refuse ordinary databases and normal administration', function (): void {
    require_once PL_APP . '/includes/functions/web_functions.php';
    $environment = getenv('PL_ENV');
    $localHttp = getenv('PL_DEMO_LOCAL_HTTP');
    $f = ledger_fixture();
    try {
        putenv('PL_DEMO_LOCAL_HTTP=1');
        assert_same(false, pl_web_local_demo_http(['HTTP_HOST' => 'localhost', 'REMOTE_ADDR' => '127.0.0.1']));
        assert_throws(fn () => pl_demo_validate_configuration(['database' => 'phpledger_demo', 'user' => 'app']), RuntimeException::class);
        putenv('PL_ENV=demo');
        foreach (['127.0.0.1', '10.204.82.1', '172.16.0.1', '192.168.1.1', '::1', 'fd00::1', '::ffff:127.0.0.1'] as $peer) {
            assert_same(true, pl_web_local_demo_http(['HTTP_HOST' => 'localhost:18202', 'REMOTE_ADDR' => $peer]));
        }
        foreach (['8.8.8.8', '172.15.0.1', '172.32.0.1', '192.0.2.1', '2001:4860:4860::8888', '', 'invalid'] as $peer) {
            assert_same(false, pl_web_local_demo_http(['HTTP_HOST' => 'localhost', 'REMOTE_ADDR' => $peer]));
        }
        assert_same(false, pl_web_local_demo_http(['HTTP_HOST' => 'phpledger.com', 'REMOTE_ADDR' => '127.0.0.1']));
        putenv('PL_DEMO_LOCAL_HTTP=0');
        assert_same(false, pl_web_local_demo_http(['HTTP_HOST' => 'localhost', 'REMOTE_ADDR' => '127.0.0.1']));
        assert_throws(fn () => pl_demo_validate_configuration(['database' => 'phpledger_test', 'user' => 'app']), RuntimeException::class);
        assert_throws(fn () => pl_demo_validate_configuration(['database' => 'phpledger_demo', 'user' => 'root']), RuntimeException::class);
        pl_demo_validate_configuration(['database' => 'phpledger_demo', 'user' => 'restricted-demo']);
        assert_throws(fn () => pl_create_user('blocked@example.invalid', 'Blocked', 'Sample blocked passphrase'), DomainException::class);
        assert_throws(fn () => pl_create_company($f['actor_id'], 'Blocked', 'USD', '2026-09-14'), DomainException::class);
        assert_throws(fn () => pl_require_company_access($f['actor_id'], $f['company_id']), DomainException::class);
    } finally {
        putenv('PL_ENV=' . $environment);
        putenv($localHttp === false ? 'PL_DEMO_LOCAL_HTTP' : 'PL_DEMO_LOCAL_HTTP=' . $localHttp);
    }
});
