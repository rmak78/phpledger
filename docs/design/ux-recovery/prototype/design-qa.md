# Design QA: workflow recovery prototype

**Final result: passed for the bounded local design prototype.** This is not owner acceptance, accounting validation, WCAG conformance or production readiness.

## Source and comparison

The approved Review Console direction and Inter typography are recorded in `docs/DESIGN.md`. The exact approved source image is [preserved here](qa/approved-review-console.png). Current-run application audit screenshots provide the behavioural problem evidence. [The combined comparison](comparison.html) places the approved source and the current desktop prototype together; the rendered comparison screenshot was inspected. The source is 1586×992 and the prototype capture 1440×1035; these are labelled and intentionally not represented as a pixel-identical comparison.

The shared selected task is EXP-0018, Harbor Office Supply, USD125.00. The new direction intentionally replaces stacked horizontal navigation with one header and one navigation list, preserves the list/detail structure, removes redundant Save in review mode, and exposes journal/history progressively. The Petty cash example makes account preservation reviewable. These are proposed refinements required by the audit, not accidental fidelity drift.

## Required visual surfaces

| Surface | Inspection/result |
|---|---|
| Typography | Actual bundled Inter variable font loaded; 14px body, 26px main heading, restrained 20px secondary hierarchy; financial values use tabular numerals. Desktop/tablet/mobile screenshots inspected. |
| Layout/spacing | Shared context header; 192px/164px navigation on desktop/tablet; explicit mobile Menu. List/detail collapses to reachable list-to-detail. Amounts and both debit/credit columns fit 390px. |
| Colour/tokens | Existing navy `#0c2052`, blue `#304dea`, ink `#182546`, light borders and pale selected rows retained. Status uses text as well as colour. Comprehensive contrast review is still required. |
| Assets | Existing PHP Ledger raster logo and bundled Inter font reused with font licence. No invented illustration, icon system or customer imagery. |
| Copy/content | Always-visible design/sample notice, explicit sample identity, country Research gates, manually configured tax example and unimplemented deep-history notices. No fabricated review date, package hash or release status. |

## Iterations and fixes

1. Initial tablet capture showed unnecessary vertical centring; corrected main margins and rechecked the final daily captures.
2. Adjacent grid panels inherited a vertical margin; reset the grid rule. Later module/POS captures show aligned panels.
3. Independent operations review found mobile lists hidden by a permanent detail class; restored list → detail → Back and retested all four operations routes.
4. Settled list status was hardcoded; it now agrees with the detail balance.
5. Source links opened lists; they now open detail hashes. A final reload test confirms detail survives a refreshed URL.
6. Skip-link hash routing was corrected. The first Tab showed visible focus; Enter focused `MAIN#main` while retaining Transactions.
7. The daily demonstration action is labelled Open sample expense. It opens the selected example without offering an edit action on a posted record. New invoice/bill input has its own exact-value review demonstration.

The final daily screenshots are `qa/89-prototype-final-{1440,768,390}.png`. Earlier captures remain as iteration evidence, not claims about the final CSS. Operations final retakes and their limits are in [independent QA](operations-qa.md) and [its manifest](operations-qa.json).

## Interaction checks

- Six-step setup: Pakistan remains separate from USD; Research package cannot claim installation; chart name edit retained; unchecked final acknowledgement produces an error; checking it completes the walkthrough without creating a company.
- Daily: selected mobile row opens detail; memo-only Save retains Petty cash and shows success; debit/credit both remain visible.
- Tax: inclusive default changes the displayed example to gross105/net100/tax5, with no posted-document mutation claim.
- Visibility: hiding Inventory removes its navigation link and retains core availability.
- POS: notebook4.50 with cash5.00 gives change0.50 in the simulated receipt.
- Independent operations: partial/full customer and supplier payments, linked customer credit, overpayment rejection with preserved input, order receiving/bill clearing and refreshed source-detail links passed. Final replay: 14 states, zero page errors.
- Accessibility spot checks: one main/h1 and zero unlabelled visible setup controls; visible skip-link outline; actual focus transfer; Inter loaded; reduced-motion media query matched with no animation; 390px page width matched document width.

JavaScript passed `node --check`. The known initial missing-favicon request was corrected in the main prototype. The comparison helper is only a local review sheet. No backend requests, persistence, company creation or financial posting are implemented.

## Remaining acceptance work

The prototype deliberately covers representative flows. It does not prove exhaustive supplier-credit, multi-line invoice, import, bank matching, permission, stale preview, closed-period or concurrency behaviour. Full browser zoom, screen-reader, contrast, keyboard journey and touch-device tests remain for the actual implementation. The eleven sample plans need real reconciled fixture expansion and loader review. These are named scope limits, not hidden production capabilities.

No open P0/P1/P2 layout or interaction defect remains in the tested prototype paths. Further density/polish and observed task-speed feedback are appropriate during owner/accountant review.
