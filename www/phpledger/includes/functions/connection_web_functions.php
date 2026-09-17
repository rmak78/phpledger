<?php
declare(strict_types=1);

require_once __DIR__ . '/oauth_functions.php';
require_once __DIR__ . '/client_functions.php';
require_once __DIR__ . '/integration_http_functions.php';

function pl_web_connections(int $actor, array $user, array $company, string $method): never
{
    $result = null;
    $message = '';
    try {
        $endpoint = pl_connection_resource();
        if ($method === 'POST') {
            pl_web_assert_scope($company, $_POST);
            pl_connection_rate('connection-create:' . $actor, 10);
            $action = pl_web_text($_POST, 'action');
            if ($action === 'create') {
                $result = pl_create_personal_token($actor, pl_web_text($_POST, 'name'), [['company_id' => (int) $company['id'], 'book_id' => (int) $company['book_id']]], pl_web_text($_POST, 'access_mode', 'reports'));
            } elseif ($action === 'revoke') {
                // A company screen cannot be used to target an unseen connection in another book.
                if (!DB::queryFirstField('SELECT connection_id FROM pl_connection_books WHERE connection_id = %s AND company_id = %i AND book_id = %i', pl_web_text($_POST, 'connection_id'), (int) $company['id'], (int) $company['book_id'])) { throw new DomainException('This connection is not available in the selected book.'); }
                pl_revoke_connection($actor, pl_web_text($_POST, 'connection_id'));
                pl_notice('The connection has been revoked. Its clients must reconnect.');
                pl_redirect('/connections');
            } else { throw new DomainException('Choose a valid connection action.'); }
        }
    } catch (DomainException|OverflowException $error) {
        $message = $error->getMessage();
        http_response_code(422);
    } catch (RuntimeException) {
        $endpoint = '';
        $message = 'The installation owner must configure the public HTTPS URL before connections can be created.';
        http_response_code(503);
    }
    // Tokens are rendered only in this no-store response, never put in flash/session state.
    $input = $method === 'POST' && pl_web_text($_POST, 'action') === 'create' && $result === null
        ? ['name' => pl_web_text($_POST, 'name'), 'access_mode' => pl_web_text($_POST, 'access_mode', 'reports')] : [];
    pl_render('connections', ['title' => 'Connections', 'user' => $user, 'company' => $company, 'endpoint' => $endpoint, 'message' => $message, 'input' => $input, 'issued' => $result, 'connections' => pl_list_connections($actor, (int) $company['id'], (int) $company['book_id'])]);
}

function pl_web_oauth(?int $actor, ?array $user, string $method): never
{
    pl_connection_rate('authorize-source:' . pl_connection_source(), 30);
    if ($method === 'GET' && !isset($_GET['resume'])) {
        if (strlen($_SERVER['QUERY_STRING'] ?? '') > 8192) { throw new DomainException('The connection request is too large.'); }
        $validated = pl_oauth_authorize_validate($_GET);
        // One pending request per browser, content-bound with an unpredictable consent nonce.
        $_SESSION['oauth_pending'] = ['query' => array_intersect_key($_GET, array_flip(['client_id','redirect_uri','response_type','scope','state','code_challenge','code_challenge_method','resource'])), 'nonce' => bin2hex(random_bytes(24)), 'expires_at' => time() + 600];
    }
    $pending = $_SESSION['oauth_pending'] ?? null;
    if (!is_array($pending) || ($pending['expires_at'] ?? 0) <= time()) {
        unset($_SESSION['oauth_pending']);
        throw new DomainException('This consent request expired. Start the connection again in your client.');
    }
    if ($actor === null) {
        pl_notice('Sign in or start your private sample, then review the client connection.');
        pl_redirect('/login');
    }
    $validated = pl_oauth_authorize_validate($pending['query']);
    // Chrome checks form-action across the redirect chain. Permit this validated
    // client's origin on the consent document so the authorization response can return.
    $redirectParts = parse_url((string) $validated->getRedirectUri());
    $redirectOrigin = $redirectParts['scheme'] . '://' . $redirectParts['host'] . (isset($redirectParts['port']) ? ':' . $redirectParts['port'] : '');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self' " . $redirectOrigin . "; frame-ancestors 'none'; base-uri 'none'; object-src 'none'");
    $message = '';
    if ($method === 'POST') {
        if (!hash_equals($pending['nonce'], pl_web_text($_POST, 'consent_nonce'))) { throw new DomainException('The pending connection changed. Review the latest request again.'); }
        if (pl_web_text($_POST, 'decision') === 'deny') {
            unset($_SESSION['oauth_pending']);
            $uri = $validated->getRedirectUri();
            $query = http_build_query(['error' => 'access_denied', 'state' => $validated->getState()], '', '&', PHP_QUERY_RFC3986);
            header('Location: ' . $uri . (str_contains($uri, '?') ? '&' : '?') . $query, true, 303);
            exit;
        }
        try {
            if (pl_web_text($_POST, 'decision') !== 'allow') { throw new DomainException('Choose whether to allow this connection.'); }
            $company = pl_company_context($actor, pl_web_id($_POST, 'company_id'));
            $scopes = array_map(static fn ($scope): string => $scope->getIdentifier(), $validated->getScopes());
            if (!in_array('ledger.read', $scopes, true) || array_diff($scopes, ['ledger.read','offline_access']) !== []) { throw new DomainException('This client must request read access.'); }
            $accessMode = pl_web_text($_POST, 'access_mode', 'reports');
            $response = pl_ledger_transaction(function () use ($actor, $company, $validated, $scopes, $accessMode): Psr\Http\Message\ResponseInterface {
                $connection = pl_create_connection($actor, $validated->getClient()->getIdentifier(), $validated->getClient()->getName(), [['company_id' => (int) $company['id'], 'book_id' => (int) $company['book_id']]], 'oauth', in_array('offline_access', $scopes, true) ? 'ledger.read offline_access' : 'ledger.read', $accessMode);
                $repositories = new PlOAuthRepositories();
                $repositories->connectionId = $connection['id'];
                $validated->setUser(new PlOAuthUser($actor));
                $validated->setAuthorizationApproved(true);
                return pl_oauth_server($repositories)->completeAuthorizationRequest($validated, new Nyholm\Psr7\Response());
            });
            unset($_SESSION['oauth_pending']);
            pl_integration_emit($response->withHeader('Cache-Control', 'no-store'));
        } catch (DomainException $error) {
            $message = $error->getMessage();
            http_response_code(422);
        }
    }
    pl_render('oauth-consent', ['title' => 'Review connection', 'user' => $user, 'client' => $validated->getClient(), 'pending' => $pending, 'message' => $message, 'companies' => pl_list_companies($actor)]);
}
