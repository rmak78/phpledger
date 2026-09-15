<?php
declare(strict_types=1);

use Symfony\Component\Uid\Uuid;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

final class PlMcpSessions implements Mcp\Server\Session\SessionStoreInterface
{
    public function __construct(private readonly string $connectionId) {}
    public function exists(Uuid $id): bool { return $this->read($id) !== false; }
    public function read(Uuid $id): string|false
    {
        return DB::queryFirstField('SELECT state FROM pl_connection_sessions WHERE session_id = %s AND connection_id = %s AND expires_at > UTC_TIMESTAMP()', $id->toRfc4122(), $this->connectionId) ?? false;
    }
    public function write(Uuid $id, string $data): bool
    {
        if (strlen($data) > 65536) { return false; }
        return pl_ledger_transaction(function () use ($id, $data): bool {
            // Serialize session creation per connection and bind every lookup to that connection.
            DB::queryFirstField('SELECT id FROM pl_connections WHERE id = %s FOR UPDATE', $this->connectionId);
            $connection = pl_connection_require($this->connectionId);
            if (!$this->exists($id) && (int) DB::queryFirstField('SELECT COUNT(*) FROM pl_connection_sessions WHERE connection_id = %s AND expires_at > UTC_TIMESTAMP()', $this->connectionId) >= 10) { return false; }
            $expiry = min($connection['expires_at'], gmdate('Y-m-d H:i:s', time() + 1800));
            // Do not overwrite another actor's session, even if a client supplies its UUID.
            DB::query('INSERT INTO pl_connection_sessions (session_id, connection_id, state, expires_at) VALUES (%s, %s, %s, %s) ON DUPLICATE KEY UPDATE state = IF(connection_id = %s, %s, state), expires_at = IF(connection_id = %s, %s, expires_at)', $id->toRfc4122(), $this->connectionId, $data, $expiry, $this->connectionId, $data, $this->connectionId, $expiry);
            return $this->exists($id);
        });
    }
    public function destroy(Uuid $id): bool
    {
        DB::query("UPDATE pl_connection_sessions SET state = '', expires_at = UTC_TIMESTAMP() WHERE session_id = %s AND connection_id = %s", $id->toRfc4122(), $this->connectionId);
        return true;
    }
    public function gc(): array
    {
        if (pl_demo_enabled()) { return []; } // The restricted demo runtime cannot delete; hourly reset collects expired sessions.
        $ids = DB::queryFirstColumn('SELECT session_id FROM pl_connection_sessions WHERE expires_at <= UTC_TIMESTAMP() LIMIT 100');
        if ($ids !== []) { DB::query('DELETE FROM pl_connection_sessions WHERE session_id IN %ls AND expires_at <= UTC_TIMESTAMP()', $ids); }
        return array_map(static fn (string $id): Uuid => Uuid::fromString($id), $ids);
    }
}

final class PlMcpReadTool implements Mcp\Server\Handler\ToolHandlerInterface
{
    public function __construct(private readonly string $connectionId, private readonly string $operation) {}
    public function execute(array $arguments, Mcp\Server\ClientGateway $gateway): mixed
    {
        try {
            $result = pl_read_operation($this->connectionId, $this->operation, $arguments);
            pl_connection_audit($this->connectionId, null, 'read.' . $this->operation, 'success');
            return new Mcp\Schema\Result\CallToolResult([new Mcp\Schema\Content\TextContent(json_encode($result, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES))], false, $result);
        } catch (DomainException|UnexpectedValueException|LengthException $error) {
            pl_connection_audit($this->connectionId, null, 'read.' . $this->operation, 'denied');
            return new Mcp\Schema\Result\CallToolResult([new Mcp\Schema\Content\TextContent($error->getMessage())], true, ['error' => $error->getMessage()]);
        }
    }
}

function pl_mcp_response(ServerRequestInterface $request, array $connection): ResponseInterface
{
    $builder = Mcp\Server::builder()->setServerInfo('PHP Ledger', '0.2.0-preview')
        ->setCapabilities(new Mcp\Schema\ServerCapabilities(tools: true, resources: false, prompts: false))
        ->setInstructions('Read-only accounting preview. Select an explicitly authorized company and book. Amounts are exact decimal strings. Business dates are inclusive; event times are UTC. Follow pagination and source links. Drafts do not affect reports. Reconnect after expiry or demo reset.')
        ->setPaginationLimit(25)->setProtocolVersion(Mcp\Schema\Enum\ProtocolVersion::V2025_11_25)
        ->setModernVersions([Mcp\Schema\Enum\ProtocolVersion::V2026_07_28])
        ->setSession(new PlMcpSessions($connection['id']));
    foreach (pl_read_catalog() as $operation => $definition) {
        $tool = Mcp\Schema\Tool::fromArray(['name' => 'ledger_' . $operation, 'description' => $definition['description'], 'inputSchema' => $definition['schema'], 'annotations' => ['readOnlyHint' => true, 'destructiveHint' => false, 'idempotentHint' => true, 'openWorldHint' => false]]);
        $builder->add($tool, new PlMcpReadTool($connection['id'], $operation));
    }
    $factory = new Nyholm\Psr7\Factory\Psr17Factory();
    // The exact configured Origin and Host are checked by the HTTP boundary before SDK dispatch.
    $hosts = [(string) parse_url(pl_connection_issuer(), PHP_URL_HOST)];
    foreach (pl_connection_origins() as $origin) { $hosts[] = (string) parse_url($origin, PHP_URL_HOST); }
    // Version validation belongs to the SDK's classified protocol leg; a handshake-only
    // version middleware here would reject modern requests or headerless initialize.
    $middleware = [new Mcp\Server\Transport\Http\Middleware\CorsMiddleware(pl_connection_origins()), new Mcp\Server\Transport\Http\Middleware\DnsRebindingProtectionMiddleware(array_unique($hosts))];
    $transport = new Mcp\Server\Transport\StreamableHttpTransport($request, $factory, $factory, new Psr\Log\NullLogger(), $middleware, 65536);
    $response = $builder->build()->run($transport);
    if (strlen((string) $response->getBody()) > 524288) { throw new LengthException('Response exceeds the transport limit. Reduce the page size.'); }
    return $response;
}
