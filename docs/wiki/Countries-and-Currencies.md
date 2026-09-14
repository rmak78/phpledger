# Regional choices without hidden accounting assumptions

The local preview supports **one base currency per business/book**. Its country selector can suggest a currency, while users retain an explicit manual choice. A bounded backend country lookup may provide a suggestion once per session; local/private addresses skip it and failed lookups are cached.

## Base currencies in the current preview

| Currency | Code |
|---|---|
| US dollar | USD |
| Euro | EUR |
| Pound sterling | GBP |
| Pakistani rupee | PKR |
| Indian rupee | INR |
| Malaysian ringgit | MYR |
| Bangladeshi taka | BDT |
| Sri Lankan rupee | LKR |
| Nepalese rupee | NPR |
| Singapore dollar | SGD |

Malaysia, Bangladesh, Sri Lanka, Nepal and Singapore are the five latest country/currency additions. The selected base currency stays consistent through sample transactions, POS receipts and reports; existing businesses keep their currency. This is not foreign-currency accounting or country tax coverage.

## Accounting framework priority

**Pakistan → UK → UAE** is the reporting implementation priority. Framework selection must consider the actual entity, legal form, reporting period, applicable rules and reviewed accounting policies. IP location, interface language and currency cannot make that decision automatically.

Country and industry Chart of Accounts research is intended to improve the setup wizard. Those researched candidates are not yet installed, reviewed jurisdiction-specific templates. The current foundation does not claim compliance in any country.

## Language, time and number display

The current interface is English. Event timestamps are stored in UTC and displayed in the terminal's local timezone. Posting, fiscal and cutover dates remain the same business calendar dates everywhere.

The future direction is multilingual, with English as the primary fallback. Reviewed translations, script fonts and RTL layouts must precede a language-support claim. Planned display preferences include date formats, currency code/symbol placement, separators, permitted precision, and Western or South Asian grouping. Formatting must never alter a stored amount, currency or accounting date.

## Future multicurrency

Later transaction currencies will use fixed rates, periodic backend updates and permission-controlled manual overrides. Each posting must retain the applied rate, source and effective-time snapshot so a later rate update cannot rewrite history. Rate-provider choice, refresh timing, rounding and gains/revaluation policies require design and accounting review; none is an implemented preview feature.

[[Accounting and reports|Accounting-and-Reports]] · [[Architecture]] · [[Roadmap]]
