<?php
declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

function pl_connection_origins(): array
{
    $issuer = parse_url(pl_connection_issuer());
    $own = $issuer['scheme'] . '://' . $issuer['host'] . (isset($issuer['port']) ? ':' . $issuer['port'] : '');
    $origins = [$own];
    foreach (array_filter(array_map('trim', explode(',', getenv('PL_ALLOWED_ORIGINS') ?: ''))) as $origin) {
        $parts = parse_url($origin);
        if (!$parts || ($parts['scheme'] ?? '') !== 'https' || !isset($parts['host']) || isset($parts['path']) || isset($parts['query']) || isset($parts['fragment']) || isset($parts['user']) || isset($parts['pass'])) { throw new RuntimeException('Configure exact HTTPS origins, without paths or wildcards.'); }
        $origins[] = $origin;
    }
    return array_values(array_unique($origins));
}

function pl_integration_json(array $data, int $status = 200): ResponseInterface
{
    return new Nyholm\Psr7\Response($status, ['Content-Type' => 'application/json; charset=utf-8', 'Cache-Control' => 'no-store'], json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
}

function pl_integration_emit(ResponseInterface $response): never
{
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $name => $values) { foreach ($values as $value) { header($name . ': ' . $value, false); } }
    echo $response->getBody();
    exit;
}

function pl_integration_request(string $path, string $method): ServerRequestInterface
{
    if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 65536 || strlen($_SERVER['QUERY_STRING'] ?? '') > 8192) { throw new LengthException('Request exceeds the size limit.'); }
    $stream = fopen('php://input', 'rb');
    $body = $stream ? stream_get_contents($stream, 65537) : '';
    if ($stream) { fclose($stream); }
    if ($body === false || strlen($body) > 65536) { throw new LengthException('Request exceeds the size limit.'); }
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $request = (new Nyholm\Psr7\ServerRequest($method, pl_connection_issuer() . $path, $headers, $body))->withQueryParams($_GET);
    if ($path === '/oauth/token' || $path === '/oauth/revoke') {
        if (!str_starts_with(strtolower($request->getHeaderLine('Content-Type')), 'application/x-www-form-urlencoded')) { throw new DomainException('Use application/x-www-form-urlencoded for OAuth requests.'); }
        $request = $request->withParsedBody($_POST);
    }
    return $request;
}

function pl_integration_response(string $path, ServerRequestInterface $request): ResponseInterface
{
    $method = $request->getMethod();
    $issuer = pl_connection_issuer();
    $origin = $request->getHeaderLine('Origin');
    $expected = parse_url($issuer, PHP_URL_HOST) . (parse_url($issuer, PHP_URL_PORT) ? ':' . parse_url($issuer, PHP_URL_PORT) : '');
    if (strtolower($request->getHeaderLine('Host')) !== strtolower($expected) || ($origin !== '' && !in_array($origin, pl_connection_origins(), true))) {
        return pl_integration_json(['error' => 'origin_not_allowed'], 403);
    }
    $allowed = match ($path) { '/mcp' => ['GET','POST','DELETE'], '/oauth/token','/oauth/register','/oauth/revoke' => ['POST'], default => ['GET'] };
    if ($method === 'OPTIONS') {
        if (!in_array($request->getHeaderLine('Access-Control-Request-Method'), $allowed, true)) { return pl_integration_json(['error' => 'method_not_allowed'], 405); }
        $headers = array_filter(array_map('trim', explode(',', strtolower($request->getHeaderLine('Access-Control-Request-Headers')))));
        if (array_diff($headers, ['authorization','content-type','accept','mcp-protocol-version','mcp-session-id','mcp-method','mcp-name','last-event-id']) !== []) { return pl_integration_json(['error' => 'headers_not_allowed'], 403); }
        return new Nyholm\Psr7\Response(204, ['Access-Control-Allow-Methods' => implode(', ', $allowed), 'Access-Control-Allow-Headers' => 'Authorization, Content-Type, Accept, MCP-Protocol-Version, Mcp-Session-Id, Mcp-Method, Mcp-Name, Last-Event-ID']);
    }
    if (!in_array($method, $allowed, true)) { return pl_integration_json(['error' => 'method_not_allowed', 'message' => 'Financial writes are not supported.'], 405)->withHeader('Allow', implode(', ', $allowed)); }
    pl_connection_rate('request-source:' . pl_connection_source(), 300);
    if (str_starts_with($path, '/.well-known/oauth-protected-resource')) {
        return pl_integration_json(['resource' => pl_connection_resource(), 'authorization_servers' => [$issuer], 'scopes_supported' => ['ledger.read','offline_access'], 'bearer_methods_supported' => ['header'], 'resource_name' => 'PHP Ledger authorized accounting reads']);
    }
    if (str_starts_with($path, '/.well-known/oauth-authorization-server')) {
        return pl_integration_json(['issuer' => $issuer, 'authorization_endpoint' => $issuer . '/oauth/authorize', 'token_endpoint' => $issuer . '/oauth/token', 'registration_endpoint' => $issuer . '/oauth/register', 'revocation_endpoint' => $issuer . '/oauth/revoke', 'response_types_supported' => ['code'], 'grant_types_supported' => ['authorization_code','refresh_token'], 'token_endpoint_auth_methods_supported' => ['none'], 'code_challenge_methods_supported' => ['S256'], 'scopes_supported' => ['ledger.read','offline_access'], 'client_id_metadata_document_supported' => true]);
    }
    if ($path === '/oauth/register') {
        pl_connection_rate('registration-global', 20);
        pl_connection_rate('registration-source:' . pl_connection_source(), 5);
        if (!str_starts_with(strtolower($request->getHeaderLine('Content-Type')), 'application/json')) { throw new DomainException('Client registration requires application/json.'); }
        $input = json_decode((string) $request->getBody(), true, 16, JSON_THROW_ON_ERROR);
        if (!is_array($input) || array_is_list($input)) { throw new DomainException('Client metadata must be an object.'); }
        return pl_integration_json(pl_client_registration($input), 201);
    }
    if ($path === '/oauth/token') { return pl_oauth_token_response($request); }
    if ($path === '/oauth/revoke') { return pl_oauth_revoke_response($request); }
    if ($path === '/api/v1/openapi.json') { return pl_integration_json(pl_read_openapi()); }
    $connection = pl_connection_authenticate($request);
    pl_connection_rate('connection:' . $connection['id'], 120);
    if ($path === '/mcp') { return pl_mcp_response($request, $connection); }
    $operation = str_replace('-', '_', substr($path, strlen('/api/v1/')));
    if (!isset(pl_read_catalog()[$operation])) { return pl_integration_json(['error' => 'not_found'], 404); }
    $result = pl_read_operation($connection['id'], $operation, pl_read_arguments($operation, $request->getQueryParams(), true));
    pl_connection_audit($connection['id'], $connection['actor_id'], 'read.' . $operation, 'success');
    return pl_integration_json($result);
}

function pl_integration_http(string $path, string $method): never
{
    try {
        // Origin validation must remain available if bootstrap rejects a busy demo.
        require_once __DIR__ . '/connection_functions.php';
        require_once dirname(__DIR__) . '/bootstrap.php';
        require_once __DIR__ . '/oauth_functions.php';
        require_once __DIR__ . '/client_functions.php';
        require_once __DIR__ . '/mcp_functions.php';
        $request = pl_integration_request($path, $method);
        $response = pl_integration_response($path, $request);
    } catch (League\OAuth2\Server\Exception\OAuthServerException $error) {
        $response = $error->generateHttpResponse(new Nyholm\Psr7\Response());
    } catch (PlDemoUnavailable $error) {
        $response = pl_integration_json(['error' => 'demo_refresh', 'message' => 'The sample is refreshing or busy. Retry shortly. After the hourly reset, start a new sample and reconnect your client.'], 503)->withHeader('Retry-After', '10');
    } catch (UnexpectedValueException $error) {
        $response = pl_integration_json(['error' => 'invalid_token', 'message' => $error->getMessage()], 401)
            ->withHeader('WWW-Authenticate', 'Bearer resource_metadata="' . pl_connection_issuer() . '/.well-known/oauth-protected-resource", error="invalid_token"');
    } catch (DomainException|JsonException $error) {
        $response = pl_integration_json(['error' => 'invalid_request', 'message' => $error instanceof JsonException ? 'Invalid JSON.' : $error->getMessage()], 400);
    } catch (LengthException $error) {
        $response = pl_integration_json(['error' => 'size_limit', 'message' => $error->getMessage()], 413);
    } catch (OverflowException $error) {
        $response = pl_integration_json(['error' => 'rate_limit', 'message' => $error->getMessage()], 429)->withHeader('Retry-After', '60');
    } catch (Throwable $error) {
        error_log('PHP Ledger integration unavailable (' . get_class($error) . ').');
        $response = pl_integration_json(['error' => 'unavailable', 'message' => 'Connections are unavailable. Contact the installation owner.'], 503);
    }
    $response = $response->withHeader('Cache-Control', 'no-store')->withHeader('Vary', 'Origin')
        ->withHeader('Access-Control-Expose-Headers', 'WWW-Authenticate, Mcp-Session-Id, MCP-Protocol-Version, Retry-After');
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    try { if ($origin !== '' && in_array($origin, pl_connection_origins(), true)) { $response = $response->withHeader('Access-Control-Allow-Origin', $origin); } } catch (Throwable) {}
    pl_integration_emit($response);
}

function pl_read_openapi(): array
{
    $paths = [];
    foreach (pl_read_catalog() as $operation => $definition) {
        $parameters = [];
        foreach ($definition['schema']['properties'] as $name => $schema) { $parameters[] = ['name' => $name, 'in' => 'query', 'required' => in_array($name, $definition['schema']['required'], true), 'schema' => $schema]; }
        $paths['/api/v1/' . str_replace('_', '-', $operation)] = ['get' => ['operationId' => 'ledger_' . $operation, 'description' => $definition['description'], 'parameters' => $parameters, 'security' => [['bearerAuth' => []]], 'responses' => ['200' => ['description' => 'Authorized read with decimal-string money, UTC event timestamps and bounded collections.', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ReadResult']]]], '400' => ['description' => 'Invalid arguments or denied company/book scope.'], '401' => ['description' => 'Reconnect after credential expiry or revocation.'], '429' => ['description' => 'Retry after one minute.']]]];
    }
    return ['openapi' => '3.1.0', 'info' => ['title' => 'PHP Ledger read API', 'version' => '0.2.1-preview'], 'servers' => [['url' => pl_connection_issuer()]], 'paths' => $paths, 'components' => [
        'securitySchemes' => ['bearerAuth' => ['type' => 'http', 'scheme' => 'bearer']],
        'schemas' => [
            'Money' => ['type' => 'string', 'pattern' => '^-?[0-9]+\\.[0-9]{4}$', 'description' => 'Exact decimal amount; do not convert to binary floating point.', 'examples' => ['123.4567']],
            'Pagination' => ['type' => 'object', 'required' => ['page','page_size','total','pages','next_page'], 'properties' => ['page' => ['type' => 'integer', 'minimum' => 1], 'page_size' => ['type' => 'integer', 'enum' => [25,50,100]], 'total' => ['type' => 'integer', 'minimum' => 0], 'pages' => ['type' => 'integer', 'minimum' => 1], 'next_page' => ['type' => ['integer','null'], 'minimum' => 2]]],
            'ReadResult' => ['type' => 'object', 'required' => ['data','access_expires_at'], 'properties' => ['company_id' => ['type' => 'integer'], 'book_id' => ['type' => 'integer'], 'currency' => ['type' => 'string'], 'access_expires_at' => ['type' => 'string', 'format' => 'date-time'], 'data' => ['type' => 'object', 'description' => 'Operation-specific fields. Money remains decimal strings. Collection pagination is separate from full-report totals. Account running balances precede pagination.', 'additionalProperties' => true]]],
        ],
    ]];
}
