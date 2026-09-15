<?php
declare(strict_types=1);

// Standalone STDIO -> HTTPS bridge. Deliberately does not load the app or database configuration.
if (PHP_SAPI !== 'cli') { exit(1); }
if (!extension_loaded('curl')) { fwrite(STDERR, "PHP curl is required.\n"); exit(2); }
$endpoint = getenv('PL_MCP_URL') ?: '';
$credential = getenv('PL_MCP_TOKEN') ?: '';
$parts = parse_url($endpoint);
$loopback = getenv('PL_BRIDGE_ALLOW_LOOPBACK') === '1' && in_array($parts['host'] ?? '', ['127.0.0.1','localhost'], true);
if (!$parts || !isset($parts['host']) || (($parts['scheme'] ?? '') !== 'https' && !($loopback && ($parts['scheme'] ?? '') === 'http')) || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment']) || isset($parts['query']) || strlen($endpoint) > 512
    || !preg_match('/^[A-Za-z0-9._~-]{20,8192}$/D', $credential)) {
    fwrite(STDERR, "Set PL_MCP_URL to the HTTPS MCP endpoint and PL_MCP_TOKEN to a scoped credential in your private environment.\n");
    exit(2);
}
$session = null;
$protocol = '2025-11-25';
while (($line = fgets(STDIN, 65538)) !== false) {
    $id = null;
    $notification = false;
    try {
        if (strlen($line) > 65536 || !str_ends_with($line, "\n")) { throw new LengthException('MCP input must be a newline-delimited JSON message under 64 KiB.'); }
        $message = json_decode($line, true, 32, JSON_THROW_ON_ERROR);
        if (!is_array($message) || array_is_list($message) || ($message['jsonrpc'] ?? null) !== '2.0' || !is_string($message['method'] ?? null)) { throw new DomainException('Expected one MCP JSON-RPC request or notification.'); }
        $id = $message['id'] ?? null;
        $notification = !array_key_exists('id', $message);
        if ($id !== null && !is_string($id) && !is_int($id)) { throw new DomainException('Invalid request id.'); }
        $modern = $message['params']['_meta']['io.modelcontextprotocol/protocolVersion'] ?? null;
        $version = $modern ?? $protocol;
        if (!in_array($version, ['2025-11-25','2026-07-28'], true)) { throw new DomainException('This bridge supports MCP 2025-11-25 and 2026-07-28.'); }
        $headers = ['Authorization: Bearer ' . $credential, 'Content-Type: application/json', 'Accept: application/json, text/event-stream', 'MCP-Protocol-Version: ' . $version];
        if ($modern !== null) {
            if (!preg_match('/^[a-zA-Z0-9_\/-]{1,80}$/D', $message['method'])) { throw new DomainException('Invalid MCP method.'); }
            $headers[] = 'Mcp-Method: ' . $message['method'];
            if (isset($message['params']['name'])) {
                if (!is_string($message['params']['name']) || !preg_match('/^[a-zA-Z0-9_.-]{1,128}$/D', $message['params']['name'])) { throw new DomainException('Invalid MCP tool name.'); }
                $headers[] = 'Mcp-Name: ' . $message['params']['name'];
            }
        }
        if ($session !== null && $modern === null && $message['method'] !== 'initialize') { $headers[] = 'Mcp-Session-Id: ' . $session; }
        $body = '';
        $responseType = '';
        $nextSession = null;
        $curl = curl_init($endpoint);
        curl_setopt_array($curl, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => $line, CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => $loopback ? CURLPROTO_HTTP | CURLPROTO_HTTPS : CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 60, CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_WRITEFUNCTION => static function ($handle, string $chunk) use (&$body): int {
                if (strlen($body) + strlen($chunk) > 524288) { return 0; }
                $body .= $chunk;
                return strlen($chunk);
            },
            CURLOPT_HEADERFUNCTION => static function ($handle, string $header) use (&$nextSession, &$responseType): int {
                if (stripos($header, 'Mcp-Session-Id:') === 0) { $nextSession = trim(substr($header, 15)); }
                if (stripos($header, 'Content-Type:') === 0) { $responseType = strtolower(trim(substr($header, 13))); }
                return strlen($header);
            }]);
        $success = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        if ($success === false) { throw new RuntimeException('MCP endpoint unavailable or response exceeded the limit.'); }
        if ($status === 401 || $status === 403) { throw new RuntimeException('Access expired, revoked or denied. Reconnect in PHP Ledger Connections and replace the private credential.'); }
        if ($status === 404 && $session !== null) { $session = null; throw new RuntimeException('The MCP session expired. Reinitialize the client; after a demo reset, create a new connection.'); }
        if ($status < 200 || $status >= 300) { throw new RuntimeException('MCP endpoint returned HTTP ' . $status . '. Check connection settings and retry.'); }
        if ($nextSession !== null) {
            if (!preg_match('/^[a-f0-9-]{36}$/Di', $nextSession)) { throw new RuntimeException('Invalid MCP session header.'); }
            $session = $nextSession;
        }
        if ($notification || $body === '') { continue; }
        $responses = [];
        if (str_starts_with($responseType, 'text/event-stream')) {
            foreach (preg_split('/\r?\n\r?\n/', $body) ?: [] as $event) {
                $data = [];
                foreach (preg_split('/\r?\n/', $event) ?: [] as $entry) { if (str_starts_with($entry, 'data:')) { $data[] = ltrim(substr($entry, 5), ' '); } }
                if ($data !== []) { $responses[] = json_decode(implode("\n", $data), true, 64, JSON_THROW_ON_ERROR); }
            }
        } else { $responses[] = json_decode($body, true, 64, JSON_THROW_ON_ERROR); }
        $matched = false;
        foreach ($responses as $response) {
            if (!is_array($response) || ($response['jsonrpc'] ?? '') !== '2.0') { throw new RuntimeException('Endpoint returned an invalid MCP response.'); }
            if (array_key_exists('id', $response) && $response['id'] === $id) {
                $matched = true;
                if ($message['method'] === 'initialize' && isset($response['result']['protocolVersion'])) { $protocol = $response['result']['protocolVersion']; }
                fwrite(STDOUT, json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES) . "\n");
            }
        }
        if (!$matched) { throw new RuntimeException('Endpoint did not return a matching MCP response.'); }
    } catch (Throwable $error) {
        // Never echo headers, credentials, financial results, or arbitrary upstream errors to stderr.
        if (!$notification) {
            $safe = $error instanceof JsonException ? 'Invalid MCP JSON.' : $error->getMessage();
            fwrite(STDOUT, json_encode(['jsonrpc' => '2.0', 'id' => $id, 'error' => ['code' => -32000, 'message' => $safe]], JSON_THROW_ON_ERROR) . "\n");
        }
        if ($error instanceof LengthException) { exit(2); }
    }
}
