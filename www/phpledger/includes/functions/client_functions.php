<?php
declare(strict_types=1);

function pl_client_redirect(string $uri): string
{
    $parts = parse_url($uri);
    if (!$parts || strlen($uri) > 1024 || preg_match('/[\x00-\x20\x7f\\\\]/', $uri) || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment']) || !isset($parts['host']) || filter_var($uri, FILTER_VALIDATE_URL) === false) {
        throw new DomainException('Use an exact HTTPS redirect URI or an HTTP loopback redirect.');
    }
    if (($parts['scheme'] ?? '') !== 'https' && !(($parts['scheme'] ?? '') === 'http' && in_array($parts['host'], ['127.0.0.1','[::1]','localhost'], true))) {
        throw new DomainException('Redirects require HTTPS; only local loopback callbacks may use HTTP.');
    }
    return $uri;
}

function pl_client_registration(array $input, ?string $metadataId = null): array
{
    if (array_diff(array_keys($input), ['client_name','redirect_uris','grant_types','response_types','token_endpoint_auth_method','client_id','client_uri','logo_uri','contacts','scope']) !== []) { throw new DomainException('Unsupported client metadata fields.'); }
    $name = pl_ledger_text($input['client_name'] ?? '', 'Client name', 120);
    $redirects = $input['redirect_uris'] ?? null;
    if (!is_array($redirects) || !array_is_list($redirects) || count($redirects) < 1 || count($redirects) > 10) { throw new DomainException('Register one to ten exact redirect URIs.'); }
    $redirects = array_values(array_unique(array_map(static function ($uri): string {
        if (!is_string($uri)) { throw new DomainException('Redirect URI must be text.'); }
        return pl_client_redirect($uri);
    }, $redirects)));
    if (($input['token_endpoint_auth_method'] ?? 'none') !== 'none' || ($input['response_types'] ?? ['code']) !== ['code']
        || !is_array($input['grant_types'] ?? []) || array_diff($input['grant_types'] ?? [], ['authorization_code','refresh_token']) !== []) {
        throw new DomainException('Use public authorization-code clients with PKCE S256 and optional refresh tokens.');
    }
    if ($metadataId !== null && ($input['client_id'] ?? null) !== $metadataId) { throw new DomainException('Client metadata must identify its exact HTTPS URL.'); }
    $id = $metadataId ?? 'plc_' . bin2hex(random_bytes(24));
    // Global and source limits are applied by HTTP before any metadata fetch or registration.
    if ((int) DB::queryFirstField('SELECT COUNT(*) FROM pl_connection_clients') >= 10000) { throw new OverflowException('Client registration capacity reached. Contact the installation owner.'); }
    $clientRow = ['client_id' => $id, 'name' => $name, 'redirect_uris' => json_encode($redirects, JSON_THROW_ON_ERROR), 'source' => $metadataId === null ? 'registered' : 'metadata', 'expires_at' => gmdate('Y-m-d H:i:s', time() + 90 * 86400)];
    pl_ledger_transaction(function () use ($id, $metadataId, $clientRow): void {
        $existing = DB::queryFirstRow('SELECT * FROM pl_connection_clients WHERE client_id = %s FOR UPDATE', $id);
        if ($existing !== null) {
            if ($metadataId === null || $existing['source'] !== 'metadata' || $existing['revoked_at'] !== null || $existing['expires_at'] > gmdate('Y-m-d H:i:s')) { throw new DomainException('This client registration cannot be replaced.'); }
            // Refreshing expired metadata must never revive credentials from the old registration.
            DB::query('UPDATE pl_connections SET revoked_at = COALESCE(revoked_at, UTC_TIMESTAMP()) WHERE client_id = %s', $id);
            DB::update('pl_connection_clients', $clientRow, 'client_id = %s', $id);
        } else { DB::insert('pl_connection_clients', $clientRow); }
    });
    return ['client_id' => $id, 'client_name' => $name, 'redirect_uris' => $redirects, 'grant_types' => ['authorization_code','refresh_token'], 'response_types' => ['code'], 'token_endpoint_auth_method' => 'none', 'scope' => 'ledger.read offline_access', 'client_id_issued_at' => time()];
}

/** Reject special/private addresses, including IPv4-mapped IPv6, before pinning DNS for curl. */
function pl_client_public_ip(string $ip): bool
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) { return false; }
    if (str_contains($ip, ':')) {
        // Permit ordinary global unicast; exclude IETF special assignments (including Teredo), documentation and 6to4.
        $packed = inet_pton($ip);
        return $packed !== false && (ord($packed[0]) & 0xe0) === 0x20
            && !(substr($packed, 0, 2) === "\x20\x01" && (ord($packed[2]) & 0xfe) === 0)
            && substr($packed, 0, 4) !== "\x20\x01\x0d\xb8" && substr($packed, 0, 2) !== "\x20\x02";
    }
    $long = ip2long($ip);
    return $long !== false && !(($long & 0xffc00000) === 0x64400000) && !(($long & 0xffffff00) === 0xc0000000) && !(($long & 0xfffe0000) === 0xc6120000);
}

/** CIMD fetch cannot redirect, use a proxy, or resolve again after public-address validation. */
function pl_client_metadata(string $url): array
{
    $parts = parse_url($url);
    if (!$parts || strlen($url) > 512 || ($parts['scheme'] ?? '') !== 'https' || ($parts['port'] ?? 443) !== 443 || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])
        || !preg_match('/^(?=.{1,253}$)(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,63}$/D', $parts['host'] ?? '')) {
        throw new DomainException('Client metadata requires a public HTTPS DNS name on port 443.');
    }
    $host = $parts['host'];
    $records = dns_get_record($host, DNS_A | DNS_AAAA);
    $ips = [];
    foreach ($records ?: [] as $record) {
        $ip = $record['ip'] ?? $record['ipv6'] ?? null;
        if ($ip !== null) {
            if (!pl_client_public_ip($ip)) { throw new DomainException('Client metadata cannot resolve to a private or special network.'); }
            $ips[] = $ip;
        }
    }
    if ($ips === [] || count($ips) > 16) { throw new DomainException('Client metadata DNS could not be safely resolved.'); }
    $body = '';
    $curl = curl_init($url);
    curl_setopt_array($curl, [CURLOPT_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROXY => '', CURLOPT_CONNECTTIMEOUT => 2, CURLOPT_TIMEOUT => 4,
        CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_HTTPHEADER => ['Accept: application/json'],
        CURLOPT_RESOLVE => [$host . ':443:' . (str_contains($ips[0], ':') ? '[' . $ips[0] . ']' : $ips[0])],
        CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$body): int {
            if (strlen($body) + strlen($chunk) > 32768) { return 0; }
            $body .= $chunk;
            return strlen($chunk);
        }]);
    $ok = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $type = (string) curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    if (!$ok || $status !== 200 || !str_starts_with(strtolower($type), 'application/json')) { throw new DomainException('Client metadata was unavailable or exceeded its limits.'); }
    $metadata = json_decode($body, true, 16, JSON_THROW_ON_ERROR);
    if (!is_array($metadata) || array_is_list($metadata)) { throw new DomainException('Invalid client metadata.'); }
    return pl_client_registration($metadata, $url);
}

/** Fetch a CIMD document only on an explicit browser connection request, never ordinary page loads. */
function pl_oauth_authorize_validate(array $query): League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface
{
    if (($query['resource'] ?? null) !== pl_connection_resource() || ($query['code_challenge_method'] ?? null) !== 'S256'
        || !is_string($query['code_challenge'] ?? null) || !preg_match('/^[A-Za-z0-9_-]{43}$/D', $query['code_challenge'])
        || !is_string($query['client_id'] ?? null) || strlen($query['client_id']) > 512
        || !is_string($query['redirect_uri'] ?? null) || !is_string($query['state'] ?? null) || strlen($query['state']) < 1 || strlen($query['state']) > 512) {
        throw new DomainException('Use authorization code with PKCE S256, state, an exact redirect and the PHP Ledger resource URL.');
    }
    $repositories = new PlOAuthRepositories();
    if (!$repositories->getClientEntity($query['client_id']) && str_starts_with($query['client_id'], 'https://')) {
        pl_connection_rate('metadata-global', 20);
        pl_connection_rate('metadata-source:' . pl_connection_source(), 5);
        pl_client_metadata($query['client_id']);
    }
    $client = $repositories->getClientEntity($query['client_id']);
    if (!$client || !in_array(pl_client_redirect($query['redirect_uri']), (array) $client->getRedirectUri(), true)) { throw new DomainException('The redirect URI does not exactly match this client registration.'); }
    $request = (new Nyholm\Psr7\ServerRequest('GET', pl_connection_issuer() . '/oauth/authorize'))->withQueryParams($query);
    return pl_oauth_server($repositories)->validateAuthorizationRequest($request);
}
