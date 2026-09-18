The working development environment is not yet a downloadable product package. Prepare a repeatable new-foundation artifact and an administrator journey that works from a clean supported environment, without relying on an existing developer checkout or historical configuration.

## Scope

- Define the candidate contents and supported PHP 8.5/MySQL 8.4 environment, including required extensions, private configuration, writable paths and the single public document root.
- Build a versioned archive with an inventory and checksums. Include the required new runtime, migrations, dependencies/notices and operator instructions; exclude secrets, local state, synthetic test credentials and unrelated legacy runtime files.
- Provide a clear requirements check, first-administrator setup and installation failure/recovery guidance using the existing bootstrap, configuration and migration conventions.
- Document the supported upgrade starting points and prove backup restoration into an isolated environment.

## Acceptance

- [ ] An archive can be reproduced from a recorded source revision, with a documented version name and file/checksum manifest.
- [ ] A fresh supported environment installs from that archive alone, creates the first administrator and completes sign-in and simple business setup.
- [ ] Incompatible prerequisites fail clearly; private configuration and legacy repository files are not publicly served.
- [ ] Re-running completed setup/migrations cannot recreate the administrator or silently alter installed migration receipts.
- [ ] An upgrade from each explicitly supported prior schema preserves stable account identities, posted journals, document/source links and reconciled balances.
- [ ] A restored backup reconciles records, migration receipts, immutable controls and financial totals; recovery instructions are tested rather than assumed.
- [ ] The final quickstart points to a real published artifact only after its release. Until then it identifies the candidate as unreleased.

## Dependencies and exclusions

The licence/provenance issue gates publication, while packaging can be prepared in parallel. No package download, tag or supported hosting certification is claimed by this issue's creation. Do not migrate historical root SQL dumps or expose customer data to prove the installer.

Part of the [first package plan](https://github.com/phpledger/phpledger/wiki/First-Package).

## Linked work

Publication depends on [#55 the licence/provenance decision](https://github.com/phpledger/phpledger/issues/55). This package supplies the artifact tested by [#59 candidate acceptance](https://github.com/phpledger/phpledger/issues/59). Packaging preparation may proceed in parallel with [#57 reporting](https://github.com/phpledger/phpledger/issues/57) and [#58 POS refinement](https://github.com/phpledger/phpledger/issues/58).
