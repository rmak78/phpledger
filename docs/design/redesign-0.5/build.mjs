// PHP Ledger 0.5 redesign prototype builder.
// Compiles Tailwind, inlines icons/fonts/logo, packs every screens/*.html fragment into one
// clickable single-file prototype: dist/index.html (standalone) and dist/artifact.html (for publishing).
import { execSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';

const root = path.dirname(new URL(import.meta.url).pathname);
const r = (p) => path.join(root, p);
const outArg = process.argv.find((a) => a.startsWith('--out='));
const out = outArg ? outArg.slice(6) : 'dist';
fs.mkdirSync(r(out), { recursive: true });

execSync(`npx @tailwindcss/cli -i src/app.css -o ${out}/app.css --minify`, { cwd: root, stdio: 'pipe' });
let css = fs.readFileSync(r(`${out}/app.css`), 'utf8');
const font = fs.readFileSync(r('src/fonts/InterVariable.woff2')).toString('base64');
css = css.replace(/url\((["']?)[^)"']*InterVariable\.woff2\1\)/g, `url(data:font/woff2;base64,${font})`);

const iconDir = r('node_modules/@tabler/icons/icons/outline');
const missingIcons = new Set();
function icon(name, cls) {
  const file = path.join(iconDir, `${name}.svg`);
  if (!fs.existsSync(file)) { missingIcons.add(name); return `<span class="icon-missing">[${name}]</span>`; }
  let svg = fs.readFileSync(file, 'utf8').replace(/\s*\n\s*/g, ' ').replace(/<path stroke="none" d="M0 0h24v24H0z" fill="none" \/>/, '');
  svg = svg.replace(/class="[^"]*"/, `class="${cls || 'icon'}" aria-hidden="true" focusable="false"`).replace(/ width="24" height="24"/, '');
  return svg;
}
const logo = 'data:image/png;base64,' + fs.readFileSync(r('src/phpledger-horizontal.png')).toString('base64');
function expand(html) {
  return html
    .replace(/<i data-icon="([a-z0-9-]+)"(?: class="([^"]*)")?\s*><\/i>/g, (_, n, c) => icon(n, c ? `icon ${c}` : 'icon'))
    .replaceAll('{{logo}}', logo);
}

const shell = expand(fs.readFileSync(r('src/shell.html'), 'utf8'));
const screens = [];
for (const f of fs.readdirSync(r('screens')).filter((f) => f.endsWith('.html')).sort()) {
  const raw = fs.readFileSync(r(`screens/${f}`), 'utf8');
  const m = raw.match(/<!--\s*meta\s*(\{[\s\S]*?\})\s*-->/);
  if (!m) { console.warn(`SKIP ${f}: no <!--meta {...} --> header`); continue; }
  let meta;
  try { meta = JSON.parse(m[1]); } catch (e) { console.warn(`SKIP ${f}: bad meta JSON (${e.message})`); continue; }
  meta.id ??= f.replace(/\.html$/, '');
  screens.push({ ...meta, body: expand(raw.replace(m[0], '')) });
}
const attr = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;');
const templates = screens.map((s) =>
  `<template id="screen-${s.id}" data-title="${attr(s.title)}" data-layout="${attr(s.layout || 'app')}" data-nav="${attr(s.nav || '')}" data-crumbs="${attr(JSON.stringify(s.crumbs || []))}" data-route="${attr(s.route || '')}" data-group="${attr(s.group || '')}" data-pattern="${attr(s.pattern || '')}" data-state="${attr(s.state || '')}">${s.body}</template>`).join('\n');
const manifest = JSON.stringify(screens.map(({ body, ...m }) => m));
const extDir = r('src/ext');
const extJs = fs.existsSync(extDir) ? fs.readdirSync(extDir).filter((f) => f.endsWith('.js')).sort().map((f) => `\n// ext/${f}\n` + fs.readFileSync(path.join(extDir, f), 'utf8')).join('') : '';
const js = fs.readFileSync(r('src/proto.js'), 'utf8') + extJs;

const title = 'PHP Ledger Redesign';
const inner = `<title>${title}</title>\n<style>${css}</style>\n<div id="app"></div>\n${shell}\n${templates}\n<script type="application/json" id="screen-manifest">${manifest}</script>\n<script>${js}</script>\n`;
const writeAtomic = (p, s) => { fs.writeFileSync(p + '.tmp', s); fs.renameSync(p + '.tmp', p); };
writeAtomic(r(`${out}/artifact.html`), inner);
writeAtomic(r(`${out}/index.html`), `<!doctype html>\n<html lang="en" dir="ltr">\n<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">\n${inner.replace('<div id="app">', '</head><body><div id="app">')}</body></html>\n`);
const kb = (fs.statSync(r(`${out}/artifact.html`)).size / 1024).toFixed(0);
console.log(`Built ${screens.length} screens · ${kb} KB${missingIcons.size ? ` · MISSING ICONS: ${[...missingIcons].join(', ')}` : ''}`);
