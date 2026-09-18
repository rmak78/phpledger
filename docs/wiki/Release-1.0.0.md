## 1.0.0: first stable release

This release consolidates the 0.6.1 workflow-recovery closure, the 0.7 browser installer and the 0.8 signed update/backup/recovery work into one supported stable release, in addition to everything shipped in [[0.6.0-preview|Release-0.6.0-preview]] and earlier.

- **Workflow recovery.** Save/post/reverse/source journeys preserve submitted values on a field error instead of discarding the draft, and errors are linked to their field. Report, source, action and return navigation is preserved through nested journeys. Financial tables and their dynamic rows are keyboard-focusable with unique row identities.
- **Browser installation.** A new `/install` entry lets an operator set up the application from a browser instead of the command line: host/database checks, the existing migration chain, private configuration, OAuth key provisioning, first-account creation and business onboarding. No Composer, Node or terminal is required. Setup locks after completion.
- **Operator-initiated signed updates and recovery.** An independent `public/maintenance.php` entry point lets the installation operator apply publisher-signed releases using a private `operator.key` and a pinned publisher public key. The updater takes an automatic, complete, matched backup (code, private configuration, keys and database) before applying a release, and automatically restores that backup if a migration or mutation fails.
- **CLI recovery companion.** `tools/resume-update.php` advances pending recovery steps from the shell when browser access was interrupted.
- **Release and channel tooling.** `tools/build-package.py`/`tools/package-files.json` build the versioned, allowlisted release archive with an authenticated exact-member manifest; `tools/sign-update.php` produces the signed release metadata.

### Assurance status and verification scope

Before this release, the owner ran the full automated test suite, fault-injection tests against the update/recovery path (signature/channel tampering, unsafe paths, interrupted migrations and mutations, dependency loss and recovery resumption), exact-artifact installation/upgrade/recovery checks against a built package archive, and developer-operated browser checks of the installer and workflow journeys at desktop, tablet and phone widths. These are disclosed technical checks, not completed acceptance claims.

Independent accounting review, independent security review, supervised pilots including a real month-end close, installation observation by an unfamiliar operator, and recovery certification on a restricted shared-hosting account have **not** been performed. The owner published 1.0.0 as the supported production scope with these limits disclosed, rather than waiting for those reviews; they continue as post-release commitments. The updater also currently requires a schema-owning database identity with DDL privileges and matching view/trigger definers; a separate low-privilege runtime identity with temporary update credentials is not implemented.

No accounting sign-off, WCAG certification, country compliance or restricted shared-host recovery certification is claimed. Use the release's `INSTALL.md`, `UPGRADE.md` and `RELEASE-SIGNING.md` for exact steps.

### Supported scope and limits

1.0.0 supports a country-neutral accounting core: chart of accounts, receipts/expenses, general journals with linked reversals, required AR/AP with manually configured tax, optional Purchasing and shared Inventory, opening conversion, bank reconciliation, core financial reports and English-language screens, plus browser installation and signed automatic updates with backup/recovery.

1.0.0 does **not** include regional tax certification or e-invoicing, a production-ready shop POS (the bundled cash POS remains an illustrative demonstration), advanced stock, partner/profit-sharing accounting, e-commerce or storefront integration, offline or native clients, or reviewed Urdu/Arabic/RTL screens (planned for 1.1 and 1.2).

### Upgrade from 0.6.0-preview

The supplied migration chain is unchanged since 0.6.0-preview; 1.0.0 adds no new migration. Upgrading is a manual, staged procedure — back up and rehearse restoration, replace application code under a maintenance window, run the CLI migration command once, provision `operator.key`/`publisher.pem` before the first signed update, and verify sign-in and a general-journal post/reversal before reopening access. See `UPGRADE.md`.

Media kit: https://github.com/rmak78/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip

Archive: `phpledger-1.0.0.zip`, SHA-256 `1c44e685b126352220c54d2ce13d575aaf2dfaf873d6b372a3a2b17189dcc63d`, 3.14 MB (3,137,991 bytes). Media kit SHA-256: `21cd6bda061794affbfaedee70ee0fb5b892133ef2dace009f4e68fb34a4df76`. Source revision: `1415ceb61cab2fe27be3947d611a16f4035e54c7`. No signed update metadata is attached to 1.0.0: the official publisher signing key had not been generated at publication, so verify the ZIP by its SHA-256 checksum; the key fingerprint will be published in the repository RELEASE-SIGNING document, the website and this Wiki once it exists.md`.

Install the application ZIP below, not GitHub's automatic source archive. Compare its SHA-256 checksum before installation. This is the first stable release.
