#!/usr/bin/env node
/* Small dependency-free content guard for the generated marketing site. */
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
const root = path.resolve(fileURLToPath(new URL('../public', import.meta.url)));
const files = [];
function walk(dir) { for (const e of fs.readdirSync(dir, {withFileTypes:true})) { const p=path.join(dir,e.name); if(e.isDirectory()) walk(p); else if(e.name==='index.html') files.push(p); } }
walk(root);
let failed = false;
for (const file of files) {
  const document = fs.readFileSync(file, 'utf8');
  const html = document.match(/<main\b[^>]*>([\s\S]*?)<\/main>/i)?.[1] || '';
  const text = html.replace(/<(script|style)[\s\S]*?<\/\1>/gi,' ').replace(/<[^>]+>/g,' ').replace(/\s+/g,' ').trim();
  const h2 = [...html.matchAll(/<h2\b/gi)].length;
  const h3 = [...html.matchAll(/<h3\b/gi)].length;
  const figures = [...html.matchAll(/<figure\b/gi)].length;
  const captions = [...html.matchAll(/<figcaption\b/gi)].length;
  const brand = (text.match(/PHP Ledger/g)||[]).length;
  const words = text.split(/\s+/).filter(Boolean).length;
  const rel = path.relative(root,file).replaceAll(path.sep,'/');
  const row = { page: rel==='index.html'?'/':`/${rel.replace(/\/index\.html$/,'/')}`, words, h2, h3, figures, captions, brandMentions: brand, summary: /class="[^"]*\bsummary\b/.test(html), contents: /class="page-toc"/.test(html) };
  if (figures !== captions) console.error(`NOTE ${row.page}: figure/figcaption mismatch (${figures}/${captions}); existing capture markup needs editorial follow-up`);
  const excluded = /<meta name="robots" content="[^"]*noindex/i.test(document) || /<meta http-equiv="refresh"/i.test(document);
  if (excluded) { console.log(JSON.stringify({ ...row, skipped: 'noindex or redirect stub' })); continue; }
  if (row.page !== '/404.html' && brand < 1) { console.error(`FAIL ${row.page}: missing PHP Ledger mention`); failed=true; }
  console.log(JSON.stringify(row));
}
process.exitCode = failed ? 1 : 0;
