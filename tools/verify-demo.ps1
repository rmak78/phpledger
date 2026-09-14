param()
$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest
Push-Location (Split-Path $PSScriptRoot -Parent)
try {
    $database = (& docker compose exec -T db_test printenv MYSQL_DATABASE).Trim()
    if ($LASTEXITCODE -ne 0 -or $database -ne 'phpledger_test') { throw 'Demo verification requires the disposable db_test service.' }
    $sql = @'
CREATE USER IF NOT EXISTS 'ledger_demo_test'@'%' IDENTIFIED BY 'local-demo-test-only';
GRANT SELECT, INSERT, UPDATE ON phpledger_demo.* TO 'ledger_demo_test'@'%';
'@
    $sql | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot'
    if ($LASTEXITCODE -ne 0) { throw 'Could not prepare the restricted local demo test account.' }
    $options = @('--profile','test','run','--rm','-e','PL_ENV=demo','-e','PL_DB_NAME=phpledger_demo')
    & docker compose @options -e PL_DB_USER=root -e PL_DB_PASSWORD=local-test-root-only -e PL_DEMO_RESET_MODE=1 test php tools/demo-reset.php --now
    if ($LASTEXITCODE -ne 0) { throw 'Initial isolated demo reset failed.' }
    $query = 'SELECT generation FROM phpledger_demo.pl_demo_state WHERE id = 1;'
    $before = $query | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot --batch --skip-column-names'
    & docker compose @options -e PL_DB_USER=ledger_demo_test -e PL_DB_PASSWORD=local-demo-test-only test php tests/demo_smoke.php
    if ($LASTEXITCODE -ne 0) { throw 'Restricted demo smoke failed.' }
    & docker compose @options -e PL_DB_USER=root -e PL_DB_PASSWORD=local-test-root-only -e PL_DEMO_RESET_MODE=1 test php tools/demo-reset.php --now
    if ($LASTEXITCODE -ne 0) { throw 'Second isolated demo reset failed.' }
    $after = $query | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot --batch --skip-column-names'
    $empty = 'SELECT COUNT(*) FROM phpledger_demo.pl_demo_visitors;' | & docker compose exec -T db_test sh -lc 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" mysql -uroot --batch --skip-column-names'
    if ($before -eq $after -or [int] $empty -ne 0) { throw 'Demo reset failed to change generation or remove prior visitors.' }
    Write-Output 'Demo reset verification passed: separate restricted web grants, real generation replacement and empty visitor state; only phpledger_demo in db_test was reset.'
} finally {
    Pop-Location
}
