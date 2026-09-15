#!/usr/bin/env node
/**
 * check.mjs: static QA for the built site in www/website/public (Node 18+, no dependencies).
 *
 * ERRORS make the process exit 1; WARNINGS are printed and recorded. The report is written to
 * docs/design/website/qa/static-checks.json (per-page bytes, budgets, errors and warnings).
 *
 * Rules (E = error, W = warning)
 *   links     local href/src/srcset/poster resolve to a file or dir/index.html (E); in-page and cross-page
 *             #anchors resolve to an id (E); id references in for/aria-* attributes resolve (E);
 *             directory links without a trailing slash (W); missing navigation destinations (E); /demo/ is served
 *             by another vhost and is skipped; mailto: only to site.email (E); no tel: (E)
 *   document  <html lang> (E); exactly one <h1> (E); unique <title> across pages (E); description 50-160
 *             characters (E); canonical = baseUrl + path (E); exactly one aria-current="page" in the nav on
 *             nav pages and none elsewhere (E); duplicate ids (E); og:image exists and its PNG dimensions
 *             match og:image:width/height (E)
 *   csp       no style= attributes, <style>, inline <script> (JSON-LD excepted), on*= handlers, javascript:
 *             or data: URLs, external origins for stylesheets/scripts/images/sources/preloads (E); CSS has
 *             no @import, url(http, url(// or url(data: (E)
 *   images    alt present (E); width and height present (E); sizes when srcset (E); loading="lazy" after
 *             the first two images unless fetchpriority="high" (W)
 *   content   forbidden strings (E); a "synthetic" label on every page that shows /assets/screens/ (E);
 *             JSON-LD parses and has no aggregateRating/review/interaction counts (E); two-fragment
 *             headings and banned words (W)
 *   sitemap   every URL maps to a file (E); indexable pages missing from the sitemap (W)
 *   budgets   eager = html + css + js + @font-face fonts + brand images + non-lazy images (largest srcset
 *             candidate); total = eager + every other referenced asset; compared with the page's
 *             budgetEager/budgetTotal front matter (E when exceeded)
 *   js        node --check on every public JS file (E); no fetch(, XMLHttpRequest, localStorage,
 *             sessionStorage or sendBeacon (E)
 * Pages carrying <meta http-equiv="refresh"> are redirect stubs: only the link, CSP and content rules apply.
 */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { spawnSync } from 'node:child_process';

const ROOT = path.dirname(fileURLToPath(import.meta.url));
const SRC = path.join(ROOT, 'src');
const PUBLIC = path.join(ROOT, 'public');
const REPO = path.resolve(ROOT, '..', '..');
const REPORT = path.join(REPO, 'docs', 'design', 'website', 'qa', 'static-checks.json');

const site = JSON.parse(fs.readFileSync(path.join(SRC, 'site.json'), 'utf8'));
const NAV_PATHS = new Set(site.nav.map((entry) => entry[2]));
const DEMO_PREFIX = site.demo || '/demo/';
const FONT_FILE = /\.(woff2?|ttf|otf|eot)$/i;
const FORBIDDEN = [
  [/\+92[\d\s-]{6,}/, 'phone number'],
  [/M5 First Floor/, 'old address'],
  [/licensing review/i, '"licensing review"'],
  [/being prepared/i, '"being prepared"'],
  [/no download/i, '"no download"'],
  [/PLACEHOLDER/, '"PLACEHOLDER"'],
  [/\blorem\b/i, '"lorem"'],
];
const BANNED_WORDS = /\b(empower|seamless|streamlin|robust|effortless|unlock|elevat)\w*/gi;
const TWO_FRAGMENTS = /^[^.!?]{2,40}\.\s+[^.!?]{2,40}\.$/;
const JS_FORBIDDEN = ['fetch(', 'XMLHttpRequest', 'localStorage', 'sessionStorage', 'sendBeacon'];
const LOCAL_ONLY_LINK_RELS = new Set(['stylesheet', 'preload', 'modulepreload', 'prefetch', 'icon', 'apple-touch-icon', 'manifest']);
const ID_REFERENCE_ATTRS = ['for', 'aria-labelledby', 'aria-describedby', 'aria-controls', 'aria-owns', 'aria-activedescendant'];

const errors = [];
const warnings = [];
const error = (page, rule, message) => errors.push({ page, rule, message });
const warning = (page, rule, message) => warnings.push({ page, rule, message });

/* ---------- files ---------- */

function walk(dir, rel = '') {
  const out = [];
  for (const entry of fs.readdirSync(dir, { withFileTypes: true }).sort((a, b) => a.name.localeCompare(b.name))) {
    const relPath = rel ? `${rel}/${entry.name}` : entry.name;
    if (entry.isDirectory()) out.push(...walk(path.join(dir, entry.name), relPath));
    else out.push(relPath);
  }
  return out;
}
if (!fs.existsSync(PUBLIC)) { console.error('check: public/ does not exist; run build.mjs first'); process.exit(1); }
const files = walk(PUBLIC);
const fileSet = new Set(files);
const bytesOf = (rel) => fs.statSync(path.join(PUBLIC, rel)).size;
const readPublic = (rel) => fs.readFileSync(path.join(PUBLIC, rel), 'utf8');

/* ---------- html scanning ---------- */

const ENTITIES = { amp: '&', lt: '<', gt: '>', quot: '"', apos: "'", nbsp: ' ' };
const decodeEntities = (text) => text
  .replace(/&#x([0-9a-f]+);/gi, (m, hex) => String.fromCodePoint(parseInt(hex, 16)))
  .replace(/&#(\d+);/g, (m, dec) => String.fromCodePoint(Number(dec)))
  .replace(/&([a-z]+);/gi, (m, name) => ENTITIES[name.toLowerCase()] ?? m);

const ATTR_RE = /([^\s"'<>\/=]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s"'<>`]+)))?/g;
const TAG_SOURCE = '<!--[\\s\\S]*?-->|<!doctype[^>]*>|<\\/([a-zA-Z][\\w:-]*)\\s*>|<([a-zA-Z][\\w:-]*)((?:\\s+[^\\s"\'<>\\/=]+(?:\\s*=\\s*(?:"[^"]*"|\'[^\']*\'|[^\\s"\'<>`]+))?)*)\\s*(\\/?)>';
const RAW_TEXT_TAGS = new Set(['script', 'style', 'textarea', 'title']);

function parseAttrs(text) {
  const attrs = [];
  for (const m of text.matchAll(ATTR_RE)) {
    const value = m[2] ?? m[3] ?? m[4];
    attrs.push({ name: m[1].toLowerCase(), value: value === undefined ? null : decodeEntities(value) });
  }
  return attrs;
}

function scan(html) {
  const tags = [];
  const re = new RegExp(TAG_SOURCE, 'gi');
  let m;
  while ((m = re.exec(html))) {
    if (m[0].startsWith('<!')) continue;
    if (m[1]) { tags.push({ name: m[1].toLowerCase(), closing: true, index: m.index, attrs: [] }); continue; }
    const name = m[2].toLowerCase();
    const tag = { name, attrs: parseAttrs(m[3] || ''), index: m.index, end: m.index + m[0].length, closing: false, text: '' };
    if (RAW_TEXT_TAGS.has(name) && m[4] !== '/') {
      const close = html.toLowerCase().indexOf(`</${name}`, tag.end);
      tag.text = html.slice(tag.end, close === -1 ? html.length : close);
      if (close !== -1) re.lastIndex = close;
    }
    tags.push(tag);
  }
  return tags;
}

const attr = (tag, name) => tag.attrs.find((a) => a.name === name)?.value ?? null;
const hasAttr = (tag, name) => tag.attrs.some((a) => a.name === name);
const visibleText = (html) => decodeEntities(html
  .replace(/<!--[\s\S]*?-->/g, ' ')
  .replace(/<(script|style|textarea)\b[\s\S]*?<\/\1\s*>/gi, ' ')
  .replace(/<br\s*\/?>/gi, ' ')
  .replace(/<[^>]+>/g, ' ')).replace(/\s+/g, ' ').trim();
const headingsOf = (html) => [...html.matchAll(/<(h[1-6])\b[^>]*>([\s\S]*?)<\/\1\s*>/gi)].map((m) => ({ tag: m[1].toLowerCase(), text: visibleText(m[2]) }));
const pagePathOf = (rel) => (rel === 'index.html' ? '/' : rel.endsWith('/index.html') ? `/${rel.slice(0, -'index.html'.length)}` : `/${rel}`);
const isExternal = (url) => /^(?:[a-z][a-z0-9+.-]*:|\/\/)/i.test(url);

function splitUrl(url) {
  const hashAt = url.indexOf('#');
  const hash = hashAt === -1 ? null : url.slice(hashAt + 1);
  const noHash = hashAt === -1 ? url : url.slice(0, hashAt);
  const queryAt = noHash.indexOf('?');
  let pathPart = queryAt === -1 ? noHash : noHash.slice(0, queryAt);
  try { pathPart = decodeURIComponent(pathPart); } catch { /* keep as-is */ }
  return { pathPart, hash, hasQuery: queryAt !== -1 };
}

function srcsetCandidates(value) {
  return value.split(',').map((part) => part.trim()).filter(Boolean).map((part) => {
    const [url, descriptor] = part.split(/\s+/);
    const w = descriptor && /^\d+w$/.test(descriptor) ? Number(descriptor.slice(0, -1)) : null;
    const x = descriptor && /^[\d.]+x$/.test(descriptor) ? Number(descriptor.slice(0, -1)) : null;
    return { url, weight: w ?? (x !== null ? x * 1000 : 0) };
  });
}

/* Resolve a local path (root-relative or relative to the page) to a file in public/. */
function resolveTarget(pageRel, pathPart) {
  let sitePath;
  if (pathPart === '') sitePath = pagePathOf(pageRel);
  else if (pathPart.startsWith('/')) sitePath = path.posix.normalize(pathPart);
  else sitePath = path.posix.normalize(path.posix.join(path.posix.dirname(`/${pageRel}`), pathPart));
  if (sitePath.endsWith('/')) {
    const rel = `${sitePath.slice(1)}index.html`;
    return { sitePath, rel: fileSet.has(rel) ? rel : null, exists: fileSet.has(rel), needsSlash: false };
  }
  const rel = sitePath.slice(1);
  if (fileSet.has(rel)) return { sitePath, rel, exists: true, needsSlash: false };
  if (fileSet.has(`${rel}/index.html`)) return { sitePath, rel: `${rel}/index.html`, exists: true, needsSlash: true };
  return { sitePath, rel: null, exists: false, needsSlash: false };
}

function pngSize(file) {
  try {
    const fd = fs.openSync(file, 'r');
    const header = Buffer.alloc(24);
    const read = fs.readSync(fd, header, 0, 24, 0);
    fs.closeSync(fd);
    if (read < 24 || header.toString('latin1', 1, 4) !== 'PNG') return null;
    return { width: header.readUInt32BE(16), height: header.readUInt32BE(20) };
  } catch {
    return null;
  }
}

/* ---------- budgets from front matter ---------- */

function loadBudgets() {
  const budgets = new Map();
  const dir = path.join(SRC, 'pages');
  if (!fs.existsSync(dir)) return budgets;
  for (const name of fs.readdirSync(dir).filter((n) => n.endsWith('.html')).sort()) {
    const raw = fs.readFileSync(path.join(dir, name), 'utf8');
    const m = raw.match(/^\s*<!--\s*(\{[\s\S]*?\})\s*-->/);
    if (!m) continue;
    try {
      const meta = JSON.parse(m[1]);
      if (meta.path) budgets.set(meta.path, { eager: meta.budgetEager ?? null, total: meta.budgetTotal ?? null, source: `src/pages/${name}` });
    } catch { /* build.mjs reports invalid front matter */ }
  }
  return budgets;
}
const budgets = loadBudgets();

/* ---------- css helpers ---------- */

const cssUrlCache = new Map();
function cssUrls(rel) {
  if (!cssUrlCache.has(rel)) {
    const css = readPublic(rel);
    const urls = [...css.matchAll(/url\(\s*(?:"([^"]*)"|'([^']*)'|([^)"']*))\s*\)/g)].map((m) => (m[1] ?? m[2] ?? m[3]).trim()).filter(Boolean);
    cssUrlCache.set(rel, urls);
  }
  return cssUrlCache.get(rel);
}

/* ---------- documents ---------- */

const docs = new Map();
for (const rel of files.filter((f) => f.endsWith('.html'))) {
  const html = fs.readFileSync(path.join(PUBLIC, rel), 'utf8');
  const tags = scan(html);
  const ids = new Map();
  for (const tag of tags) {
    if (tag.closing) continue;
    const id = attr(tag, 'id');
    if (id) ids.set(id, (ids.get(id) || 0) + 1);
  }
  const isStub = tags.some((tag) => tag.name === 'meta' && (attr(tag, 'http-equiv') || '').toLowerCase() === 'refresh');
  docs.set(rel, { rel, html, tags, ids, isStub, sitePath: pagePathOf(rel), text: visibleText(html) });
}

const report = { site: site.baseUrl, publicDir: 'www/website/public', pages: {}, errors, warnings, summary: {} };
const titles = new Map();

function checkPage(doc) {
  const { rel: page, html, tags, ids, isStub, sitePath } = doc;
  const budget = budgets.get(sitePath) || null;
  const stats = {
    path: sitePath,
    redirectStub: isStub,
    indexable: false,
    title: null,
    descriptionLength: null,
    images: 0,
    lazyImages: 0,
    bytes: { html: bytesOf(page), eager: 0, total: 0 },
    budgets: budget ? { eager: budget.eager, total: budget.total } : null,
    eagerAssets: [],
    lazyAssets: [],
  };
  const eager = new Map();
  const total = new Map();
  const addAsset = (bucket, target) => {
    if (!target?.exists || target.needsSlash) return;
    const size = bytesOf(target.rel);
    total.set(target.rel, size);
    if (bucket === 'eager') eager.set(target.rel, size);
  };

  const htmlTag = tags.find((tag) => tag.name === 'html' && !tag.closing);
  if (!htmlTag || !(attr(htmlTag, 'lang') || '').trim()) error(page, 'html-lang', '<html> needs a lang attribute');
  for (const [id, count] of ids) if (count > 1) error(page, 'duplicate-id', `id "${id}" appears ${count} times`);

  /* link and resource resolution */
  function checkUrl(tag, attrName, value, { localOnly = false, bucket = null } = {}) {
    const url = value.trim();
    if (!url) { warning(page, 'empty-url', `<${tag.name} ${attrName}=""> is empty`); return null; }
    if (/^(javascript|data):/i.test(url)) return null; // reported by the CSP rule
    if (/^mailto:/i.test(url)) {
      const address = url.slice(7).split('?')[0].trim();
      if (address.toLowerCase() !== String(site.email).toLowerCase()) error(page, 'mailto', `mailto: to ${address}; only ${site.email} is allowed`);
      return null;
    }
    if (/^tel:/i.test(url)) { error(page, 'tel', 'tel: links are not allowed'); return null; }
    if (isExternal(url)) {
      if (localOnly) error(page, 'csp-external-origin', `<${tag.name} ${attrName}="${url}"> loads from an external origin`);
      else if (!/^https:\/\//i.test(url)) warning(page, 'external-scheme', `<${tag.name} ${attrName}="${url}"> is not https`);
      return null;
    }
    const { pathPart, hash, hasQuery } = splitUrl(url);
    if (pathPart === '' && hash !== null && !hasQuery) {
      if (hash === '') warning(page, 'empty-fragment', `<${tag.name} ${attrName}="#"> points at the top of the page`);
      else if (!ids.has(hash)) error(page, 'anchor', `<${tag.name} ${attrName}="${url}">: no element with id "${hash}" on this page`);
      return null;
    }
    const target = resolveTarget(page, pathPart);
    if (target.sitePath.startsWith(DEMO_PREFIX)) return null; // served by the demo vhost, never in public/
    if (!target.exists) {
      error(page, 'broken-link', `<${tag.name} ${attrName}="${url}"> does not resolve to a file in public/`);
      return null;
    }
    if (target.needsSlash) warning(page, 'directory-slash', `<${tag.name} ${attrName}="${url}"> resolves to a directory; link to ${target.sitePath}/ to avoid a redirect`);
    if (hash) {
      const other = docs.get(target.rel);
      if (other && !other.ids.has(hash)) error(page, 'anchor', `<${tag.name} ${attrName}="${url}">: no element with id "${hash}" in ${target.rel}`);
    }
    if (bucket) addAsset(bucket, target);
    return target;
  }

  let canonical = null;
  let description = null;
  let robots = '';
  let ogImage = null;
  let ogWidth = null;
  let ogHeight = null;
  let twitterImage = null;
  let title = null;
  let insideHead = false;
  let headTitleCount = 0;
  let h1Count = 0;
  let imageIndex = 0;
  let insideNav = false;
  const navAnchors = [];
  const brandPrefix = '/assets/brand/';

  addAsset('eager', { exists: true, rel: page, needsSlash: false });

  for (const tag of tags) {
    if (tag.closing) {
      if (tag.name === 'nav') insideNav = false;
      if (tag.name === 'head') insideHead = false;
      continue;
    }
    for (const a of tag.attrs) {
      if (a.name === 'style') error(page, 'csp-inline-style', `<${tag.name}> carries a style attribute`);
      if (/^on[a-z]+$/.test(a.name)) error(page, 'csp-inline-handler', `<${tag.name}> carries ${a.name}=`);
      if (['href', 'src', 'srcset', 'poster', 'action', 'formaction', 'data'].includes(a.name) && a.value) {
        const candidates = a.name === 'srcset' ? srcsetCandidates(a.value).map((c) => c.url) : [a.value];
        for (const candidate of candidates) {
          if (/^\s*(javascript|data):/i.test(candidate)) error(page, 'csp-url-scheme', `<${tag.name} ${a.name}> uses a ${candidate.trim().split(':')[0].toLowerCase()}: URL`);
        }
      }
    }
    for (const name of ID_REFERENCE_ATTRS) {
      const value = attr(tag, name);
      if (value === null) continue;
      for (const id of value.split(/\s+/).filter(Boolean)) {
        if (!ids.has(id)) error(page, 'id-reference', `<${tag.name} ${name}="${value}">: no element with id "${id}"`);
      }
    }
    switch (tag.name) {
      case 'head': insideHead = true; break;
      case 'title': if (insideHead) { title = visibleText(tag.text); headTitleCount += 1; } break;
      case 'h1': h1Count += 1; break;
      case 'style': error(page, 'csp-style-element', '<style> elements are not allowed'); break;
      case 'object': case 'embed': error(page, 'csp-object', `<${tag.name}> is blocked by object-src 'none'`); break;
      case 'nav': insideNav = true; break;
      case 'a': {
        const href = attr(tag, 'href');
        if (href === null) break;
        if (insideNav) navAnchors.push(tag);
        checkUrl(tag, 'href', href);
        break;
      }
      case 'link': {
        const rels = (attr(tag, 'rel') || '').toLowerCase().split(/\s+/).filter(Boolean);
        const href = attr(tag, 'href');
        if (rels.includes('canonical')) { canonical = href; break; }
        if (href === null) break;
        const localOnly = rels.some((r) => LOCAL_ONLY_LINK_RELS.has(r));
        const bucket = rels.includes('stylesheet') || rels.includes('preload') || rels.includes('modulepreload') ? 'eager' : localOnly ? 'total' : null;
        const target = checkUrl(tag, 'href', href, { localOnly, bucket });
        if (target?.exists && rels.includes('stylesheet') && target.rel.endsWith('.css')) {
          for (const url of cssUrls(target.rel)) {
            if (/^(https?:|\/\/|data:)/i.test(url)) continue; // reported by the CSS rule
            const asset = resolveTarget(target.rel, splitUrl(url).pathPart);
            if (!asset.exists) { error(page, 'broken-link', `${target.rel}: url(${url}) does not resolve to a file in public/`); continue; }
            addAsset(FONT_FILE.test(asset.rel) ? 'eager' : 'total', asset);
          }
        }
        break;
      }
      case 'script': {
        const src = attr(tag, 'src');
        const type = (attr(tag, 'type') || '').trim().toLowerCase();
        if (src === null && type !== 'application/ld+json') error(page, 'csp-inline-script', 'inline <script> without src is not allowed');
        if (src !== null) checkUrl(tag, 'src', src, { localOnly: true, bucket: 'eager' });
        if (type === 'application/ld+json') {
          let data = null;
          try { data = JSON.parse(tag.text); } catch (e) { error(page, 'jsonld-parse', `JSON-LD does not parse: ${e.message}`); }
          if (/"(aggregateRating|review|reviews|userInteractionCount|interactionStatistic)"\s*:/.test(tag.text)) error(page, 'jsonld-ratings', 'JSON-LD must not carry ratings, reviews or interaction counts');
          if (data && data['@context'] !== 'https://schema.org') warning(page, 'jsonld-context', `JSON-LD @context is ${JSON.stringify(data['@context'])}`);
        }
        break;
      }
      case 'img': {
        const index = imageIndex;
        imageIndex += 1;
        stats.images += 1;
        const src = attr(tag, 'src');
        const srcset = attr(tag, 'srcset');
        const lazy = (attr(tag, 'loading') || '').toLowerCase() === 'lazy';
        if (lazy) stats.lazyImages += 1;
        const label = `<img${src !== null ? ` src="${src}"` : ''}${attr(tag, 'id') ? ` id="${attr(tag, 'id')}"` : ''}>`;
        if (!hasAttr(tag, 'alt')) error(page, 'img-alt', `${label} has no alt attribute`);
        if (!hasAttr(tag, 'width') || !hasAttr(tag, 'height')) error(page, 'img-dimensions', `${label} needs width and height attributes`);
        if (srcset !== null && !hasAttr(tag, 'sizes')) error(page, 'img-sizes', `${label} has srcset but no sizes`);
        if (src !== null && index >= 2 && !lazy && (attr(tag, 'fetchpriority') || '').toLowerCase() !== 'high') warning(page, 'img-lazy', `${label} is image ${index + 1} on the page and is not loading="lazy"`);
        const brand = src !== null && splitUrl(src).pathPart.startsWith(brandPrefix);
        const eagerImage = !lazy || brand;
        const candidates = srcset !== null ? srcsetCandidates(srcset) : [];
        const largest = candidates.length ? candidates.reduce((best, c) => (c.weight >= best.weight ? c : best)) : null;
        if (src !== null) checkUrl(tag, 'src', src, { localOnly: true, bucket: eagerImage && !largest ? 'eager' : 'total' });
        for (const c of candidates) checkUrl(tag, 'srcset', c.url, { localOnly: true, bucket: eagerImage && c === largest ? 'eager' : 'total' });
        break;
      }
      case 'source': case 'video': case 'audio': case 'iframe': case 'track': {
        for (const name of ['src', 'poster']) { const v = attr(tag, name); if (v !== null) checkUrl(tag, name, v, { localOnly: true, bucket: 'total' }); }
        const srcset = attr(tag, 'srcset');
        if (srcset !== null) for (const c of srcsetCandidates(srcset)) checkUrl(tag, 'srcset', c.url, { localOnly: true, bucket: 'total' });
        if (tag.name === 'iframe') warning(page, 'iframe', '<iframe> present; frame-src falls back to default-src \'self\'');
        break;
      }
      case 'form': {
        const action = attr(tag, 'action');
        if (action) checkUrl(tag, 'action', action);
        break;
      }
      case 'meta': {
        const name = (attr(tag, 'name') || '').toLowerCase();
        const property = (attr(tag, 'property') || '').toLowerCase();
        const content = attr(tag, 'content') ?? '';
        if (name === 'description') description = content;
        if (name === 'robots') robots = content.toLowerCase();
        if (property === 'og:image') ogImage = content;
        if (property === 'og:image:width') ogWidth = Number(content);
        if (property === 'og:image:height') ogHeight = Number(content);
        if (name === 'twitter:image') twitterImage = content;
        break;
      }
      default: break;
    }
  }

  /* content rules (all pages) */
  for (const [pattern, label] of FORBIDDEN) {
    const m = html.match(pattern);
    if (m) error(page, 'forbidden-string', `${label}: "${m[0]}" near "${html.slice(Math.max(0, m.index - 40), m.index + m[0].length + 40).replace(/\s+/g, ' ')}"`);
  }
  if (html.includes('/assets/screens/') && !/synthetic/i.test(doc.text)) error(page, 'synthetic-label', 'page shows product captures but never says "synthetic"');

  stats.title = title;
  if (!isStub) {
    if (headTitleCount !== 1 || !title) error(page, 'title', 'Document head needs exactly one non-empty <title>');
    else if (titles.has(title)) error(page, 'title-unique', `<title> "${title}" is also used by ${titles.get(title)}`);
    else titles.set(title, page);
    if (h1Count !== 1) error(page, 'h1-count', `page has ${h1Count} <h1> elements; exactly one is required`);
    stats.descriptionLength = description === null ? null : description.length;
    if (description === null) error(page, 'description', 'meta description is missing');
    else if (description.length < 50 || description.length > 160) error(page, 'description', `meta description is ${description.length} characters; 50-160 required`);
    const expectedCanonical = site.baseUrl + sitePath;
    if (canonical !== expectedCanonical) error(page, 'canonical', `canonical is ${canonical === null ? 'missing' : `"${canonical}"`}; expected "${expectedCanonical}"`);

    const currentLinks = navAnchors.filter((a) => (attr(a, 'aria-current') || '').toLowerCase() === 'page');
    if (!navAnchors.length) warning(page, 'nav', 'no <nav> links found');
    else if (NAV_PATHS.has(sitePath)) {
      if (currentLinks.length !== 1) error(page, 'nav-current', `nav page needs exactly one aria-current="page" link; found ${currentLinks.length}`);
      else if (splitUrl(attr(currentLinks[0], 'href') || '').pathPart !== sitePath) error(page, 'nav-current', `aria-current="page" is on "${attr(currentLinks[0], 'href')}" instead of "${sitePath}"`);
    } else if (currentLinks.length) error(page, 'nav-current', `page is not a nav route but has ${currentLinks.length} aria-current="page" link(s)`);

    if (ogImage === null) error(page, 'og-image', 'og:image is missing');
    else if (ogImage.startsWith(`${site.baseUrl}/`)) {
      const target = resolveTarget(page, ogImage.slice(site.baseUrl.length));
      if (!target.exists) error(page, 'og-image', `og:image ${ogImage} does not resolve to a file in public/`);
      else {
        const dims = pngSize(path.join(PUBLIC, target.rel));
        if (dims && (dims.width !== ogWidth || dims.height !== ogHeight)) error(page, 'og-image', `og:image is ${dims.width}x${dims.height} but og:image:width/height say ${ogWidth}x${ogHeight}`);
        else if (!dims) warning(page, 'og-image', `og:image ${ogImage} is not a PNG; dimensions were not verified`);
      }
    } else error(page, 'og-image', `og:image must be an absolute URL on ${site.baseUrl}; found ${ogImage}`);
    if (twitterImage !== null && twitterImage !== ogImage) warning(page, 'twitter-image', 'twitter:image differs from og:image');

    for (const h of headingsOf(html)) if (TWO_FRAGMENTS.test(h.text)) warning(page, 'copy-two-fragments', `<${h.tag}> "${h.text}" is two short fragments`);
    const bannedIn = (label, text) => {
      const seen = new Set();
      for (const m of (text || '').matchAll(BANNED_WORDS)) {
        const word = m[0].toLowerCase();
        if (seen.has(word)) continue;
        seen.add(word);
        warning(page, 'copy-banned-word', `${label}: "${m[0]}" near "${text.slice(Math.max(0, m.index - 30), m.index + m[0].length + 30)}"`);
      }
    };
    bannedIn('text', doc.text);
    bannedIn('title', title || '');
    bannedIn('description', description || '');
  }

  /* budgets */
  stats.bytes.eager = [...eager.values()].reduce((sum, n) => sum + n, 0);
  stats.bytes.total = [...total.values()].reduce((sum, n) => sum + n, 0);
  stats.eagerAssets = [...eager].sort().map(([file, bytes]) => ({ file, bytes }));
  stats.lazyAssets = [...total].filter(([file]) => !eager.has(file)).sort().map(([file, bytes]) => ({ file, bytes }));
  if (budget && !isStub) {
    if (budget.eager !== null && stats.bytes.eager > budget.eager) error(page, 'budget-eager', `eager bytes ${stats.bytes.eager} exceed budgetEager ${budget.eager} (${budget.source})`);
    if (budget.total !== null && stats.bytes.total > budget.total) error(page, 'budget-total', `total bytes ${stats.bytes.total} exceed budgetTotal ${budget.total} (${budget.source})`);
  }
  stats.indexable = !isStub && !robots.includes('noindex') && sitePath !== '/404.html';
  report.pages[page] = stats;
}

for (const doc of docs.values()) checkPage(doc);

/* ---------- css ---------- */
for (const rel of files.filter((f) => f.endsWith('.css'))) {
  const css = readPublic(rel);
  if (/@import\b/.test(css)) error(rel, 'css-import', '@import is not allowed');
  for (const m of css.matchAll(/url\(\s*["']?\s*(https?:|\/\/|data:)/gi)) error(rel, 'css-external-url', `url(${m[1]}...) is not allowed`);
}

/* ---------- javascript ---------- */
for (const rel of files.filter((f) => f.endsWith('.js') || f.endsWith('.mjs'))) {
  const result = spawnSync(process.execPath, ['--check', path.join(PUBLIC, rel)], { encoding: 'utf8' });
  if (result.status !== 0) error(rel, 'js-syntax', (result.stderr || 'node --check failed').trim().split('\n').slice(0, 3).join(' '));
  const js = readPublic(rel);
  for (const needle of JS_FORBIDDEN) if (js.includes(needle)) error(rel, 'js-forbidden-api', `"${needle}" is not allowed`);
}

/* ---------- sitemap and robots ---------- */
if (!fileSet.has('sitemap.xml')) error('sitemap.xml', 'sitemap', 'public/sitemap.xml is missing');
else {
  const xml = readPublic('sitemap.xml');
  const locs = [...xml.matchAll(/<loc>([^<]+)<\/loc>/g)].map((m) => decodeEntities(m[1].trim()));
  const listed = new Set();
  for (const loc of locs) {
    if (!loc.startsWith(`${site.baseUrl}/`)) { error('sitemap.xml', 'sitemap', `${loc} is not under ${site.baseUrl}`); continue; }
    const sitePath = loc.slice(site.baseUrl.length);
    listed.add(sitePath);
    const target = resolveTarget('sitemap.xml', sitePath);
    if (!target.exists || target.needsSlash) error('sitemap.xml', 'sitemap', `${loc} does not map to a file in public/`);
    else if (docs.get(target.rel)?.isStub) error('sitemap.xml', 'sitemap', `${loc} is a redirect stub`);
  }
  for (const [rel, stats] of Object.entries(report.pages)) {
    if (stats.indexable && !listed.has(stats.path)) warning(rel, 'sitemap', `${stats.path} is indexable but not in sitemap.xml`);
    if (!stats.indexable && listed.has(stats.path)) error('sitemap.xml', 'sitemap', `${stats.path} is noindex, a stub or the 404 page but is listed`);
  }
}
if (!fileSet.has('robots.txt')) warning('robots.txt', 'robots', 'public/robots.txt is missing');

/* ---------- report ---------- */
report.summary = {
  pages: docs.size,
  redirectStubs: [...docs.values()].filter((d) => d.isStub).length,
  errors: errors.length,
  warnings: warnings.length,
};
fs.mkdirSync(path.dirname(REPORT), { recursive: true });
fs.writeFileSync(REPORT, `${JSON.stringify(report, null, 2)}\n`, 'utf8');

for (const e of errors) console.log(`ERROR   ${e.page} [${e.rule}] ${e.message}`);
for (const w of warnings) console.log(`warning ${w.page} [${w.rule}] ${w.message}`);
for (const [rel, stats] of Object.entries(report.pages)) {
  const budget = stats.budgets ? ` (budgets ${stats.budgets.eager ?? '-'} / ${stats.budgets.total ?? '-'})` : '';
  console.log(`bytes   ${rel}: html ${stats.bytes.html}, eager ${stats.bytes.eager}, total ${stats.bytes.total}${budget}${stats.redirectStub ? ' [redirect stub]' : ''}`);
}
console.log(`check: ${docs.size} page(s), ${errors.length} error(s), ${warnings.length} warning(s); report ${path.relative(REPO, REPORT).split(path.sep).join('/')}`);
process.exit(errors.length ? 1 : 0);
