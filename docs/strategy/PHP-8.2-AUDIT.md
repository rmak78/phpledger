# PHP 8.2 audit — 15 September 2026

Status: **configuration change stopped at an incompatible locked dependency**. PHP 8.2 is the owner's approved target floor, not a verified supported runtime for this checkout. No runtime guard, Docker base, CI runtime matrix or dependency version was changed by this audit.

## Evidence

- Official `php:8.2-cli-bookworm` resolved to **PHP 8.2.33**, image digest `sha256:6ca4b01d84082465358c5d541a2bef0edd9d0be494802aeb40f2d7c7a8d73adb`.
- `php -l` on **109 project PHP files** under `www/phpledger`, `tools` and `tests`: **zero syntax failures**. This includes the preserved in-progress demo work; parsing is not end-to-end runtime support.
- A targeted source scan found no project calls to the searched PHP 8.3–8.5 additions: `json_validate`, `mb_str_pad`, `str_increment`, `str_decrement`, `array_find`, `array_any`, `array_all`, `array_first`, `array_last`, `bcround`, `bcceil`, `bcfloor` or `request_parse_body`; no typed class constants or pipe operator were found. This scan is not a substitute for the required runtime matrix.
- All **30 PHP files in the locked `symfony/uid` package** also parse on PHP 8.2.33. Its declared runtime requirement still excludes PHP 8.2; no parser error or specific unsupported function is invented to explain that declaration.
- `composer prohibits php 8.2.33 --locked` identified the project floor and `symfony/uid v8.1.5`. The command exited with a blocking dependency result, as expected.

## Exact blockers and dependent configuration

| File and line at audit | Finding | Required follow-up |
|---|---|---|
| `composer.lock:1919` / `composer.lock:1933` | Locked `symfony/uid` **v8.1.5** declares PHP **>=8.4.1**. | Resolve a supported dependency version under PHP 8.2, review the lock diff and rerun MCP/OAuth/session tests. Do not bypass Composer's platform checks. |
| `composer.lock:565` | `mcp/sdk` permits `symfony/uid` `^5.4 || ^6.4 || ^7.3 || ^8.0`. | A compatible dependency resolution may be possible without changing the requested MCP SDK pin; it has not been selected or tested in this task. |
| `composer.json:7` / `composer.lock:2135` | Existing project requirement is `~8.5.0`. | Change the manifest and lock together only after resolving the dependency gate. |
| `www/phpledger/includes/functions/runtime_functions.php:6` | Runtime guard rejects versions below 8.5 and at/above 8.6. | Replace with the approved minimum after compatible dependencies and runtime tests exist. This is the existing guard, not a syntax incompatibility. |
| `www/phpledger/install/preflight.php:108` | Success output names PHP 8.5; preflight calls the shared guard at line 18. | Update alongside the guard and installer tests, not independently. |
| `tests/installer_test.php:27` / `tests/installer_test.php:29` | Assertions encode the existing 8.5 runtime policy. | Update meaningful boundary cases when the floor is implemented. |
| `Dockerfile:1` / `.github/workflows/foundation.yml:20` | Docker/test workflow uses PHP 8.5.10. Compose services build that shared Dockerfile. | Use the approved PHP 8.2 base and an 8.2/8.3/8.4 test matrix after dependency resolution. No Compose runtime was changed. |

The extracted 0.2.0 candidate's generated `vendor/composer/platform_check.php:7` also enforces PHP >=8.5 from that package's manifest. The package predates the strategy decision; do not hand-edit generated platform checks or present that ZIP as PHP 8.2 compatible.

## Stop rule and remaining gate

The [decision task](CODEX-PROMPT-2026-09-15.md) says: “If the PHP 8.2 audit finds code that cannot run on 8.2, list each occurrence with file and line in `docs/strategy/PHP-8.2-AUDIT.md` and stop; do not refactor in this task.” The locked dependency prevents a supported 8.2 installation, so the PHP-floor configuration group stops here. Licence/CLA and product documentation are independent changes.

No unsupported project syntax was found; the concrete obstacle is the installed dependency contract plus existing runtime enforcement. Fresh installation, upgrade, financial/access/connection suites and browser checks on PHP 8.2/8.3/8.4 remain unrun. README and INSTALL continue to state the actual PHP 8.5 requirement and identify 8.2 as the target until those checks pass.
