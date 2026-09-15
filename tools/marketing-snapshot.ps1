<#
tools/marketing-snapshot.ps1 - read-only repository and public SEO snapshot.

INTERNAL USE ONLY. The figures this script collects (stars, watchers, forks,
open issues, traffic, clones, release downloads, discussion and issue counts,
referrers) are for the maintainers' own planning. They are not to be published
in the README, the Wiki, the website, release notes, social posts or anywhere
else: the project does not publish counts or adoption claims.

Each run appends one row to .cache/marketing/snapshots-v2.csv and saves raw
read responses in a unique UTC-stamped directory under .cache/marketing/raw/.
Old snapshots.csv files are preserved. Nothing is sent, submitted or changed on
remote services. Public repository metadata and public marketing pages are read
by default. Private GitHub traffic requires -IncludePrivateTraffic. The script
reads /demo/health only and never starts a demo visitor or writes remote state.

Columns: date, stars, subscribers_count, forks, open_issues, views14, uniques14,
views_yesterday, uniques_yesterday, clones14, clones_uniques14, zip_downloads,
sha_downloads, discussions, external_issues_7d, top_referrers_json

Requirements: Windows PowerShell 5.1 or later and the GitHub CLI (gh) signed in.
Only -IncludePrivateTraffic needs repository traffic permission.

Usage:
  powershell -NoProfile -ExecutionPolicy Bypass -File tools\marketing-snapshot.ps1
  ./tools/marketing-snapshot.ps1 -ReleaseTag v0.1.3-preview
  ./tools/marketing-snapshot.ps1 -IncludePrivateTraffic

A source that fails is reported as a warning and leaves its columns blank; the
row is still written and the script then exits with code 1.
#>

[CmdletBinding()]
param(
    [ValidatePattern('^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$')][string]$Repository = 'rmak78/phpledger',
    [ValidatePattern('^$|^v[0-9]+\.[0-9]+\.[0-9]+(?:-[A-Za-z0-9.-]+)?$')][string]$ReleaseTag = '',
    [string]$ZipAssetName = '',
    [string]$ShaAssetName = '',
    [ValidateRange(1,10)][int]$TopReferrerCount = 5,
    [switch]$IncludePrivateTraffic,
    [switch]$SkipSiteChecks
)

$ErrorActionPreference = 'Stop'

if ($null -eq (Get-Command gh -ErrorAction SilentlyContinue)) {
    throw 'The GitHub CLI (gh) is not installed or not on PATH.'
}

$ownerName = $Repository.Split('/')[0]
$repoName = $Repository.Split('/')[1]

$repoRoot = Split-Path -Parent $PSScriptRoot
$siteConfig = Get-Content -LiteralPath (Join-Path $repoRoot 'www/website/src/site.json') -Raw -Encoding UTF8 | ConvertFrom-Json
if ($ReleaseTag -eq '') { $ReleaseTag = [string]$siteConfig.release.tag }
if ($ReleaseTag -notmatch '^v[0-9]+\.[0-9]+\.[0-9]+(?:-[A-Za-z0-9.-]+)?$') { throw 'Invalid release tag in site configuration.' }
if ($ZipAssetName -eq '') { $ZipAssetName = 'phpledger-' + $ReleaseTag.Substring(1) + '.zip' }
if ($ShaAssetName -eq '') { $ShaAssetName = $ZipAssetName + '.sha256' }
$cacheDir = Join-Path $repoRoot '.cache\marketing'
$nowUtc = (Get-Date).ToUniversalTime()
$dateStamp = $nowUtc.ToString('yyyy-MM-dd')
$yesterdayStamp = $nowUtc.AddDays(-1).ToString('yyyy-MM-dd')
$sevenDaysAgoStamp = $nowUtc.AddDays(-7).ToString('yyyy-MM-dd')
$runStamp = $nowUtc.ToString('yyyyMMddTHHmmssfffZ') + '-' + [guid]::NewGuid().ToString('N').Substring(0,8)
$rawDir = Join-Path $cacheDir ('raw\' + $runStamp)
$csvPath = Join-Path $cacheDir 'snapshots-v2.csv'

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

function Get-RobotsDecision {
    # For this site's public / and /demo/ probes. Applies matching specific
    # groups instead of merging them with *, then the longest matching rule.
    param([string]$Body, [string]$Agent, [string]$Path)
    $groups = @()
    $agents = @()
    $rules = @()
    foreach ($line in ($Body -split '\r?\n')) {
        $clean = ($line -split '#',2)[0].Trim()
        if ($clean -notmatch '^([^:]+):\s*(.*)$') { continue }
        $field = $Matches[1].Trim().ToLowerInvariant()
        $value = $Matches[2].Trim()
        if ($field -eq 'user-agent') {
            if ($rules.Count -gt 0) {
                $groups += [pscustomobject]@{Agents=$agents; Rules=$rules}
                $agents = @(); $rules = @()
            }
            $agents += $value.ToLowerInvariant()
        } elseif ($field -in @('allow','disallow') -and $agents.Count -gt 0) {
            $rules += [pscustomobject]@{Field=$field; Path=$value}
        }
    }
    if ($agents.Count -gt 0) { $groups += [pscustomobject]@{Agents=$agents; Rules=$rules} }
    $specificity = -1
    $selected = @()
    foreach ($group in $groups) {
        $score = -1
        foreach ($token in $group.Agents) {
            if ($token -eq '*') { $score = [Math]::Max($score,0) }
            elseif ($Agent.ToLowerInvariant().Contains($token)) { $score = [Math]::Max($score,$token.Length) }
        }
        if ($score -gt $specificity) { $selected = @(); $specificity = $score }
        if ($score -ge 0 -and $score -eq $specificity) { $selected += $group.Rules }
    }
    $bestLength = -1
    $allowed = $true
    foreach ($rule in $selected) {
        if ($rule.Path -eq '') { continue }
        $pattern = '^' + [regex]::Escape($rule.Path).Replace('\*','.*').Replace('\$','$')
        if ($Path -cmatch $pattern) {
            $length = $rule.Path.Length
            if ($length -gt $bestLength -or ($length -eq $bestLength -and $rule.Field -eq 'allow')) {
                $bestLength = $length
                $allowed = $rule.Field -eq 'allow'
            }
        }
    }
    return [pscustomobject]@{Agent=$Agent; Path=$Path; Allowed=$allowed}
}

function Get-PublicResponse {
    param([string]$Url)
    try {
        $response = Invoke-WebRequest -UseBasicParsing -Method Get -Uri $Url -TimeoutSec 30 -MaximumRedirection 5 -Headers @{'Cache-Control'='no-cache'; 'User-Agent'='PHP-Ledger-Public-Audit/1.0'}
        return [pscustomobject]@{Url=$Url; Status=[int]$response.StatusCode; Body=[string]$response.Content; ContentType=[string]$response.Headers['Content-Type']; Robots=[string]$response.Headers['X-Robots-Tag']; Error=$null}
    } catch {
        $status = 0
        if ($null -ne $_.Exception.Response) { $status = [int]$_.Exception.Response.StatusCode }
        # Do not store response headers, cookies or error bodies.
        return [pscustomobject]@{Url=$Url; Status=$status; Body=''; ContentType=''; Robots=''; Error='HTTP request did not return a successful response'}
    }
}

function Get-HtmlAttribute {
    param([string]$Tag, [string]$Name)
    $match = [regex]::Match($Tag, ('(?i)\b' + [regex]::Escape($Name) + '\s*=\s*(["''])((?:(?!\1).)*)\1'))
    if (-not $match.Success) { return '' }
    return [System.Net.WebUtility]::HtmlDecode($match.Groups[2].Value)
}

function Write-PublicSiteSnapshot {
    # Fixed owned host only; no account login, analytics, robots submissions,
    # visitor allocation, external page assets or sitemap-supplied foreign URLs.
    $base = 'https://phpledger.com'
    $problems = @()
    $robots = Get-PublicResponse ($base + '/robots.txt')
    $sitemap = Get-PublicResponse ($base + '/sitemap.xml')
    $llms = Get-PublicResponse ($base + '/llms.txt')
    $feed = Get-PublicResponse ($base + '/news/feed.xml')
    $demoHealth = Get-PublicResponse ($base + '/demo/health')
    $missing = Get-PublicResponse ($base + '/seo-audit-missing-' + $runStamp + '/')
    if ($robots.Status -ne 200 -or $robots.Body -notmatch '(?im)^Sitemap:\s*https://phpledger\.com/sitemap\.xml\s*$') { $problems += 'robots or sitemap declaration missing' }
    if ($llms.Status -ne 200) { $problems += 'llms file unavailable' }
    if ($feed.Status -ne 200) { $problems += 'RSS feed unavailable' }
    try { $null = [xml]$feed.Body } catch { $problems += 'RSS feed malformed XML' }
    if ($missing.Status -ne 404) { $problems += 'missing public URL did not return 404' }
    if ($demoHealth.Status -ne 200 -or $demoHealth.Robots -notmatch '\bnoindex\b') { $problems += 'demo health unavailable or missing noindex header' }
    $decisions = @()
    foreach ($agent in @('Googlebot','Bingbot','OAI-SearchBot','Claude-SearchBot','PerplexityBot','ExampleBot')) {
        foreach ($path in @('/','/demo/')) {
            $decision = Get-RobotsDecision $robots.Body $agent $path
            $decisions += $decision
            # Google/Bing may fetch the demo's noindex response. Other listed
            # bots remain crawl-blocked. A robots block is not index removal.
            $expectedAllowed = $path -eq '/' -or $agent -in @('Googlebot','Bingbot')
            if ($decision.Allowed -ne $expectedAllowed) { $problems += ('robots policy mismatch: ' + $agent + ' ' + $path) }
        }
    }
    $urls = @()
    try {
        if ($sitemap.Status -ne 200) { throw 'Unavailable sitemap' }
        $settings = New-Object System.Xml.XmlReaderSettings
        $settings.DtdProcessing = [System.Xml.DtdProcessing]::Prohibit
        $settings.XmlResolver = $null
        $reader = [System.Xml.XmlReader]::Create([System.IO.StringReader]::new($sitemap.Body),$settings)
        try { $xml = New-Object System.Xml.XmlDocument; $xml.XmlResolver = $null; $xml.Load($reader) } finally { $reader.Dispose() }
        $urls = @($xml.SelectNodes('//*[local-name()="url"]/*[local-name()="loc"]') | ForEach-Object {$_.InnerText})
        if ($urls.Count -lt 1 -or $urls.Count -gt 30) { throw 'Unexpected sitemap URL count' }
    } catch { $problems += 'sitemap unreadable or outside bounded 1-30 URL check'; $urls = @() }
    $pages = @()
    foreach ($url in $urls) {
        $uri = [uri]$url
        if ($uri.Scheme -ne 'https' -or $uri.Authority -ne 'phpledger.com' -or $uri.UserInfo -ne '' -or $uri.Query -ne '' -or $uri.Fragment -ne '' -or $uri.AbsolutePath -notmatch '^/(?:[a-z0-9-]+/)*$' -or $uri.AbsolutePath.StartsWith('/demo/')) {
            $problems += 'sitemap includes an unexpected URL; not fetched'; continue
        }
        $page = Get-PublicResponse $url
        $description = $canonical = $ogImage = $google = $bing = ''
        foreach ($tag in [regex]::Matches($page.Body,'(?is)<(?:meta|link)\b[^>]*>')) {
            $name = Get-HtmlAttribute $tag.Value 'name'
            $property = Get-HtmlAttribute $tag.Value 'property'
            if ($name -eq 'description') { $description = Get-HtmlAttribute $tag.Value 'content' }
            if ($name -eq 'google-site-verification') { $google = Get-HtmlAttribute $tag.Value 'content' }
            if ($name -eq 'msvalidate.01') { $bing = Get-HtmlAttribute $tag.Value 'content' }
            if ($name -eq 'robots' -and (Get-HtmlAttribute $tag.Value 'content') -match 'noindex') { $problems += ('marketing page noindex: ' + $url) }
            if ($property -eq 'og:image') { $ogImage = Get-HtmlAttribute $tag.Value 'content' }
            if ((Get-HtmlAttribute $tag.Value 'rel') -eq 'canonical') { $canonical = Get-HtmlAttribute $tag.Value 'href' }
        }
        $title = [System.Net.WebUtility]::HtmlDecode([regex]::Match($page.Body,'(?is)<title>(.*?)</title>').Groups[1].Value)
        $h1Count = [regex]::Matches($page.Body,'(?i)<h1\b').Count
        $jsonCount = 0
        foreach ($json in [regex]::Matches($page.Body,'(?is)<script\b[^>]*type=["'']application/ld\+json["''][^>]*>(.*?)</script>')) {
            try { $null = ConvertFrom-Json $json.Groups[1].Value; $jsonCount++ } catch { $problems += ('invalid JSON-LD: ' + $url) }
        }
        if ($page.Status -ne 200 -or $title -eq '' -or $description -eq '' -or $canonical -cne $url -or $h1Count -ne 1 -or $jsonCount -eq 0 -or $ogImage -eq '' -or $page.Robots -match 'noindex') { $problems += ('public page metadata/status mismatch: ' + $url) }
        $pages += [pscustomobject]@{Url=$url; Status=$page.Status; Title=$title; Description=$description; Canonical=$canonical; H1Count=$h1Count; JsonLdBlocks=$jsonCount; OgImage=$ogImage; GoogleVerificationPresent=($google -ne ''); BingVerificationPresent=($bing -ne '')}
    }
    $result = [ordered]@{TimestampUtc=$nowUtc.ToString('o'); ReleaseTarget=$ReleaseTag; Checks='public HTTP and metadata only; no indexing or search-console account state verified'; SitemapUrlCount=$urls.Count; Robots=$decisions; Pages=$pages; RobotsStatus=$robots.Status; SitemapStatus=$sitemap.Status; LlmsStatus=$llms.Status; FeedStatus=$feed.Status; MissingPageStatus=$missing.Status; DemoHealthStatus=$demoHealth.Status; DemoHealthRobots=$demoHealth.Robots; Problems=$problems}
    [System.IO.File]::WriteAllText((Join-Path $rawDir 'public-seo.json'),(ConvertTo-Json $result -Depth 10),$utf8NoBom)
    if ($problems.Count -gt 0) { $script:failedSources += 'public SEO checks'; Write-Warning ($problems -join '; ') }
    Write-Host ('Public SEO: {0} sitemap URLs, {1} findings. No account/submission/visitor action performed.' -f $urls.Count,$problems.Count)
}

# --- Collect -----------------------------------------------------------------

$repo = Get-JsonSource -Label 'repository' -Arguments @('api', '--method', 'GET', ('repos/{0}' -f $Repository)) -RawName 'repo.json'
$views = $clones = $referrers = [pscustomobject]@{Ok=$false; Data=$null}
if ($IncludePrivateTraffic) {
    $views = Get-JsonSource -Label 'traffic views' -Arguments @('api', '--method', 'GET', ('repos/{0}/traffic/views' -f $Repository)) -RawName 'traffic-views.json'
    $clones = Get-JsonSource -Label 'traffic clones' -Arguments @('api', '--method', 'GET', ('repos/{0}/traffic/clones' -f $Repository)) -RawName 'traffic-clones.json'
    $referrers = Get-JsonSource -Label 'popular referrers' -Arguments @('api', '--method', 'GET', ('repos/{0}/traffic/popular/referrers' -f $Repository)) -RawName 'traffic-referrers.json'
    $null = Get-JsonSource -Label 'popular paths' -Arguments @('api', '--method', 'GET', ('repos/{0}/traffic/popular/paths' -f $Repository)) -RawName 'traffic-paths.json'
}
$releases = Get-JsonSource -Label 'target release' -Arguments @('api', '--method', 'GET', ('repos/{0}/releases/tags/{1}' -f $Repository,$ReleaseTag)) -RawName 'release.json'

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
        if ($null -eq $zipDownloads -or $null -eq $shaDownloads) {
            Write-Warning 'Target release is missing its expected ZIP or checksum asset.'
            $script:failedSources += 'release assets'
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
    timestamp_utc      = $nowUtc.ToString('o')
    repository         = $Repository
    release_tag        = $ReleaseTag
    private_traffic    = [bool]$IncludePrivateTraffic
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
    external_issues_limit_reached = ($externalIssueCount -eq 1000)
    top_referrers_json = $topReferrersJson
}

# --- Write -------------------------------------------------------------------

$columns = @($row.Keys)
$headerLine = ($columns -join ',')
$valueLine = (($columns | ForEach-Object { ConvertTo-CsvField $row[$_] }) -join ',')

if (-not (Test-Path -LiteralPath $csvPath)) {
    [System.IO.File]::WriteAllText($csvPath, $headerLine + "`n", $utf8NoBom)
} elseif ((Get-Content -LiteralPath $csvPath -TotalCount 1) -cne $headerLine) {
    throw 'Snapshot CSV header differs; preserve the existing file and investigate before appending.'
}
[System.IO.File]::AppendAllText($csvPath, $valueLine + "`n", $utf8NoBom)
if (-not $SkipSiteChecks) { Write-PublicSiteSnapshot }

Write-Host ('Snapshot appended to {0}' -f $csvPath)
Write-Host ('Raw responses saved under {0}' -f $rawDir)
Write-Host ('Release target: {0}; private traffic requested: {1}' -f $ReleaseTag,[bool]$IncludePrivateTraffic)

if ($script:failedSources.Count -gt 0) {
    Write-Warning ('Sources with errors: {0}' -f ($script:failedSources -join ', '))
    exit 1
}
exit 0
