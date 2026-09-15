param()

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

# This tool can access only the explicitly named disposable Compose test service.
function Invoke-TestDatabaseSql([string] $Sql) {
    $result = $Sql | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot --batch --skip-column-names'
    if ($LASTEXITCODE -ne 0) { throw 'The isolated test database command failed.' }
    return $result
}

Push-Location (Split-Path $PSScriptRoot -Parent)
$restoreName = 'phpledger_restore_verify_' + [guid]::NewGuid().ToString('N')
$created = $false
$restoreReadGrant = $false
# Database-level grants treat underscores as wildcards even in quoted names.
$grantSchema = $restoreName.Replace('_', '\_')
$priorOutputEncoding = $OutputEncoding
$priorConsoleEncoding = [Console]::OutputEncoding
try {
    # Windows PowerShell otherwise encodes native pipeline input as ASCII, corrupting UTF-8 data.
    $OutputEncoding = [Text.UTF8Encoding]::new($false)
    [Console]::OutputEncoding = [Text.UTF8Encoding]::new($false)
    $configuredDatabase = (& docker compose exec -T db_test printenv MYSQL_DATABASE).Trim()
    if ($LASTEXITCODE -ne 0 -or $configuredDatabase -ne 'phpledger_test') {
        throw 'Refusing to run: db_test must be the disposable phpledger_test environment.'
    }
    if ($restoreName -notmatch '^phpledger_restore_verify_[0-9a-f]{32}$') {
        throw 'Invalid isolated restore target.'
    }
    $existing = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = '$restoreName';"
    if ([int] $existing -ne 0) { throw 'Refusing to overwrite a pre-existing restore database.' }
    $tables = @(Invoke-TestDatabaseSql "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'phpledger_test' AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME;")
    if ($tables.Count -lt 10 -or @($tables | Where-Object { $_ -notmatch '^pl_[a-z_]+$' }).Count -ne 0) {
        throw 'The source is missing the expected foundation tables or contains unexpected tables.'
    }
    $views = @(Invoke-TestDatabaseSql "SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = 'phpledger_test' ORDER BY TABLE_NAME;")
    if (@($views | Where-Object { $_ -notmatch '^pl_[a-z_]+$' }).Count -ne 0 -or
        'pl_effective_documents' -notin $views -or 'pl_effective_general_drafts' -notin $views) {
        throw 'The source is missing its expected effective source views or contains unexpected views.'
    }
    $dump = & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump -uroot --single-transaction --routines --triggers --hex-blob --no-tablespaces --set-gtid-purged=OFF phpledger_test'
    if ($LASTEXITCODE -ne 0 -or !$dump) { throw 'The isolated test database backup failed.' }
    Invoke-TestDatabaseSql "CREATE DATABASE $restoreName CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;" | Out-Null
    $created = $true
    $dump | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot --database="$1"' -- $restoreName
    if ($LASTEXITCODE -ne 0) { throw 'The isolated database restore failed.' }
    $dump = $null

    # Preserve SQL SECURITY DEFINER metadata while granting the existing test definer
    # read access only to this run's isolated target, never to another database.
    $testDefiner = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM mysql.user WHERE User = 'ledger_test' AND Host = '%';"
    if ([int] $testDefiner -ne 1) { throw 'The expected disposable test view definer is missing.' }
    Invoke-TestDatabaseSql ('GRANT SELECT ON `' + $grantSchema + '`.* TO ''ledger_test''@''%'';') | Out-Null
    $restoreReadGrant = $true

    $restoredTables = @(Invoke-TestDatabaseSql "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$restoreName' AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME;")
    if (($tables -join ',') -ne ($restoredTables -join ',')) { throw 'Restored table inventory differs.' }
    $rows = 0L
    foreach ($table in $tables) {
        $sourceCount = [long] (Invoke-TestDatabaseSql "SELECT COUNT(*) FROM phpledger_test.$table;")
        $restoreCount = [long] (Invoke-TestDatabaseSql "SELECT COUNT(*) FROM $restoreName.$table;")
        if ($sourceCount -ne $restoreCount) { throw "Restored row count differs for $table." }
        $sourceChecksum = ((Invoke-TestDatabaseSql "CHECKSUM TABLE phpledger_test.$table EXTENDED;") -split "`t")[-1]
        $restoreChecksum = ((Invoke-TestDatabaseSql "CHECKSUM TABLE $restoreName.$table EXTENDED;") -split "`t")[-1]
        if ($sourceChecksum -eq 'NULL' -or $sourceChecksum -ne $restoreChecksum) { throw "Restored data checksum differs for $table." }
        $sourceSchema = Invoke-TestDatabaseSql "SHOW CREATE TABLE phpledger_test.$table;"
        $restoreSchema = Invoke-TestDatabaseSql "SHOW CREATE TABLE $restoreName.$table;"
        if (($sourceSchema -join "`n") -ne ($restoreSchema -join "`n")) { throw "Restored table definition differs for $table." }
        $rows += $sourceCount
    }
    $restoredViews = @(Invoke-TestDatabaseSql "SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = '$restoreName' ORDER BY TABLE_NAME;")
    if (($views -join ',') -ne ($restoredViews -join ',')) { throw 'Restored view inventory differs.' }
    $viewRows = 0L
    foreach ($view in $views) {
        $sourceDefinition = (Invoke-TestDatabaseSql "SHOW CREATE VIEW phpledger_test.$view;") -join "`n"
        $restoreDefinition = (Invoke-TestDatabaseSql "SHOW CREATE VIEW $restoreName.$view;") -join "`n"
        $sourceDefinition = $sourceDefinition.Replace('`phpledger_test`.', '`VERIFIED_SCHEMA`.')
        $restoreDefinition = $restoreDefinition.Replace(('`' + $restoreName + '`.'), '`VERIFIED_SCHEMA`.')
        if ($sourceDefinition -ne $restoreDefinition) { throw "Restored view definition differs for $view." }
        # Equal results alone could hide a view accidentally reading the original database.
        $dependencies = @(Invoke-TestDatabaseSql "SELECT DISTINCT TABLE_SCHEMA FROM information_schema.VIEW_TABLE_USAGE WHERE VIEW_SCHEMA = '$restoreName' AND VIEW_NAME = '$view' ORDER BY TABLE_SCHEMA;")
        if ($dependencies.Count -ne 1 -or $dependencies[0] -ne $restoreName) {
            throw "Restored view $view does not depend exclusively on the isolated restore schema."
        }
        $columns = @(Invoke-TestDatabaseSql "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'phpledger_test' AND TABLE_NAME = '$view' ORDER BY ORDINAL_POSITION;")
        if ($columns.Count -eq 0 -or @($columns | Where-Object { $_ -notmatch '^[a-z_]+$' }).Count -ne 0) {
            throw "Unexpected columns in effective source view $view."
        }
        $jsonColumns = ($columns | ForEach-Object { '`' + $_ + '`' }) -join ','
        # Compare exact per-row digests, ordered by stable source ID; no source data is printed.
        $sourceViewRows = @(Invoke-TestDatabaseSql "SELECT id, SHA2(CAST(JSON_ARRAY($jsonColumns) AS CHAR CHARACTER SET utf8mb4), 256) FROM phpledger_test.$view ORDER BY id;")
        $restoreViewRows = @(Invoke-TestDatabaseSql "SELECT id, SHA2(CAST(JSON_ARRAY($jsonColumns) AS CHAR CHARACTER SET utf8mb4), 256) FROM $restoreName.$view ORDER BY id;")
        if (($sourceViewRows -join "`n") -ne ($restoreViewRows -join "`n")) { throw "Restored effective rows differ for $view." }
        $viewRows += $sourceViewRows.Count
    }
    foreach ($source in @(
        @{ View = 'pl_effective_documents'; Table = 'pl_documents'; Type = 'o.kind' },
        @{ View = 'pl_effective_general_drafts'; Table = 'pl_general_drafts'; Type = "'general_journal'" }
    )) {
        $brokenCurrentLinks = Invoke-TestDatabaseSql @"
SELECT COUNT(*) FROM $restoreName.$($source.View) d
JOIN $restoreName.$($source.Table) o ON o.id=d.id AND o.company_id=d.company_id AND o.book_id=d.book_id
LEFT JOIN $restoreName.pl_posting_identities i ON i.company_id=o.company_id AND i.book_id=o.book_id AND i.source_id=o.id AND i.source_type=$($source.Type)
LEFT JOIN $restoreName.pl_posting_revisions v ON v.identity_id=i.id AND v.revision=(SELECT MAX(v2.revision) FROM $restoreName.pl_posting_revisions v2 WHERE v2.identity_id=i.id)
LEFT JOIN $restoreName.pl_journals j ON j.id=d.journal_id AND j.company_id=d.company_id AND j.book_id=d.book_id
WHERE (d.journal_id IS NOT NULL AND j.id IS NULL)
 OR NOT (d.original_journal_id <=> o.journal_id)
 OR NOT (d.journal_id <=> COALESCE(v.journal_id,o.journal_id))
 OR d.revision <> COALESCE(v.revision,o.revision);
"@
        if ([int] $brokenCurrentLinks -ne 0) { throw "Restored current posting links differ for $($source.View)." }
    }
    $sourceTriggers = @(Invoke-TestDatabaseSql "SELECT TRIGGER_NAME, ACTION_TIMING, EVENT_MANIPULATION, ACTION_STATEMENT FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = 'phpledger_test' ORDER BY TRIGGER_NAME;")
    $restoreTriggers = @(Invoke-TestDatabaseSql "SELECT TRIGGER_NAME, ACTION_TIMING, EVENT_MANIPULATION, ACTION_STATEMENT FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '$restoreName' ORDER BY TRIGGER_NAME;")
    if ($sourceTriggers.Count -lt 9 -or ($sourceTriggers -join "`n") -ne ($restoreTriggers -join "`n")) {
        throw 'Restored immutable journal triggers differ; run after other test processes finish.'
    }
    $imbalanced = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM (SELECT journal_id FROM $restoreName.pl_journal_lines GROUP BY journal_id HAVING SUM(debit) <> SUM(credit)) AS broken;"
    if ([int] $imbalanced -ne 0) { throw 'The restored ledger contains unbalanced journals.' }
    $receipt = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM $restoreName.pl_schema_migrations WHERE status = 'applied';"
    $expectedMigrations = @(Get-ChildItem -LiteralPath 'www/phpledger/install/migrations' -Filter '*.php').Count
    if ([int] $receipt -ne $expectedMigrations) { throw 'The restored migration receipts are missing.' }
    $unlinked = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM $restoreName.pl_documents d LEFT JOIN $restoreName.pl_journals j ON j.id = d.journal_id AND j.company_id = d.company_id AND j.book_id = d.book_id WHERE d.journal_id IS NOT NULL AND j.id IS NULL;"
    if ([int] $unlinked -ne 0) { throw 'A restored posted source is missing its scoped journal.' }
    $unlinkedGeneral = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM $restoreName.pl_general_drafts d LEFT JOIN $restoreName.pl_journals j ON j.id = d.journal_id AND j.company_id = d.company_id AND j.book_id = d.book_id WHERE d.journal_id IS NOT NULL AND j.id IS NULL;"
    if ([int] $unlinkedGeneral -ne 0) { throw 'A restored general journal source is missing its scoped journal.' }
    Write-Output "Backup/restore passed: $($tables.Count) table definitions and data checksums, $rows rows, $($views.Count) target-scoped view definitions and $viewRows effective row digests, $($sourceTriggers.Count) guard triggers, $receipt migration receipts, original/current scoped source links, and balanced journals."
} finally {
    # The target was randomly generated, verified absent, and created by this run in db_test.
    if ($created -and $restoreName -match '^phpledger_restore_verify_[0-9a-f]{32}$') {
        if ($restoreReadGrant) {
            Invoke-TestDatabaseSql ('REVOKE SELECT ON `' + $grantSchema + '`.* FROM ''ledger_test''@''%'';') | Out-Null
        }
        Invoke-TestDatabaseSql "DROP DATABASE $restoreName;" | Out-Null
        Write-Output 'Removed only this run''s isolated restore database; phpledger_test and the main database remain intact.'
    }
    Pop-Location
    $OutputEncoding = $priorOutputEncoding
    [Console]::OutputEncoding = $priorConsoleEncoding
}
