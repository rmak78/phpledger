# Codex prompt: AR/AP foundations — parties, multi-currency, correction model

Paste everything below the line into Codex at the repository root.

---

You are working in the PHP Ledger repository, branch `codex/integration-delivery`. Before AR/AP itself is built, three prerequisite design documents need to land in the schema and posting-service design. **This is a schema/foundations task. Do not build AR/AP invoice or bill UI, workflows, or documents in this task** — that follows once this lands.

Read first, in this order:
1. `docs/strategy/DECISION-REGISTER.md` §B7 (correction model) and `docs/strategy/PRODUCT-DIRECTION-CLARIFICATION-2026-09-15.md` (country-neutral direction — Pakistan is a reference market, not the only one; keep fields structurally generic even where today's values are drawn from Pakistan material).
2. `docs/strategy/MULTI-CURRENCY.md` — read in full; build only the items listed under "Before AR/AP schema freeze — blocking" in its §11.
3. `docs/strategy/AR-AP-PARTIES-AND-VETTING.md` — read in full; build §2 (party/contact model) now; build §3.1 (vetting status field) as schema only, no enforcement yet.
4. `docs/strategy/ERPNEXT-REVIEW.md` §0–2 for the architectural pattern to follow: one posting funnel, one open-item ledger as sole source of truth (not a derived copy that can drift), reversal-not-rename for corrections.

## Build now (schema and core posting-service mechanics only)

1. Journal-line multi-currency columns (MULTI-CURRENCY.md §1.1): `currency`, `amount_fc`, `rate`, `rate_type`, `rate_source_id`, `amount_base`, `rate_is_stale`, `ic_counterparty_entity_id` — on every line, always populated, even for domestic lines (`currency = base`, `rate = 1`).
2. Account currency properties (§1.2): `currency` (nullable), `is_monetary`, `revaluation_account_id`, `group_account_id`.
3. Company/book fields (§1.3): `functional_currency` (immutable after creation), `presentation_currency`, `group_id`, `parent_entity_id`, `ownership_pct`, `consolidation_method`.
4. `currency_rates` table (§6.1), append-only. Manual entry only in this task — no provider integrations.
5. Realised FX on settlement (§4.1), including the actual-rate override — the posting mechanic, not a settlement UI.
6. Party and contact tables (AR-AP-PARTIES-AND-VETTING.md §2): one `party` table with role flags (customer/vendor/both — never separate customer/vendor tables), the full field set in §2.1, `contacts` from §2.2, dedup on NTN/CNIC/phone at entry.
7. Vetting status field (§3.1) as schema only: `Draft → Under review → Approved → On hold → Blacklisted → Archived`, with an immutable transition log. No enforcement logic yet.
8. Correction/reversal posting model (decision register B7): the posting service supports reversal-and-repost under the *same* document identity — never ERPNext's rename-and-recreate (`-1`) pattern. A voided posting always gets a dated reversing entry (default: cancellation date; a permissioned exception may backdate the reversal while the period is still open). This is a posting-service and schema decision, not a UI decision — get the identity model right now; it cannot be migrated cleanly once AR/AP invoices exist.
9. Outbound event queue shape (parties doc §5.4): cron-driven, retryable, idempotency-keyed. Schema and dispatch mechanism only — no A75 connector implementation.

## Explicitly not in this task

- AR/AP invoice/bill documents, UI, or workflows
- Rate providers beyond manual entry (§6.2 items 2–4)
- Period-end revaluation run (§4.2), group consolidation (§8), gold/commodity accounts (§7)
- Vetting enforcement (§3.4), vendor bank change control (§3.5), the A75 connector itself
- Any regional tax-adapter logic

## Rules

- Schema and posting-service mechanics only. Bounded claims, no capability presented as shipped without evidence — existing documentation conventions apply.
- Money: integer minor units or a decimal library, never floats (PHP 8.2 floor already in force).
- If anything here conflicts with work already in progress, stop and record the conflict in `docs/strategy/AR-AP-FOUNDATIONS-NOTES.md` rather than resolving it unilaterally.
- Commit in logical groups: (1) multi-currency schema, (2) party/contact schema, (3) reversal/correction posting-service change. Do not push.
