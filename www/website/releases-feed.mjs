#!/usr/bin/env node
/**
 * releases-feed.mjs: builds public/releases/index.json, the release feed installations read
 * (docs/RELEASE-PROTOCOL.md). Node 18+, no dependencies.
 *
 * Reads   src/releases.json   every release, newest first
 *         src/site.json       release facts shown on the download page; the stable entry must agree
 * Writes  <out>/releases/index.json
 *
 * Signed update metadata mirrored on phpledger.com lives in src/static/releases/<version>.update.json,
 * which build.mjs copies verbatim; an update_json address on phpledger.com must point at such a file.
 * Output is deterministic: generated_at is the newest publication date, not the build time.
 *
 * Usage   node releases-feed.mjs              write public/releases/index.json
 *         node releases-feed.mjs --out DIR    write DIR/releases/index.json
 *         node releases-feed.mjs --check      validate only
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = path.dirname(fileURLToPath(import.meta.url));
const VERSION = /^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(?:-[0-9A-Za-z]+(?:\.[0-9A-Za-z]+)*)?$/;
const IMAGE = /^[a-z0-9]+(?:[._/-][a-z0-9]+)*(?::[A-Za-z0-9._-]{1,128}|@sha256:[0-9a-f]{64})$/;

const fail = (message) => { throw new Error(`releases.json: ${message}`); };

function allowedUrl(value) {
  let url;
  try { url = new URL(value); } catch { return false; }
  if (url.protocol !== 'https:' || url.username || url.password || url.port) return false;
  if (url.hostname === 'phpledger.com') return url.pathname.startsWith('/releases/');
  return url.hostname === 'github.com' && url.pathname.startsWith('/phpledger/phpledger/releases/');
}

/** Compare semantic versions, including prerelease identifiers. */
export function compareVersions(a, b) {
  const [coreA, preA] = a.split(/-(.*)/s);
  const [coreB, preB] = b.split(/-(.*)/s);
  const numbers = (v) => v.split('.').map(Number);
  const [x, y] = [numbers(coreA), numbers(coreB)];
  for (let i = 0; i < 3; i += 1) if (x[i] !== y[i]) return x[i] < y[i] ? -1 : 1;
  if (!preA || !preB) return preA ? -1 : preB ? 1 : 0;
  const [p, q] = [preA.split('.'), preB.split('.')];
  for (let i = 0; i < Math.max(p.length, q.length); i += 1) {
    if (p[i] === undefined) return -1;
    if (q[i] === undefined) return 1;
    const [np, nq] = [/^\d+$/.test(p[i]), /^\d+$/.test(q[i])];
    if (np && nq && Number(p[i]) !== Number(q[i])) return Number(p[i]) < Number(q[i]) ? -1 : 1;
    if (np !== nq) return np ? -1 : 1;
    if (p[i] !== q[i]) return p[i] < q[i] ? -1 : 1;
  }
  return 0;
}

function entry(release, staticDir) {
  const { version } = release;
  if (typeof version !== 'string' || !VERSION.test(version)) fail(`invalid version ${JSON.stringify(version)}`);
  const channel = version.includes('-') ? 'preview' : 'stable';
  if (!/^\d{4}-\d{2}-\d{2}$/.test(release.published_at ?? '')) fail(`${version}: published_at must be YYYY-MM-DD`);
  for (const key of ['notes', 'zip']) if (!allowedUrl(release[key] ?? '')) fail(`${version}: ${key} must be an HTTPS project release address`);
  if (!release.zip.endsWith(`/phpledger-${version}.zip`)) fail(`${version}: zip must name phpledger-${version}.zip`);
  if (!/^[0-9a-f]{64}$/.test(release.sha256 ?? '')) fail(`${version}: sha256 must be 64 lowercase hex characters`);
  if (release.update_json !== null) {
    if (!allowedUrl(release.update_json ?? '')) fail(`${version}: update_json must be null or an HTTPS project release address`);
    const url = new URL(release.update_json);
    if (url.hostname === 'phpledger.com' && !fs.existsSync(path.join(staticDir, ...url.pathname.split('/').filter(Boolean)))) {
      fail(`${version}: ${url.pathname} is not in src/static, so the mirrored signed metadata would be missing`);
    }
  }
  if (!/^\d+\.\d+\.\d+$/.test(release.min_php ?? '')) fail(`${version}: min_php must look like 8.2.0`);
  if (!Array.isArray(release.databases) || release.databases.length === 0 || !release.databases.every((d) => /^(mysql|mariadb|pgsql|sqlite):\d+(\.\d+)*$/.test(d))) {
    fail(`${version}: databases must list engine:version pairs`);
  }
  if (release.image !== null && !IMAGE.test(release.image ?? '')) fail(`${version}: image must be null or an image reference`);
  if (release.min_client !== null && !VERSION.test(release.min_client ?? '')) fail(`${version}: min_client must be null or a version`);
  return {
    version, channel, published_at: release.published_at, notes: release.notes, zip: release.zip, sha256: release.sha256,
    update_json: release.update_json, min_php: release.min_php, databases: release.databases, image: release.image, min_client: release.min_client,
  };
}

export function buildReleaseFeed(root = ROOT) {
  const source = JSON.parse(fs.readFileSync(path.join(root, 'src', 'releases.json'), 'utf8'));
  const site = JSON.parse(fs.readFileSync(path.join(root, 'src', 'site.json'), 'utf8'));
  if (source.schema !== 1 || !Array.isArray(source.releases) || source.releases.length === 0) fail('expected schema 1 and at least one release');
  const releases = source.releases.map((release) => entry(release, path.join(root, 'src', 'static')));
  const seen = new Set();
  releases.forEach((release, index) => {
    if (seen.has(release.version)) fail(`${release.version} is listed twice`);
    seen.add(release.version);
    if (index > 0 && compareVersions(releases[index - 1].version, release.version) <= 0) fail('releases must be listed newest first');
  });
  const latest = (channel) => releases.find((release) => release.channel === channel) ?? null;
  const stable = latest('stable');
  if (!stable) fail('there is no stable release');
  if (stable.version !== site.release.version || stable.sha256 !== site.release.sha256 || stable.zip !== site.release.zipUrl || stable.published_at !== site.release.date) {
    fail(`the stable entry ${stable.version} disagrees with site.json release ${site.release.version}; update both together`);
  }
  let preview = latest('preview');
  if (preview && compareVersions(preview.version, stable.version) <= 0) preview = null;
  const generated = releases.map((release) => release.published_at).sort().at(-1);
  return {
    schema: 1,
    generated_at: `${generated}T00:00:00Z`,
    channels: { stable, preview },
    history: releases.map(({ version, channel, published_at }) => ({ version, channel, published_at })),
  };
}

export function writeReleaseFeed(outDir, root = ROOT) {
  const feed = buildReleaseFeed(root);
  const target = path.join(outDir, 'releases', 'index.json');
  fs.mkdirSync(path.dirname(target), { recursive: true });
  fs.writeFileSync(target, `${JSON.stringify(feed, null, 2)}\n`);
  return target;
}

if (process.argv[1] && path.resolve(process.argv[1]) === fileURLToPath(import.meta.url)) {
  try {
    const args = process.argv.slice(2);
    if (args.includes('--check')) {
      const feed = buildReleaseFeed();
      console.log(`Release feed valid: stable ${feed.channels.stable.version}, preview ${feed.channels.preview?.version ?? 'none'}, ${feed.history.length} release(s).`);
    } else {
      const outIndex = args.indexOf('--out');
      const outDir = outIndex >= 0 ? path.resolve(args[outIndex + 1]) : path.join(ROOT, 'public');
      console.log(`Wrote ${path.relative(process.cwd(), writeReleaseFeed(outDir))}`);
    }
  } catch (error) {
    console.error(error.message);
    process.exit(1);
  }
}
