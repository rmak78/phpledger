// Checks the phpledger.com release-feed generator (www/website/releases-feed.mjs). No network.
import assert from 'node:assert/strict';
import fs from 'node:fs';
import os from 'node:os';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { buildReleaseFeed, compareVersions, writeReleaseFeed } from '../www/website/releases-feed.mjs';

const website = path.join(path.dirname(fileURLToPath(import.meta.url)), '..', 'www', 'website');
const site = JSON.parse(fs.readFileSync(path.join(website, 'src', 'site.json'), 'utf8'));
const source = JSON.parse(fs.readFileSync(path.join(website, 'src', 'releases.json'), 'utf8'));
let checks = 0;

function fixture(mutate) {
  const root = fs.mkdtempSync(path.join(os.tmpdir(), 'pl-feed-'));
  fs.mkdirSync(path.join(root, 'src', 'static', 'releases'), { recursive: true });
  const releases = structuredClone(source);
  const siteCopy = structuredClone(site);
  mutate?.(releases, siteCopy, root);
  fs.writeFileSync(path.join(root, 'src', 'releases.json'), JSON.stringify(releases));
  fs.writeFileSync(path.join(root, 'src', 'site.json'), JSON.stringify(siteCopy));
  return root;
}
const rejects = (mutate, pattern) => { assert.throws(() => buildReleaseFeed(fixture(mutate)), pattern); checks += 1; };
const preview = (version, date = '2026-10-01') => ({
  version, published_at: date,
  notes: `https://github.com/phpledger/phpledger/releases/tag/v${version}`,
  zip: `https://github.com/phpledger/phpledger/releases/download/v${version}/phpledger-${version}.zip`,
  sha256: 'b'.repeat(64), update_json: null, min_php: '8.2.0', databases: ['mysql:8.4'], image: null, min_client: null,
});

// Semantic-version ordering, including prerelease rules.
for (const [a, b] of [['1.0.0', '1.0.1'], ['1.0.0-rc.1', '1.0.0'], ['1.0.0-alpha', '1.0.0-alpha.1'], ['1.0.0-alpha.1', '1.0.0-alpha.beta'], ['1.0.0-rc.2', '1.0.0-rc.10'], ['1.9.0', '1.10.0']]) {
  assert.equal(compareVersions(a, b), -1, `${a} < ${b}`);
  assert.equal(compareVersions(b, a), 1, `${b} > ${a}`);
  checks += 2;
}

// The real source builds, deterministically, and agrees with the download page.
const feed = buildReleaseFeed(website);
assert.equal(feed.channels.stable.version, site.release.version); checks += 1;
assert.deepEqual(buildReleaseFeed(website), feed); checks += 1;
assert.match(feed.generated_at, /^\d{4}-\d{2}-\d{2}T00:00:00Z$/); checks += 1;

// A preview newer than stable is offered; an older one is dropped.
const withPreview = buildReleaseFeed(fixture((r) => r.releases.unshift(preview('1.1.0-rc.1'))));
assert.equal(withPreview.channels.preview.version, '1.1.0-rc.1'); checks += 1;
assert.equal(withPreview.history.length, 2); checks += 1;
const stalePreview = buildReleaseFeed(fixture((r) => r.releases.push(preview('1.0.0-rc.1', '2026-09-10'))));
assert.equal(stalePreview.channels.preview, null); checks += 1;

// Refusals.
rejects((r, s) => { s.release.sha256 = 'c'.repeat(64); }, /disagrees with site.json/);
rejects((r) => { r.releases[0].zip = 'https://example.com/phpledger-1.0.0.zip'; }, /zip must be an HTTPS project release address/);
rejects((r) => { r.releases[0].zip = 'https://github.com/phpledger/phpledger/releases/download/v1.0.0/other.zip'; }, /zip must name/);
rejects((r) => { r.releases[0].sha256 = 'ABC'; }, /sha256/);
rejects((r) => { r.releases[0].version = '1.0'; }, /invalid version/);
rejects((r) => { r.releases.push(structuredClone(r.releases[0])); }, /listed twice/);
rejects((r) => { r.releases.push(preview('1.1.0-rc.1')); }, /newest first/);
rejects((r) => { r.releases[0].update_json = 'https://phpledger.com/releases/1.0.0.update.json'; }, /not in src\/static/);
rejects((r) => { r.releases[0].image = 'ghcr.io/phpledger/phpledger; rm -rf /'; }, /image/);
rejects((r) => { r.schema = 2; }, /schema 1/);

// Mirrored signed metadata is accepted once the file exists.
const mirrored = fixture((r, s, root) => {
  r.releases[0].update_json = 'https://phpledger.com/releases/1.0.0.update.json';
  fs.writeFileSync(path.join(root, 'src', 'static', 'releases', '1.0.0.update.json'), '{}');
});
assert.equal(buildReleaseFeed(mirrored).channels.stable.update_json, 'https://phpledger.com/releases/1.0.0.update.json'); checks += 1;
const written = writeReleaseFeed(path.join(mirrored, 'out'), mirrored);
assert.equal(JSON.parse(fs.readFileSync(written, 'utf8')).schema, 1); checks += 1;

console.log(`Release feed checks passed: ${checks}.`);
