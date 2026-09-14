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
try {
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
    $dump = & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysqldump -uroot --single-transaction --routines --triggers --hex-blob --no-tablespaces --set-gtid-purged=OFF phpledger_test'
    if ($LASTEXITCODE -ne 0 -or !$dump) { throw 'The isolated test database backup failed.' }
    Invoke-TestDatabaseSql "CREATE DATABASE $restoreName CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;" | Out-Null
    $created = $true
    $dump | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot --database="$1"' -- $restoreName
    if ($LASTEXITCODE -ne 0) { throw 'The isolated database restore failed.' }
    $dump = $null

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
    $sourceTriggers = @(Invoke-TestDatabaseSql "SELECT TRIGGER_NAME, ACTION_TIMING, EVENT_MANIPULATION, ACTION_STATEMENT FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = 'phpledger_test' ORDER BY TRIGGER_NAME;")
    $restoreTriggers = @(Invoke-TestDatabaseSql "SELECT TRIGGER_NAME, ACTION_TIMING, EVENT_MANIPULATION, ACTION_STATEMENT FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = '$restoreName' ORDER BY TRIGGER_NAME;")
    if ($sourceTriggers.Count -ne 9 -or ($sourceTriggers -join "`n") -ne ($restoreTriggers -join "`n")) {
        throw 'Restored immutable journal triggers differ; run after other test processes finish.'
    }
    $imbalanced = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM (SELECT journal_id FROM $restoreName.pl_journal_lines GROUP BY journal_id HAVING SUM(debit) <> SUM(credit)) AS broken;"
    if ([int] $imbalanced -ne 0) { throw 'The restored ledger contains unbalanced journals.' }
    $receipt = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM $restoreName.pl_schema_migrations WHERE version IN ('001_foundation','002_product_slice','003_demo_isolation','004_pos_showcase','005_demo_period_guard') AND status = 'applied';"
    if ([int] $receipt -ne 5) { throw 'The restored migration receipts are missing.' }
    $unlinked = Invoke-TestDatabaseSql "SELECT COUNT(*) FROM $restoreName.pl_documents d LEFT JOIN $restoreName.pl_journals j ON j.id = d.journal_id AND j.company_id = d.company_id AND j.book_id = d.book_id WHERE d.journal_id IS NOT NULL AND j.id IS NULL;"
    if ([int] $unlinked -ne 0) { throw 'A restored posted source is missing its scoped journal.' }
    Write-Output "Backup/restore passed: $($tables.Count) table definitions and data checksums, $rows rows, 9 accounting/demo/POS guard triggers, all five migration receipts, scoped source links, and balanced journals."
} finally {
    # The target was randomly generated, verified absent, and created by this run in db_test.
    if ($created -and $restoreName -match '^phpledger_restore_verify_[0-9a-f]{32}$') {
        Invoke-TestDatabaseSql "DROP DATABASE $restoreName;" | Out-Null
        Write-Output 'Removed only this run''s isolated restore database; phpledger_test and the main database remain intact.'
    }
    Pop-Location
}
