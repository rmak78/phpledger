> On 15 September 2026 the owner requested removal of the historical application from the current repository tree. Use Git history at `2972e57:legacy/` or the original baseline below for research. The history has not been rewritten; historical paths below are not current installation paths.

# Legacy reference

Historical baseline: `fe528eb52a8be277f3b2806022d23a817c01bda8` from `master`. The legacy application is retained only in Git history for traceability. Its files and SQL dumps are not a supported runtime or migration path for the revived application.

The user describes this codebase as not having been properly maintained since 2014. Repository history also contains 2015 application changes and 2023 setup commits; those commits do not establish modern runtime or security readiness.

## Original README

The original introduction below is historical wording, not the current support or licensing policy.

```text
# phpledger
A simple double entry accounting system written in PHP, mySQL , HTML, CSS, Javascript, jQuery, Bootstrap.
We at Sutlej Solutions are looking forward to meet our everyday accounting needs with this application. If you find it usefull, be our guest to use it. Just don't ask questions. We don't provide free support for it.

Eventual home for this application would be at http://www.phpledger.com/



```

## Reuse assessment

Preserve accounting vocabulary, workflow examples, and useful schema concepts as research material. Revalidate all financial behavior, security decisions, dependencies, and data relationships before any selective reuse. The legacy session setup, arbitrary module dispatch, copied vendor bundles, and database dumps are not the new foundation.

The observed legacy inventory includes company settings, account groups/accounts, fiscal/reporting periods, journal vouchers, and expense vouchers. This is an inventory of source files, not a claim that these workflows currently run correctly.
