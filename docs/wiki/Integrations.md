# Authorized read integrations

1.0.0 includes `/api/v1/`, native MCP Streamable HTTP at `https://phpledger.com/demo/mcp`, a standalone PHP STDIO bridge and the **Connections** screen. Credentials grant an explicit company/book scope and recheck current permissions on each request. Financial writes are **not** exposed over the API/MCP in 1.0.0.

Use a dedicated personal token for a private client or workflow. Shared chat installations should use per-user OAuth. Tokens are shown once; store them only in private credentials. Demo grants expire at the next real hourly boundary and cannot be refreshed beyond it.

The [versioned setup recipes and current client matrix](https://github.com/rmak78/phpledger/blob/master/docs/INTEGRATIONS.md) give the tested versions, pending clients, exact transports, OAuth/PKCE setup and origin/proxy rules. A disabled n8n workflow and Codex/OpenClaw/Hermes configurations are bundled without credentials.

Upgrade under maintenance using the supplied guide; see `UPGRADE.md` in the release package. Preserve signing/encryption keys with private configuration backups.

Financial write API/MCP access, card processing, bank feeds and offline use are not part of 1.0.0. See [[Module roadmap|Module-Roadmap]] for the sequence.
