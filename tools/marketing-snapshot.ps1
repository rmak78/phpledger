<#
tools/marketing-snapshot.ps1 - internal repository metrics snapshot for rmak78/phpledger.

INTERNAL USE ONLY. The figures this script collects (stars, watchers, forks,
open issues, traffic, clones, release downloads, discussion and issue counts,
referrers) are for the maintainers' own planning. They are not to be published
in the README, the Wiki, the website, release notes, social posts or anywhere
else: the project does not publish counts or adoption claims.

Each run appends one row to .cache/marketing/snapshots.csv and saves the raw
API responses under .cache/marketing/raw/<yyyy-MM-dd>/. The .cache/ folder is
gitignored. Dates are UTC, matching the GitHub traffic API day buckets.

Columns: date, stars, subscribers_count, forks, open_issues, views14, uniques14,
views_yesterday, uniques_yesterday, clones14, clones_uniques14, zip_downloads,
sha_downloads, discussions, external_issues_7d, top_referrers_json

Requirements: Windows PowerShell 5.1 or later and the GitHub CLI (gh) signed in
as an account with push access to the repository (the traffic endpoints need it).

Usage:
  powershell -NoProfile -ExecutionPolicy Bypass -File tools\marketing-snapshot.ps1

A source that fails is reported as a warning and leaves its columns blank; the
row is still written and the script then exits with code 1.
#>

[CmdletBinding()]
param(
    [string]$Repository = 'rmak78/phpledger',
    [string]$ReleaseTag = 'v0.1.0-preview',
    [string]$ZipAssetName = 'phpledger-0.1.0-preview.zip',
    [string]$ShaAssetName = 'phpledger-0.1.0-preview.zip.sha256',
    [int]$TopReferrerCount = 5
)

$ErrorActionPreference = 'Stop'

if ($null -eq (Get-Command gh -ErrorAction SilentlyContinue)) {
    throw 'The GitHub CLI (gh) is not installed or not on PATH.'
}

$ownerName = $Repository.Split('/')[0]
$repoName = $Repository.Split('/')[1]

$repoRoot = Split-Path -Parent $PSScriptRoot
$cacheDir = Join-Path $repoRoot '.cache\marketing'
$nowUtc = (Get-Date).ToUniversalTime()
$dateStamp = $nowUtc.ToString('yyyy-MM-dd')
$yesterdayStamp = $nowUtc.AddDays(-1).ToString('yyyy-MM-dd')
$sevenDaysAgoStamp = $nowUtc.AddDays(-7).ToString('yyyy-MM-dd')
$rawDir = Join-Path $cacheDir ('raw\' + $dateStamp)
$csvPath = Join-Path $cacheDir 'snapshots.csv'

New-Item -ItemType Directory -Force -Path $rawDir | Out-Null

$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
$script:failedSources = @()

function Invoke-Gh {
    # Runs gh with the given arguments and returns its standard output as one string.
    param([string[]]$Arguments)
    $output = & gh @Arguments
    $exitCode = $LASTEXITCODE
    $text = ''
    if ($null -ne $output) { $text = (@($output) -join "`n") }
    if ($exitCode -ne 0) {
        throw ('gh ' + ($Arguments -join ' ') + ' exited with code ' + $exitCode)
    }
    return $text
}

function Get-JsonSource {
    # Runs a gh command, saves its JSON output to the raw folder and returns an
    # object with Ok (did the call succeed) and Data (the parsed JSON, or null).
    param([string]$Label, [string[]]$Arguments, [string]$RawName)
    $result = New-Object PSObject -Property @{ Ok = $false; Data = $null }
    try {
        $text = Invoke-Gh -Arguments $Arguments
        [System.IO.File]::WriteAllText((Join-Path $rawDir $RawName), $text, $utf8NoBom)
        if ($text.Trim().Length -gt 0) {
            $result.Data = ConvertFrom-Json -InputObject $text
        }
        $result.Ok = $true
    } catch {
        Write-Warning ('{0}: {1}' -f $Label, $_.Exception.Message)
        $script:failedSources += $Label
    }
    return $result
}

function Get-Property {
    # Returns a property value, or null when the object or the property is missing.
    param($Object, [string]$Name)
    if ($null -eq $Object) { return $null }
    $property = $Object.PSObject.Properties[$Name]
    if ($null -eq $property) { return $null }
    return $property.Value
}

function Get-DayKey {
    # Traffic timestamps are "yyyy-MM-ddT00:00:00Z"; ConvertFrom-Json may already
    # have turned them into DateTime values, possibly shifted to local time.
    param($Value)
    if ($Value -is [DateTime]) {
        if ($Value.Kind -eq [DateTimeKind]::Local) { return $Value.ToUniversalTime().ToString('yyyy-MM-dd') }
        return $Value.ToString('yyyy-MM-dd')
    }
    $text = [string]$Value
    if ($text.Length -ge 10) { return $text.Substring(0, 10) }
    return $text
}

function Get-DayCount {
    # Returns @(count, uniques) for one UTC day from a per-day traffic array, or
    # @($null, $null) when that day is not in the array. GitHub lists zero-activity
    # days inside the 14-day window but publishes the buckets with a delay, so an
    # absent day means "not available yet", not zero.
    param($Entries, [string]$DayKey)
    if ($null -ne $Entries) {
        foreach ($entry in @($Entries)) {
            if ($null -ne $entry -and (Get-DayKey $entry.timestamp) -eq $DayKey) {
                return ,@([int]$entry.count, [int]$entry.uniques)
            }
        }
    }
    return ,@($null, $null)
}

function ConvertTo-CsvField {
    param($Value)
    $text = ''
    if ($null -ne $Value) { $text = [string]$Value }
    if ($text -match '[,"\r\n]') { return '"' + $text.Replace('"', '""') + '"' }
    return $text
}

# --- Collect -----------------------------------------------------------------

$repo = Get-JsonSource -Label 'repository' -Arguments @('api', ('repos/{0}' -f $Repository)) -RawName 'repo.json'
$views = Get-JsonSource -Label 'traffic views' -Arguments @('api', ('repos/{0}/traffic/views' -f $Repository)) -RawName 'traffic-views.json'
$clones = Get-JsonSource -Label 'traffic clones' -Arguments @('api', ('repos/{0}/traffic/clones' -f $Repository)) -RawName 'traffic-clones.json'
$referrers = Get-JsonSource -Label 'popular referrers' -Arguments @('api', ('repos/{0}/traffic/popular/referrers' -f $Repository)) -RawName 'traffic-referrers.json'
$paths = Get-JsonSource -Label 'popular paths' -Arguments @('api', ('repos/{0}/traffic/popular/paths' -f $Repository)) -RawName 'traffic-paths.json'
$releases = Get-JsonSource -Label 'releases' -Arguments @('api', ('repos/{0}/releases' -f $Repository)) -RawName 'releases.json'

# Same query as {repository(owner:"rmak78",name:"phpledger"){discussions{totalCount}}}.
# Owner and name travel as GraphQL variables so no double quotes have to survive
# Windows PowerShell 5.1 native-command argument passing.
$discussionQuery = 'query($owner:String!,$name:String!){repository(owner:$owner,name:$name){discussions{totalCount}}}'
$discussions = Get-JsonSource -Label 'discussions' -Arguments @('api', 'graphql', '-f', ('query={0}' -f $discussionQuery), '-F', ('owner={0}' -f $ownerName), '-F', ('name={0}' -f $repoName)) -RawName 'discussions.json'

# Issues (not pull requests) opened in the last seven days by anyone other than the owner.
$issueSearch = 'created:>={0} -author:{1}' -f $sevenDaysAgoStamp, $ownerName
$externalIssues = Get-JsonSource -Label 'external issues (7 days)' -Arguments @('issue', 'list', '-R', $Repository, '--state', 'all', '--limit', '1000', '--search', $issueSearch, '--json', 'number') -RawName 'issues-external-7d.json'

# --- Derive ------------------------------------------------------------------

$viewsYesterday = @($null, $null)
if ($views.Ok) {
    $viewsYesterday = Get-DayCount -Entries (Get-Property $views.Data 'views') -DayKey $yesterdayStamp
    if ($null -eq $viewsYesterday[0]) {
        Write-Warning ('Traffic views for {0} are not published yet; views_yesterday and uniques_yesterday are left blank.' -f $yesterdayStamp)
    }
}

$zipDownloads = $null
$shaDownloads = $null
if ($releases.Ok) {
    $release = $null
    foreach ($candidate in @($releases.Data)) {
        if ($null -ne $candidate -and $candidate.tag_name -eq $ReleaseTag) { $release = $candidate }
    }
    if ($null -eq $release) {
        Write-Warning ('Release {0} was not found in the releases list.' -f $ReleaseTag)
        $script:failedSources += 'release assets'
    } else {
        foreach ($asset in @($release.assets)) {
            if ($null -eq $asset) { continue }
            if ($asset.name -eq $ZipAssetName) { $zipDownloads = [int]$asset.download_count }
            if ($asset.name -eq $ShaAssetName) { $shaDownloads = [int]$asset.download_count }
        }
    }
}

$discussionCount = $null
if ($discussions.Ok -and $null -ne $discussions.Data) {
    $discussionCount = $discussions.Data.data.repository.discussions.totalCount
}

$externalIssueCount = $null
if ($externalIssues.Ok) {
    if ($null -eq $externalIssues.Data) { $externalIssueCount = 0 } else { $externalIssueCount = @($externalIssues.Data).Count }
}

$topReferrersJson = $null
if ($referrers.Ok) {
    $topList = @()
    if ($null -ne $referrers.Data) {
        $sorted = @($referrers.Data) | Sort-Object -Property count -Descending | Select-Object -First $TopReferrerCount
        foreach ($item in @($sorted)) {
            if ($null -eq $item) { continue }
            $topList += [pscustomobject][ordered]@{ referrer = [string]$item.referrer; count = [int]$item.count; uniques = [int]$item.uniques }
        }
    }
    $topReferrersJson = ConvertTo-Json -InputObject @($topList) -Compress
}

$row = [ordered]@{
    date               = $dateStamp
    stars              = (Get-Property $repo.Data 'stargazers_count')
    subscribers_count  = (Get-Property $repo.Data 'subscribers_count')
    forks              = (Get-Property $repo.Data 'forks_count')
    open_issues        = (Get-Property $repo.Data 'open_issues_count')
    views14            = (Get-Property $views.Data 'count')
    uniques14          = (Get-Property $views.Data 'uniques')
    views_yesterday    = $viewsYesterday[0]
    uniques_yesterday  = $viewsYesterday[1]
    clones14           = (Get-Property $clones.Data 'count')
    clones_uniques14   = (Get-Property $clones.Data 'uniques')
    zip_downloads      = $zipDownloads
    sha_downloads      = $shaDownloads
    discussions        = $discussionCount
    external_issues_7d = $externalIssueCount
    top_referrers_json = $topReferrersJson
}

# --- Write -------------------------------------------------------------------

$columns = @($row.Keys)
$headerLine = ($columns -join ',')
$valueLine = (($columns | ForEach-Object { ConvertTo-CsvField $row[$_] }) -join ',')

if (-not (Test-Path -LiteralPath $csvPath)) {
    [System.IO.File]::WriteAllText($csvPath, $headerLine + "`n", $utf8NoBom)
}
[System.IO.File]::AppendAllText($csvPath, $valueLine + "`n", $utf8NoBom)

Write-Host ('Snapshot appended to {0}' -f $csvPath)
Write-Host ('Raw responses saved under {0}' -f $rawDir)
Write-Host ([pscustomobject]$row | Format-List | Out-String -Width 4096)
Write-Output $headerLine
Write-Output $valueLine

if ($script:failedSources.Count -gt 0) {
    Write-Warning ('Sources with errors: {0}' -f ($script:failedSources -join ', '))
    exit 1
}
exit 0
