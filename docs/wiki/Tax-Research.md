# Country and industry tax research

PHP Ledger has an early, source-linked tax candidate catalog for **eight countries and seven industries**. Every country seed is marked `research_only`, `enabled: false` and `review_required: true`; regime candidates are unreviewed. These files do not activate tax calculation, registration checks, filing or provider submission.

The catalog is a starting point for an optional tax module. It is not a tax calculator or a claim that PHP Ledger supports a country's obligations.

## Coverage

| Countries | Research |
|---|---|
| Pakistan | [Federal goods, provincial/ICT services and direct-tax research](https://github.com/rmak78/phpledger/blob/master/docs/tax/PAKISTAN.md) |
| United Kingdom and United Arab Emirates | [UK/UAE research and unresolved scope](https://github.com/rmak78/phpledger/blob/master/docs/tax/UK-UAE.md) |
| Malaysia, Bangladesh, Sri Lanka, Nepal and Singapore | [Asian country research and scope gaps](https://github.com/rmak78/phpledger/blob/master/docs/tax/ASIA.md) |

Industry profiles cover **restaurants, membership clubs, pharmacies, traders, distributors, retail shops and workshops**. They contain candidate regime references and classification questions. A profile does not mean every referenced regime applies to every business.

The [JSON candidate files](https://github.com/rmak78/phpledger/tree/master/resources/tax) keep authorities, jurisdictions, source links, checked dates, rates where established, legal effective dates where evidenced, conditions and open questions.

## How to interpret a candidate

- Registration, entity type, turnover, transaction location, product/service classification and exemptions must be resolved for the actual business and period.
- A numeric rate has a stated scope and source; it is not a universal industry default.
- `null` means unresolved or not a single rate. It must never be interpreted as zero.
- Zero-rated, exempt and out-of-scope transactions need distinct treatment, including input-tax recovery.
- A source's check date is not a law's effective date. A missing end date does not prove continued applicability.
- Withholding, turnover/minimum taxes and income/corporate taxes need their own settlement and recognition rules.

Pakistan research distinguishes federal sales tax on goods from services regimes administered by provinces and the Islamabad Capital Territory framework. The catalog also records country-specific questions rather than forcing every jurisdiction into that model.

## Accounting and tax are separate review tracks

Applicable law and adopted reporting standards determine a business's accounting framework. ICAP, ICMAP and ACCA professional material informs research; those bodies are not interchangeable with statutory tax authorities, and their material does not certify PHP Ledger.

Amounts collected for a tax authority, recoverable input taxes, non-recoverable purchase taxes and withholding settlements can have different accounting effects. Each adapter needs reviewed mappings and examples consistent with the selected reporting profile. Tax recognition must not be reduced to adding a percentage to every invoice.

See the [Pakistan accounting-framework research](https://github.com/rmak78/phpledger/blob/master/docs/accounting/PAKISTAN_REPORTING_RESEARCH.md) and [[Accounting and reports|Accounting-and-Reports]].

## Before enabling a tax adapter

Define the supported entity/transaction scope, verify current official sources and legal effective dates, resolve classification and input-recovery questions, and obtain qualified local review. Test taxable, exempt, zero-rated and correction examples against document totals, tax controls and the ledger.

Retain the policy version and calculation/source snapshot with each posted document. Provider integration and filing need separate authorized implementation. Optional software does not waive applicable obligations; unsupported tax must block affected production use rather than silently calculate zero.

The core remains the immediate build priority. Tax contracts are designed before dependent document schemas, with a reviewed adapter required before their applicable production workflows. See [[Module roadmap|Module-Roadmap]].

[[Countries and currencies|Countries-and-Currencies]] · [[Architecture]] · [[Roadmap]]
