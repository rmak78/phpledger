# Integration recipes, revision 1

These files target the PHP Ledger **0.2.1-preview candidate**, not the published 0.1.5 demo. Replace example URLs/IDs and absolute paths. No exported file contains credentials. Keep populated private client settings out of source control and exported workflows.

See [the integration guide](../../../docs/INTEGRATIONS.md) for schemas, OAuth setup, transport settings and the acceptance matrix. Documentation-derived recipes are not verified application compatibility. Record the actual client version after testing.

`n8n-manual-report.json` is inactive and its native MCP node is disabled. Set the endpoint, explicit company/book IDs and the dedicated bearer credential before enabling that node for a manual run. It formats a report locally with a Code node, with no email/chat/provider-send node and no language-model call. The generic example's zero IDs fail validation until deliberately replaced.

OAuth clients use the resource `<application URL>/mcp`, S256, `ledger.read` and optional `offline_access`. For a shared deployment, each person consents separately. For a demo, reconnect after the next hourly reset. Preserve exact decimal strings.

The native MCP examples do not require an OpenAPI adapter. The separate `/api/v1/openapi.json` describes GET operations for workflow clients that use OpenAPI; that fallback has its own acceptance result.
