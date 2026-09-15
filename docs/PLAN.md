# PHP Ledger: Modern BixiSoft Foundation, Exceptional SME Experience

> Historical planning/research context. Current product direction is country-neutral, with Pakistan FBR one planned connector; minimum PHP is 8.2 and deployment defaults to 8.3. The [15 September clarification](strategy/PRODUCT-DIRECTION-CLARIFICATION-2026-09-15.md) and current roadmap/licensing policy supersede earlier runtime, regional sequencing and licence assumptions below.

## 1. Product and stack direction

Build a fresh PHP Ledger application using BixiSoft’s lightweight architecture, preserving the repository history and selectively reusing verified accounting concepts.

The first audience is **SME owners, accountants, and bookkeepers**. The software and all modules remain open source. Revenue comes from support, including setup on customer-owned hosting.

| Area | Decision |
|---|---|
| PHP | Target **PHP 8.5**, using the latest stable patch when implementation starts. The current release is **8.5.10**; PHP 8.6 is still in testing. [Official PHP releases](https://www.php.net/) |
| Application structure | One modular application using BixiSoft’s shared bootstrap, explicit routes, PHP templates, reusable functions, and server-side permissions. |
| Database | MySQL 8.4 LTS with InnoDB, fixed-precision financial amounts, constraints, and versioned migrations. [MySQL documentation](https://dev.mysql.com/doc/refman/8.4/en/) |
| Database access | Retain the MeekroDB approach, with a maintained, pinned dependency and PHP 8.5 compatibility tests. |
| Frontend | A new design system, responsive PHP-rendered screens, modern CSS, and small JavaScript modules. Replace the legacy AdminLTE interface. |
| Development | Reproducible Docker environment, Composer dependency management, automated tests, static analysis, and dependency checks. |
| Deployment | Documented installation on supported customer hosting. Project hosting initially serves the website and demonstration system. |

The active [BixiSoft architecture reference](/C:/a75crm/README.md) provides useful conventions. Its runtime currently uses PHP 8.3, so compatibility with the new stack remains a validation task. Reuse its infrastructure patterns selectively; keep PHP Ledger independent of CRM-specific code and configuration.

Accounting rules will live in small, typed, testable functions independent of HTML, request variables, and sessions. This separation is essential to keeping the lightweight architecture maintainable.

## 2. UI/UX becomes a release requirement

**The first milestone must demonstrate that people can understand and use the product quickly.**

Separate two journeys:

- **Installation:** an administrator installs the application, checks hosting requirements, and creates the first administrator account.
- **Business onboarding:** an owner or accountant creates a company and starts useful work without needing technical help.

The business journey will be:

1. Choose a clearly isolated sample company or create a real company.
2. Enter essential information: business name, base currency, accounting start date, and fiscal year.
3. Accept a suitable account template, with advanced configuration available later.
4. Choose to start fresh or import existing records through a preview-and-validation workflow.
5. Complete a first task, such as recording an expense or receipt.
6. See the result in a readable report with a path back to the transaction.

Opening balances and required accounting decisions must be resolved before treating an existing business as ready for live bookkeeping.

**Design principles**

- Plain-language tasks for owners; efficient journals, reconciliation, imports, and reports for accountants.
- One consistent product, with permissions and progressive disclosure controlling complexity.
- Helpful empty states, contextual explanations, clear next actions, and visible save/posting status.
- Fast keyboard entry, sensible defaults, reusable selections, and searchable records.
- Clear distinctions between drafts, posted entries, corrections, and reversals.
- Consistent typography, spacing, navigation, forms, tables, and feedback across every module.
- Desktop efficiency and practical mobile workflows, with a WCAG 2.2 AA accessibility target. [W3C standard](https://www.w3.org/TR/WCAG22/)

**Initial usability targets**

| Journey | Target |
|---|---|
| Explore the product | Open a sample experience without installation or registration. |
| Basic company setup | Complete within five minutes after first login for a straightforward new company. |
| First useful outcome | Record a simple transaction and locate its report effect within ten minutes. |
| Unassisted task completion | At least four of five participants in each initial owner/accountant test group complete the agreed core tasks. |
| Recovery from mistakes | Users can understand validation failures and correct drafts without losing their work. |

These are proposed acceptance targets, not claims about the current software. Measure installation time and complex data migration separately.

## 3. Revised development stages

| Stage | Work and deliverables | Completion gate |
|---|---|---|
| **1. Discovery and restart** | Preserve legacy history; assess reusable concepts and licensing; interview SME owners and accountants; define accounting workflows, multi-book meaning, support boundaries, and the initial budget. | Written product scope, accounting examples, architecture decisions, and prioritized backlog. |
| **2. Experience design and stack proof** | Develop three visual directions, select one, and prototype onboarding, daily transactions, and reports. In parallel, prove PHP 8.5 installation, authentication, permissions, and database transactions. | Selected design direction, tested core journeys, and evidence that BixiSoft conventions work cleanly on the new runtime. |
| **3. First complete product slice** | Implement company setup → account template → receipt/expense → balanced journal → trial balance → linked reversal, using the selected design system. | Accounting checks pass and representative users complete the journey without coaching. |
| **4. Website and early validation** | Prepare the phpledger.com relaunch, product demonstration, documentation, pilot registration, contributor information, and public roadmap. Test the offer with prospective users. | Accurate product claims, working website journeys, two accounting reviewers, and three prospective pilot organizations. |
| **5. Milestone funding** | Cost the next release, verify the receiving jurisdiction/platform, and prepare a campaign for AI tools, infrastructure, design, development, and specialist review. | Verified receiving route and a funded, achievable milestone with explicit deliverables. |
| **6. Accounting MVP and supported pilots** | Complete journals, fiscal controls, AR/AP, customer/vendor balances, cash/bank workflows, CSV imports, reconciliation, financial statements, exports, installation, and upgrades. Run supported pilots before the first stable release. | Pilot users complete an accounting period; reports reconcile; installation, upgrades, and recovery pass. |
| **7. Multi-book and localization** | Implement the discovery-approved book model, traceable adjustments, reconciliation, regional templates, and separately reviewed tax adapters. | Each book balances independently and differences are explainable. |
| **8. ERP expansion** | Add inventory/purchasing, then one retail POS, then van distribution. Treat jewelry, pharmacy, and restaurant experiences as separate validated releases. | Each module has demonstrated demand, usable workflows, and totals that reconcile to accounting. |

Design and technical validation run alongside each other. The broader build starts after the first product slice proves both usability and accounting integrity.

## 4. Accounting interfaces, website, and funding

All financial writes pass through one posting interface carrying company/book identity, source document, date, currency, duplicate-prevention key, and balanced journal lines. It returns a durable journal reference or a clear rejection.

Start with atomic synchronous posting. Later asynchronous modules must use durable pending events, retries, duplicate protection, and visible reconciliation status.

Define multi-book boundaries during discovery before schema implementation. Begin the proof with one complete book. Country tax logic, foreign-exchange accounting, offline synchronization, and native wrappers remain later work.

Evolve phpledger.com around three paths: **try/install**, **get support**, and **contribute/support development**. Show actual product journeys, current capabilities, limitations, and milestone progress. Resolve the observed HTTPS problem during an authorized relaunch.

Funding will explicitly include UI/UX design and usability testing alongside AI tools, hosting, development, accounting review, and security testing. Fund one measurable release at a time. Keep software access free and describe paid support deliverables precisely.

The receiving jurisdiction remains undecided. Verify platform eligibility before collecting money; prepare the interest list and campaign materials first. If funding falls short, reduce the next milestone and avoid commitments that depend on an unfunded team.

## 5. Validation and delivery boundaries

The foundation must pass fresh installation, authentication, company/book isolation, balanced posting, duplicate prevention, concurrent writes, rollback, period locking, reversal, and report-reconciliation tests.

Every release also requires appropriate lint/static checks, dependency review, upgrade and backup-restoration checks, and browser testing at desktop, tablet, and mobile sizes. UI acceptance includes keyboard operation, loading/error/empty states, and observed usability sessions.

You own product priorities and funding commitments. A technical lead owns implementation reliability, a designer owns the experience system, and accounting reviewers validate financial behavior. Roles can initially be part-time; their work must still appear in the budget.

Assumptions: no known active installation needs migration; all modules remain open source; customer hosting is customer-owned; the initial core is country-neutral.

**Review status:** files changed: none; migrations: no; schema changed: no; raw secrets exposed: no; external read-only calls: yes; production changed: no. References read include the original brief, PHP Ledger source, local BixiSoft/A75 and WCWA architecture documentation, and official technology/accessibility documentation. No Google Drive documents were read. Runtime compatibility, lint, application tests, and visual QA have not yet been executed.

The first execution milestone is the discovery brief, selected UI direction, and PHP 8.5 foundation proof.
