# Internal security review — installer, updater and maintenance barrier (18 September 2026)

This is an internal source review performed by an automated agent reading the checkout before the 1.0.0 release. It included no live-host, network, TLS, web-server or penetration testing and no fuzzing. It does **not** constitute or replace the independent security review that the [roadmap](../ROADMAP.md#current-delivery-contract-first-stable-10) still requires before that gate can be called met.

## Scope

`www/phpledger/public/index.php` (`/install` dispatch), `public/maintenance.php`, `includes/bootstrap.php`, `install_functions.php`, `install_web_functions.php`, `install_oauth_functions.php`, `installation_state_functions.php`, `update_functions.php`, `update_database_functions.php`, `update_web_functions.php`, `update_probe_functions.php`, `update_download_functions.php`, `templates/views/install.php`, `public/assets/install.js`, `tools/resume-update.php`, `tools/sign-update.php`, `install/{migrate,preflight,create-admin}.php` and their tests. Each request path was traced end to end against the threat model in [INSTALLER.md](../INSTALLER.md) and [UPGRADE.md](../../resources/release/UPGRADE.md).

## Findings

| # | Severity | Title | Status |
|---|---|---|---|
| 1 | Medium | No attempt throttling on the installation operator key at `/maintenance.php` | Fixed: `pl_update_operator_attempt()` limits browser attempts to 10 per 15 minutes with an atomic private counter; `tools/resume-update.php` remains unthrottled so remote guessing cannot lock the host operator out of command-line recovery. Regression tests added. |
| 2 | Low | Downgrade protection lost when the installed `PACKAGE-MANIFEST.json` version is absent or malformed (`version_compare($x, '', '>')` is always true) | Fixed: `pl_update_verify_metadata()` requires a well-formed installed version; the official-download path null-coalesces the manifest version. Regression tests added. |
| 3 | Low | Unauthenticated multipart bodies are buffered to `upload_tmp_dir` by PHP before the CSRF/operator checks run | Open recommendation: web-server request-body limit for `/maintenance.php` and a quota'd upload directory. |
| 4 | Low | Unauthenticated `/install` requests take `install.lock`; failed setup-key attempts consume the 10-attempt window for everyone | Open recommendation: keep `/install` behind a hosting-panel IP restriction while installing. Availability only, before installation completes. |
| 5 | Low | Database password held in cleartext in the installer PHP session until `finish` | Open recommendation: document the private session-directory requirement or encrypt the stored credentials with a key in the private installation directory. Never persisted to durable state (asserted by tests). |
| 6 | Low | Resumed private-file backup matches paths to slots positionally; a directory-order change would skip one path | Open recommendation: key progress by resolved path. Fails closed today because restore verifies each backup digest and aborts with maintenance active. |
| 7 | Informational | Authenticated installer can open a MySQL connection to an arbitrary host:port | Inherent to a browser installer; noted for trusted-network deployments. |
| 8 | Informational | OAuth private-key permission check is skipped on Windows | Windows is not a supported hosting profile. |
| 9 | Informational | `session_regenerate_id()` return value unchecked in `pl_update_web()` | Cosmetic. |

## Checked and found sound

No `X-Forwarded-*`/`Forwarded` header influences the HTTPS decision in either entry point. Strict-mode, cookie-only sessions with identifier regeneration on both authentications. The bootstrap barrier cannot be bypassed by header, query parameter, environment variable or company session. Signature verification is RSA-only (≥3072 bits, SHA-256) with a host-pinned key never read from the archive; channel, prerelease/stable consistency, expiry, issue time, archive size/digest and a case-folded inventory are enforced. `ZipArchive::extractTo` is never used; member count, regular-file attributes, encryption method, sizes and per-member digests are checked before any write. Path validation rejects traversal, absolute paths, backslashes, reserved Windows device names, trailing dots, private directories and private-file suffixes; symlinked ancestors are rejected; private paths cannot sit under the public root. The official download is fixed-origin HTTPS with peer/host verification, a bounded manual redirect loop re-applying the allow list, a byte cap and a post-download digest check. Dynamic SQL identifiers come only from `information_schema`/`SHOW` output and are validated or escaped; all values are bound parameters; no `unserialize()` or attacker-influenced `include`. Error handling renders only fixed domain messages. Company/session authority is never consulted by the updater.

## Verification after the fixes (Docker project `plsec`, PHP 8.3.33, MySQL 8.4)

PHP lint 229 files, 0 failures; PHPStan no errors; installer suite 6/6; browser installer 75 checks; release signing 20 checks; update recovery, database (601 rows), full schema (80 tables, 2 views, 106 triggers) and HTTP suites all passed, including the new regressions.
