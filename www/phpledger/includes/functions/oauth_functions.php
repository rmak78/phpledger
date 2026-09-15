<?php
declare(strict_types=1);

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities as OAuthEntity;
use League\OAuth2\Server\Entities\Traits as OAuthTrait;
use League\OAuth2\Server\Repositories as OAuthRepository;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class PlOAuthClient implements OAuthEntity\ClientEntityInterface
{
    use OAuthTrait\EntityTrait, OAuthTrait\ClientTrait;
    public function __construct(array $row)
    {
        $this->identifier = $row['client_id'];
        $this->name = $row['name'];
        $this->redirectUri = json_decode($row['redirect_uris'], true, 16, JSON_THROW_ON_ERROR);
    }
    public function supportsGrantType(string $grantType): bool { return in_array($grantType, ['authorization_code', 'refresh_token'], true); }
}

final class PlOAuthScope implements OAuthEntity\ScopeEntityInterface
{
    use OAuthTrait\EntityTrait, OAuthTrait\ScopeTrait;
    public function __construct(string $id) { $this->identifier = $id; }
}

final class PlOAuthUser implements OAuthEntity\UserEntityInterface
{
    use OAuthTrait\EntityTrait;
    public function __construct(int $id) { $this->identifier = (string) $id; }
}

final class PlOAuthAccess implements OAuthEntity\AccessTokenEntityInterface
{
    use OAuthTrait\EntityTrait, OAuthTrait\TokenEntityTrait, OAuthTrait\AccessTokenTrait;
    public function __construct(public readonly string $connectionId) {}
    public function toString(): string
    {
        $this->initJwtConfiguration();
        return $this->jwtConfiguration->builder()->issuedBy(pl_connection_issuer())
            ->permittedFor(pl_connection_resource())->identifiedBy($this->getIdentifier())
            ->issuedAt(new DateTimeImmutable())->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())->relatedTo((string) $this->getUserIdentifier())
            ->withClaim('client_id', $this->getClient()->getIdentifier())->withClaim('connection_id', $this->connectionId)
            ->withClaim('scopes', $this->getScopes())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey())->toString();
    }
}

final class PlOAuthCode implements OAuthEntity\AuthCodeEntityInterface
{
    use OAuthTrait\EntityTrait, OAuthTrait\TokenEntityTrait, OAuthTrait\AuthCodeTrait;
}

final class PlOAuthRefresh implements OAuthEntity\RefreshTokenEntityInterface
{
    use OAuthTrait\EntityTrait, OAuthTrait\RefreshTokenTrait;
}

/** Request-scoped repositories on the existing MeekroDB connection. */
final class PlOAuthRepositories implements OAuthRepository\ClientRepositoryInterface, OAuthRepository\ScopeRepositoryInterface, OAuthRepository\AccessTokenRepositoryInterface, OAuthRepository\AuthCodeRepositoryInterface, OAuthRepository\RefreshTokenRepositoryInterface
{
    public ?string $connectionId = null;
    public ?string $replayConnectionId = null;
    private bool $issueRefresh = false;

    public function getClientEntity(string $clientIdentifier): ?OAuthEntity\ClientEntityInterface
    {
        $row = DB::queryFirstRow("SELECT * FROM pl_connection_clients WHERE client_id = %s AND source <> 'personal' AND revoked_at IS NULL AND (expires_at IS NULL OR expires_at > UTC_TIMESTAMP()) FOR SHARE", $clientIdentifier);
        return $row ? new PlOAuthClient($row) : null;
    }
    public function validateClient(string $clientIdentifier, ?string $clientSecret, ?string $grantType): bool
    {
        // Public clients use S256 PKCE. Dedicated unattended jobs use scoped personal credentials.
        return ($clientSecret === null || $clientSecret === '') && $this->getClientEntity($clientIdentifier) !== null && in_array($grantType, ['authorization_code','refresh_token'], true);
    }
    public function getScopeEntityByIdentifier(string $identifier): ?OAuthEntity\ScopeEntityInterface
    {
        return in_array($identifier, ['ledger.read','offline_access'], true) ? new PlOAuthScope($identifier) : null;
    }
    public function finalizeScopes(array $scopes, string $grantType, OAuthEntity\ClientEntityInterface $clientEntity, ?string $userIdentifier = null, ?string $authCodeId = null): array
    {
        $ids = array_map(static fn ($scope): string => $scope->getIdentifier(), $scopes);
        if (!in_array('ledger.read', $ids, true) || array_diff($ids, ['ledger.read','offline_access']) !== []) { throw OAuthServerException::invalidScope('ledger.read'); }
        if ($this->connectionId !== null) {
            $connection = $this->boundConnection($clientEntity, $userIdentifier);
            if (array_diff($ids, explode(' ', $connection['oauth_scopes'])) !== []) { throw OAuthServerException::invalidScope('offline_access'); }
        }
        return $scopes;
    }
    private function boundConnection(OAuthEntity\ClientEntityInterface $client, ?string $user): array
    {
        if ($this->connectionId === null) { throw OAuthServerException::invalidGrant('Connection is unavailable. Reconnect.'); }
        $connection = pl_connection_require($this->connectionId);
        if ($connection['kind'] !== 'oauth' || $connection['client_id'] !== $client->getIdentifier() || (string) $connection['actor_id'] !== $user) {
            throw OAuthServerException::invalidGrant('Connection does not match this client and user.');
        }
        return $connection;
    }
    public function getNewToken(OAuthEntity\ClientEntityInterface $clientEntity, array $scopes, ?string $userIdentifier = null): OAuthEntity\AccessTokenEntityInterface
    {
        $connection = $this->boundConnection($clientEntity, $userIdentifier);
        $token = new PlOAuthAccess($connection['id']);
        $token->setClient($clientEntity);
        $token->setUserIdentifier($userIdentifier);
        foreach ($scopes as $scope) { $token->addScope($scope); }
        $this->issueRefresh = in_array('offline_access', array_map(static fn ($scope): string => $scope->getIdentifier(), $scopes), true);
        return $token;
    }
    public function persistNewAccessToken(OAuthEntity\AccessTokenEntityInterface $accessTokenEntity): void { $this->persist($accessTokenEntity, 'access'); }
    public function revokeAccessToken(string $tokenId): void { $this->revoke($tokenId, 'access'); }
    public function isAccessTokenRevoked(string $tokenId): bool
    {
        try { pl_connection_token($tokenId, 'access'); return false; }
        catch (UnexpectedValueException) { return true; }
    }
    public function getNewAuthCode(): OAuthEntity\AuthCodeEntityInterface { return new PlOAuthCode(); }
    public function persistNewAuthCode(OAuthEntity\AuthCodeEntityInterface $authCodeEntity): void { $this->persist($authCodeEntity, 'code'); }
    public function revokeAuthCode(string $codeId): void { $this->revoke($codeId, 'code'); }
    public function isAuthCodeRevoked(string $codeId): bool { return $this->redeemable($codeId, 'code'); }
    public function getNewRefreshToken(): ?OAuthEntity\RefreshTokenEntityInterface
    {
        return $this->issueRefresh ? new PlOAuthRefresh() : null;
    }
    public function persistNewRefreshToken(OAuthEntity\RefreshTokenEntityInterface $refreshTokenEntity): void { $this->persist($refreshTokenEntity, 'refresh'); }
    public function revokeRefreshToken(string $tokenId): void { $this->revoke($tokenId, 'refresh'); }
    public function isRefreshTokenRevoked(string $tokenId): bool { return $this->redeemable($tokenId, 'refresh'); }
    private function persist(OAuthEntity\AccessTokenEntityInterface|OAuthEntity\AuthCodeEntityInterface|OAuthEntity\RefreshTokenEntityInterface $token, string $kind): void
    {
        $connection = pl_connection_require($this->connectionId ?? '');
        $expiry = min($token->getExpiryDateTime(), new DateTimeImmutable($connection['expires_at'], new DateTimeZone('UTC')));
        $token->setExpiryDateTime($expiry);
        pl_connection_save_token($token->getIdentifier(), $connection, $kind, $expiry->format('Y-m-d H:i:s'));
    }
    private function revoke(string $id, string $kind): void
    {
        DB::query('UPDATE pl_connection_tokens SET revoked_at = COALESCE(revoked_at, UTC_TIMESTAMP()) WHERE token_hash = %s AND kind = %s', hash('sha256', $id), $kind);
    }
    private function redeemable(string $id, string $kind): bool
    {
        // The token endpoint transaction holds this lock until old-token revocation and new-token insertion commit together.
        $row = DB::queryFirstRow('SELECT * FROM pl_connection_tokens WHERE token_hash = %s AND kind = %s FOR UPDATE', hash('sha256', $id), $kind);
        if ($row && $row['revoked_at'] !== null) { $this->replayConnectionId = $row['connection_id']; }
        if (!$row || $row['revoked_at'] !== null || $row['expires_at'] <= gmdate('Y-m-d H:i:s')) { return true; }
        try { pl_connection_require($row['connection_id']); }
        catch (UnexpectedValueException) { return true; }
        $this->connectionId = $row['connection_id'];
        return false;
    }
}

function pl_oauth_key_path(string $name): string
{
    return rtrim(getenv('PL_OAUTH_KEY_DIRECTORY') ?: PL_APP . '/storage/oauth', '/\\') . '/' . $name;
}

function pl_oauth_server(PlOAuthRepositories $repositories): AuthorizationServer
{
    $encryption = @file_get_contents(pl_oauth_key_path('encryption.key'));
    if (!is_string($encryption) || strlen($encryption) !== 64 || !ctype_xdigit($encryption) || !is_file(pl_oauth_key_path('private.key'))) {
        throw new RuntimeException('OAuth signing keys are not configured. Run tools/setup-oauth.php on the installation.');
    }
    $server = new AuthorizationServer($repositories, $repositories, $repositories, pl_oauth_key_path('private.key'), hex2bin($encryption));
    $code = new League\OAuth2\Server\Grant\AuthCodeGrant($repositories, $repositories, new DateInterval('PT5M'));
    $code->setRefreshTokenTTL(new DateInterval('P30D'));
    $refresh = new League\OAuth2\Server\Grant\RefreshTokenGrant($repositories);
    $refresh->setRefreshTokenTTL(new DateInterval('P30D'));
    $server->enableGrantType($code, new DateInterval('PT15M'));
    $server->enableGrantType($refresh, new DateInterval('PT15M'));
    $server->setDefaultScope('ledger.read');
    return $server;
}

function pl_oauth_token_response(ServerRequestInterface $request): ResponseInterface
{
    $body = $request->getParsedBody();
    if (!is_array($body) || ($body['resource'] ?? null) !== pl_connection_resource()) { throw OAuthServerException::invalidRequest('resource', 'Use the exact PHP Ledger MCP resource URL.'); }
    $repositories = new PlOAuthRepositories();
    $server = pl_oauth_server($repositories);
    DB::startTransaction();
    try {
        $response = $server->respondToAccessTokenRequest($request, new Nyholm\Psr7\Response());
        pl_connection_audit($repositories->connectionId, null, 'oauth.token', 'success');
        DB::commit();
        return $response;
    } catch (Throwable $error) {
        DB::rollback();
        // A replay must invalidate the family even though failed issuance is rolled back.
        if ($repositories->replayConnectionId !== null) {
            DB::query('UPDATE pl_connections SET revoked_at = COALESCE(revoked_at, UTC_TIMESTAMP()) WHERE id = %s', $repositories->replayConnectionId);
            pl_connection_audit($repositories->replayConnectionId, null, 'oauth.replay', 'revoked');
        }
        throw $error;
    }
}

function pl_connection_authenticate(ServerRequestInterface $request): array
{
    $header = $request->getHeaderLine('Authorization');
    if (strlen($header) > 8192 || !preg_match('/^Bearer ([A-Za-z0-9._~-]+)$/Di', $header, $match)) { throw new UnexpectedValueException('A scoped bearer credential is required. Connect from PHP Ledger Connections.'); }
    $token = $match[1];
    if (str_starts_with($token, 'plp_')) { return pl_connection_token($token, 'personal'); }
    try {
        $server = new League\OAuth2\Server\ResourceServer(new PlOAuthRepositories(), pl_oauth_key_path('public.key'));
        $validated = $server->validateAuthenticatedRequest($request);
        // Parse claims only AFTER League verifies the signature, times and durable revocation state.
        $jwt = (new Lcobucci\JWT\Token\Parser(new Lcobucci\JWT\Encoding\JoseEncoder()))->parse($token);
        if (!$jwt instanceof Lcobucci\JWT\UnencryptedToken) { throw new UnexpectedValueException(); }
        $claims = $jwt->claims();
        $connection = pl_connection_token($validated->getAttribute('oauth_access_token_id'), 'access');
        if ($claims->get('iss') !== pl_connection_issuer() || $claims->get('aud') !== [pl_connection_resource()]
            || $claims->get('client_id') !== $connection['client_id'] || $claims->get('connection_id') !== $connection['id']
            || $claims->get('sub') !== (string) $connection['actor_id'] || !in_array('ledger.read', $claims->get('scopes', []), true)) { throw new UnexpectedValueException(); }
        return $connection;
    } catch (Throwable) {
        throw new UnexpectedValueException('Credential expired, revoked or invalid for this resource. Reconnect from PHP Ledger Connections.');
    }
}

/** Decode only authenticated refresh envelopes; never accept caller-supplied token identifiers. */
final class PlOAuthRefreshEnvelope
{
    use League\OAuth2\Server\CryptTrait;
    public function read(string $token): array
    {
        $key = @file_get_contents(pl_oauth_key_path('encryption.key'));
        if (!is_string($key) || strlen($key) !== 64 || !ctype_xdigit($key)) { throw new RuntimeException('OAuth encryption key is unavailable.'); }
        $this->setEncryptionKey(hex2bin($key));
        return json_decode($this->decrypt($token), true, 16, JSON_THROW_ON_ERROR);
    }
}

/** RFC 7009 public-client revocation, including expired tokens. Revoke the entire grant. */
function pl_oauth_revoke_response(ServerRequestInterface $request): ResponseInterface
{
    $body = $request->getParsedBody();
    if (!is_array($body) || !is_string($body['token'] ?? null) || $body['token'] === '' || strlen($body['token']) > 8192
        || !is_string($body['client_id'] ?? null) || $body['client_id'] === '' || strlen($body['client_id']) > 512) {
        throw OAuthServerException::invalidRequest('token', 'Supply the issued token and public client_id.');
    }
    if (($body['client_secret'] ?? '') !== '' || $request->getHeaderLine('Authorization') !== '') { throw OAuthServerException::invalidClient($request); }
    $client = DB::queryFirstRow("SELECT client_id FROM pl_connection_clients WHERE client_id = %s AND source <> 'personal'", $body['client_id']);
    if (!$client) { throw OAuthServerException::invalidClient($request); }
    $id = null;
    $kind = null;
    // Expiry does not prevent revocation. Signature/envelope authenticity and client binding still do.
    try {
        if (substr_count($body['token'], '.') === 2) {
            $jwt = (new Lcobucci\JWT\Token\Parser(new Lcobucci\JWT\Encoding\JoseEncoder()))->parse($body['token']);
            $signer = new Lcobucci\JWT\Signer\Rsa\Sha256();
            if ($jwt instanceof Lcobucci\JWT\UnencryptedToken && $jwt->headers()->get('alg') === 'RS256'
                && $signer->verify($jwt->signature()->hash(), $jwt->payload(), Lcobucci\JWT\Signer\Key\InMemory::file(pl_oauth_key_path('public.key')))
                && $jwt->claims()->get('iss') === pl_connection_issuer() && $jwt->claims()->get('aud') === [pl_connection_resource()]) {
                if ($jwt->claims()->get('client_id') !== $client['client_id']) { throw OAuthServerException::invalidClient($request); }
                $id = $jwt->claims()->get('jti');
                $kind = 'access';
            }
        } else {
            $payload = (new PlOAuthRefreshEnvelope())->read($body['token']);
            if (($payload['client_id'] ?? null) !== $client['client_id']) { throw OAuthServerException::invalidClient($request); }
            $id = $payload['refresh_token_id'] ?? null;
            $kind = 'refresh';
        }
    } catch (OAuthServerException $error) { throw $error; }
    catch (Throwable) { /* Invalid and already absent tokens have the same successful response. */ }
    if (is_string($id) && $kind !== null) {
        pl_ledger_transaction(function () use ($id, $kind, $client): void {
            $row = DB::queryFirstRow('SELECT c.id, c.actor_id FROM pl_connections c JOIN pl_connection_tokens t ON t.connection_id = c.id WHERE t.token_hash = %s AND t.kind = %s AND c.client_id = %s FOR UPDATE', hash('sha256', $id), $kind, $client['client_id']);
            if ($row) {
                DB::query('UPDATE pl_connections SET revoked_at = COALESCE(revoked_at, UTC_TIMESTAMP()) WHERE id = %s', $row['id']);
                pl_connection_end_sessions($row['id']);
                pl_connection_audit($row['id'], (int) $row['actor_id'], 'oauth.revoked', 'success');
            }
        });
    }
    return new Nyholm\Psr7\Response(200, ['Cache-Control' => 'no-store']);
}
