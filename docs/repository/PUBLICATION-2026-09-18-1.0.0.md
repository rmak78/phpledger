# PHP Ledger 1.0.0 publication receipt

**Published on 18 September 2026.** First stable release, by owner decision, consolidating the 0.6.1 workflow closure, the 0.7 browser installer and the 0.8 signed update/backup/recovery work.

- GitHub release: https://github.com/phpledger/phpledger/releases/tag/v1.0.0 (published 14:25:35 UTC, marked latest, not a prerelease)
- Media kit: https://github.com/phpledger/phpledger/releases/download/v1.0.0/phpledger-1.0.0-media-kit.zip
- Wiki: https://github.com/phpledger/phpledger/wiki/Release-1.0.0 (wiki commit `5f5f2e5`)
- Downloads page, redesigned website and hosted demo: **live and verified on phpledger.com**; see the 19 September deployment follow-up below.

The owner instructed same-day publication of the consolidated work as 1.0.0 on 18 September 2026. Independent accounting review, independent security review, supervised pilots with a month-end close, unfamiliar-operator installation observation, the 14-day release-candidate period and restricted shared-host recovery certification did not run before publication. Every release surface says so; none claims otherwise.

## Source and artifacts

| Item | Value |
|---|---|
| Package source commit (in `PACKAGE-MANIFEST.json`) | `1415ceb61cab2fe27be3947d611a16f4035e54c7` |
| Release tag `v1.0.0` target | `2f244959ab49abb798c34f867c08c0f1e68cf4e7` (adds the package proof, a test-harness fix and website checksum fills; no packaged file differs) |
| Branch | `codex/ui-redesign-0.6` |
| Build | `.cache/release-1.0.0/build-release.py` from a detached clean worktree of the source commit, production-only Composer vendor built inside the PHP 8.3.33 test image (`composer.lock` SHA-256 `f7851726888aca96bb3959126903c0e350aa3636c0fddb46fda80c789528bfe9`), `tools/build-package.py` channel `stable`, 1,582 manifest entries |

| Artifact | Bytes | SHA-256 |
|---|---:|---|
| phpledger-1.0.0.zip | 3,137,991 | `1c44e685b126352220c54d2ce13d575aaf2dfaf873d6b372a3a2b17189dcc63d` |
| phpledger-1.0.0-media-kit.zip | 879,361 | `21cd6bda061794affbfaedee70ee0fb5b892133ef2dace009f4e68fb34a4df76` |

All four assets (both archives and both `.sha256` files) were downloaded anonymously from their public URLs after publication and matched the local artifacts; `sha256sum -c` passed for both archives. The media-kit checksum asset was re-uploaded once to correct a Windows line ending; the corrected asset is the one verified.

The media kit contains README, announcement, social and e-mail drafts, a guided demo, FAQ, checksums and eight screenshots captured from the local 1.0.0 runtime with a sample "Northbridge Bookkeeping Demo" company (Home at 1440 and 390, invoice editor, ageing, profit and loss, bank reconciliation, Modules, and the browser installer's database step from a disposable unconfigured instance). No campaign was sent.

**No signed update metadata is attached.** The official publisher signing key did not exist at publication (generating a long-lived private key on this machine was refused by the tooling's permission policy and is an owner action). The procedure, fingerprint conventions and the owner's key-generation command are in [RELEASE-SIGNING.md](../RELEASE-SIGNING.md). 1.0.0 is verified by checksum; later releases ship `phpledger-<version>.update.json`.

## Verification

- **Source suite on the release tree (PHP 8.3.33, MySQL 8.4, isolated Compose project):** 286 tests, 0 failures; PHP lint 229 files, 0 failures; PHPStan no errors; sample validator passed. Release signing 20 checks; browser installer 75 checks; update recovery, database recovery (601 rows), full-schema recovery (80 tables, 2 views, 106 triggers, 439 restoration stages) and HTTP maintenance/authority suites passed; `tools/verify-backup-restore.ps1` passed (80 tables, 22,643 rows, 2 views with 1,057 effective row digests, 106 guard triggers, 32 receipts, balanced journals).
- **GitHub CI:** run 35355879764 on `2f24495` passed on PHP 8.2.33, 8.3.33 and 8.4.25. The earlier run on `1415ceb` failed only on PHP 8.2 in `tests/update-http-test.php`: the test polled `is_file()` on a marker another process unlinks and PHP's per-process stat cache never refreshed on 8.2. The product code was correct; the test now clears the stat cache. Reproduced and fixed locally on 8.2 and re-checked on 8.3 before tagging.
- **Exact ZIP (PHP 8.3.33):** [STABLE-1.0.0-PACKAGE-PROOF.json](STABLE-1.0.0-PACKAGE-PROOF.json). Archive bytes/hash and all 1,582 manifest entries verified; no unlisted files; the four packaged operator documents carry 1.0.0 and the source commit with no placeholders. Fresh installation from the extracted archive: 32 receipts, purchase→receipt→bill→payment and taxed invoice→post→settle→credit through the packaged posting service, balanced trial balance, reconciled AR/AP/stock/received-not-billed, empty replay. Browser installer against the extracted archive: 75 checks. Upgrade from the anonymously verified published 0.6.0-preview archive (`627dd521…`): zero new migrations, all prior financial rows and receipts byte-identical, new posting afterwards succeeds.
- **Not repeated against the extracted archive:** the signed-update fault-recovery suites take the checkout as the installed root; they ran against the same commit's source, and the packaged updater files are hash-identical to that source. PHP 8.2/8.4 exact-archive runs were not repeated locally; CI covers the source matrix.
- **Internal security review:** [SECURITY-REVIEW-INTERNAL-2026-09-18.md](SECURITY-REVIEW-INTERNAL-2026-09-18.md). No critical or high defect; one medium (bounded operator-key attempts) and one low (installed-version downgrade guard) fixed with tests; four low and three informational items open. Automated source review only.
- **Workflow acceptance (developer-run, local 1.0.0 runtime):** 32 of the 41 registered HTML GET routes in 49 states × 5 widths (1366/1024/768/390/320) × JavaScript on/off = 490 captures, all HTTP 200, no overflow, no console errors, headings present; 125 keyboard stops with visible focus on five screens; five forms preserved values and return context through validation errors with JavaScript on and off; two report→source→action→return chains preserved context. Nine routes needing setup, POS-cart or sample-pack state were not covered. Evidence: `output/playwright/stable-1.0.0/` (local). A defect found there, successful sign-ins consuming the shared client-IP lockout bucket, was fixed in the release commit.
- **Docs, README, Wiki, About:** README, roadmap, validation, installer, development, architecture and design documents updated; documentation restored to the repository (commit `711f743`); Wiki pages Home, Getting-Started, First-Package, Roadmap, sidebar and the new Release-1.0.0 page published and fetched anonymously; About description updated to the 1.0.0 wording, website URL and topics unchanged.

## Follow-up status

1. **Publisher signing key:** generate it with the command in `.cache/release-1.0.0/signing/gen-publisher-key.md` (also described in `docs/RELEASE-SIGNING.md`), then sign `phpledger-1.0.0.zip` with `sign-release.sh`, upload `phpledger-1.0.0.update.json` to the release, and publish the fingerprint in README, Wiki and website.
2. **phpledger.com — completed 19 September:** the product-site redesign from `master` `86a1eef` is published as `website-1-0-0-20260918-210112` (295 static files, 70 HTML outputs). The hosted 1.0.0 demo was independently reverified. See the deployment follow-up below and the credential-safe operator runbook in `docs/DEMO.md`.
3. **Merge to master:** the release branch carries the tag; open/merge the pull request so `master` matches `v1.0.0`.
4. **Open gates:** schedule the independent accounting and security reviews, pilots and the shared-host recovery demonstration recorded as unchecked in the roadmap checklist; treat `resources/coa/upstream/` (61 MB of Odoo/ERPNext chart data, uncommitted) as a separate size/licensing decision.

## Change boundaries

No new migration files; the chain still ends at `031_posting_source_lookup`. Product schema unchanged. Only disposable test databases and random proof databases were created and dropped locally. No raw secrets in files or logs; the throwaway signing key used to prove the procedure existed only inside a container. External calls: GitHub (push, release, wiki, About), dependency downloads for image builds, anonymous asset downloads. No e-mail, campaign, payment, webhook or production-host change. No Google Drive reference was used.

## Demo cutover (19 September 2026)

Release `core-1.0.0-1415ceb61cab` replaced `core-0.6.0-preview-b117b5cef7b4` at 2026-09-18T20:16:06Z host time. Source came from the verified `phpledger-1.0.0.zip` (sha256 `1c44e685…dcc63d`) plus the runtime inputs at commit `1415ceb`; only `Dockerfile` differed from the live baseline (zip extension for the updater), so the host built a new image (`sha256:8aba6be68d82…`) from the staged source instead of reusing the tested 0.6.0 image. The migration set was unchanged (32 receipts, no new migrations). The frozen live backup was restored and upgraded in isolation before the switch; 80 base tables, 2 views, 106 guards and all original records/receipts were verified unchanged, the vhost was untouched and the existing database container was kept. Backups: `/var/www/phpledger/data/backups/core-1.0.0-1415ceb61cab`. Public checks after cutover: `/demo/health` 200, `/demo/` shows 1.0.0, a fresh visitor sample started and opened the starter guide and reports. The public website remains on the 0.6.0-preview pages until its separate static release is published.

## Website completion and demo wording follow-up (19 September 2026)

The intended product-site redesign from `master` `86a1eef` (rebuild `ac8fca3`) is live as **`website-1-0-0-20260918-210112`**, published at **21:02:58 UTC on 18 September / 02:02:58 PKT on 19 September**. The first attempt in this session published an older same-version design from `record-demo-cutover`; the owner identified the mismatch and the correct master artifact then replaced it. All **295 files** match the reviewed design build, with **70 HTML outputs**. The homepage title is “PHP Ledger: self-hosted double-entry accounting software”; Pricing and Community return 200; both `/support` URLs return 301 to `/pricing/`. Only the static root and these two redirect rules changed. [Full website receipt](../design/website/qa/live-1.0.0-product-publication.json). Backup: `/var/www/phpledger/data/backups/website-1-0-0-20260918-210112`.

Anonymous app/media-kit downloads and checksum assets again matched their published hashes. Browser checks passed ten routes at 1440, 768 and 390 pixels. The 320-pixel header extends to 376 pixels; that existing layout issue is recorded for Claude. No full application regression or independent review was repeated.

The owner then requested sample wording throughout repository documentation and the demo. The live demo is now **`core-1.0.0-sample-20260919-r2`**, completed **21:20:15 UTC**, comprising the same official 1.0.0 package plus a recorded 33-file wording overlay. Twelve changed PHP files passed lint and all eleven sample-pack digests agree with their catalogue. The original package manifest/archive remain provenance; `DEPLOYMENT-OVERLAY.json` describes changed hashes. A private matched backup was retained and the normal guarded sample-only reset invalidated earlier temporary visitors, rebuilding the same schema from the same 32 migration definitions. No new migration or schema definition was added. The database container, runtime image and website proxy were retained. Fresh Accounting starter and Willow Corner Shop guides open with sample wording; the reports page loads. [Demo wording receipt](DEMO-1.0.0-SAMPLE-WORDING-2026-09-19.json). Backup: `/var/www/phpledger/data/backups/core-1.0.0-sample-20260919-r2`.

Repository substring cleanup changed 27 first-party text files plus five remaining website-worktree documentation/checker files; targeted JSON, catalogue hashes, changed PHP/JavaScript syntax and text scans passed. Generated sample data was rebuilt using its existing generator. Historical image pixels and published release ZIPs were retained. No credentials were exposed, no Google Drive reference was used, and no messages, campaigns, payments or Git push were performed. External calls were scoped SSH/HTTPS deployment and verification. Live production website and demo changed as explicitly requested.

`AGENTS_SYNC.MD` and `AGENT_MESSAGES.MD`, linked by both `AGENTS.md` and `CLAUDE.md`, record the credential-safe deployment process and shared handoff. Account numbering/conversion, collapsible report summaries and an additional Pakistan pharmacy company using an Odoo COA reference with fictional data were handed to Claude at the owner's direction. No feature implementation, conversion, migration or live feature deployment was completed in this session.
