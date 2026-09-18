A first package needs one coherent acceptance result across installation, accounting, POS and recovery. Coordinate the candidate around its actual supported scope, then publish an honest download and quickstart only when its gates are satisfied.

## Scope

- Assemble a candidate acceptance matrix linking each included capability to implementation, tests, accounting/usability review and known limitations.
- Run the complete fresh-install → business setup → transaction → journal/report → correction journey, the refined cash-sale flow, supported upgrades and backup restoration from the actual candidate artifact.
- Make a bounded starter-import decision after reviewing cutover, opening-balance and AR/AP dependencies; do not let an unfinished importer expand the release silently.
- Prepare release notes, supported-environment guidance, known issues and package checksums. Treat `0.1.0-preview` as a proposed name until a real candidate/tag is created.

## Acceptance

- [ ] Packaging, accounting/profile and POS issues have their relevant evidence linked; the owner-approved licence/provenance gate is complete before publication.
- [ ] Candidate checks pass for exact balanced posting, source/scoping permissions, duplicate/conflict handling, concurrency, period rejection, reversals and report reconciliation.
- [ ] Fresh installation, the first-admin journey, supported upgrades and restored-backup totals are verified against the candidate, with failures or exclusions disclosed.
- [ ] Appropriate PHP/JavaScript syntax, static analysis, dependency/security and responsive/keyboard checks pass; qualified accounting and observed usability reviews cover the stated supported scope.
- [ ] Starter-import scope is explicitly included or deferred. The decision contains an original cutover example, proposed CSV/XLSX fields and reconciliation rules. If included, mapping/preview/errors/duplicates, authorized confirmation, repeat safety and opening AR/AP reconciliation pass before it is advertised.
- [ ] Documentation distinguishes the hosted demo, new downloadable package and legacy source. Unsupported country, inventory, AR/AP, forecast-prediction and AI claims are absent.
- [ ] The published artifact, source revision, checksums, version/tag and quickstart agree; an independent installation follows the public instructions successfully.
- [ ] Public website, Wiki and repository links are read back after publication and accurately name the available package and remaining limits.

## Dependencies and exclusions

This issue depends on the completed decisions/evidence from the other four Sprint 03 issues. Engineering can proceed in parallel where independent; publication cannot bypass the licence or affected accounting gates. Narrow or defer incomplete capabilities explicitly.

No dates, assignees, funding commitments, full ERP scope or next-sprint AI Scan document promise are set here. Existing legacy issues are not closed or replaced by creating this candidate issue.

Part of the [first package plan](https://github.com/phpledger/phpledger/wiki/First-Package).

## Linked work

Release acceptance depends on all four workstreams:

- [#55 Licence and provenance decision](https://github.com/phpledger/phpledger/issues/55)
- [#56 Installable package and recovery](https://github.com/phpledger/phpledger/issues/56)
- [#57 Reviewed Pakistan profile and reports](https://github.com/phpledger/phpledger/issues/57)
- [#58 Deliberate POS checkout experience](https://github.com/phpledger/phpledger/issues/58)

Track the complete sprint in [milestone 4](https://github.com/phpledger/phpledger/milestone/4).
