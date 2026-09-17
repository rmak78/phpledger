// Usage: node shots.mjs [screen-id ...] [--dist=dist-x] [--sizes=1366x768,1024x768,768x1024] [--full]
// Viewport screenshots to shots/<id>-<w>x<h>.png plus a fold/overflow report.
// Mark elements that must be visible without scrolling with data-fold (e.g. primary actions, totals).
import { chromium } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';
const root = path.dirname(new URL(import.meta.url).pathname);
const args = process.argv.slice(2);
const sizes = (args.find((a) => a.startsWith('--sizes='))?.slice(8) || '1366x768,1024x768').split(',').map((s) => s.split('x').map(Number));
const full = args.includes('--full');
const dist = args.find((a) => a.startsWith('--dist='))?.slice(7) || 'dist';
let ids = args.filter((a) => !a.startsWith('--'));
const manifest = JSON.parse(fs.readFileSync(path.join(root, dist, 'index.html'), 'utf8').match(/<script type="application\/json" id="screen-manifest">([\s\S]*?)<\/script>/)[1]);
if (!ids.length) ids = manifest.map((m) => m.id);
fs.mkdirSync(path.join(root, 'shots'), { recursive: true });
const browser = await chromium.launch({ executablePath: '/opt/pw-browsers/chromium-1194/chrome-linux/chrome' });
let problems = 0;
for (const [w, h] of sizes) {
  const page = await browser.newPage({ viewport: { width: w, height: h } });
  const errors = [];
  page.on('pageerror', (e) => errors.push(e.message));
  page.on('console', (m) => m.type() === 'error' && errors.push(m.text()));
  await page.goto('file://' + path.join(root, dist, 'index.html'));
  for (const id of ids) {
    errors.length = 0;
    await page.evaluate((id) => { location.hash = '#/' + id; }, id);
    await page.waitForTimeout(120);
    const report = await page.evaluate(() => {
      const vh = innerHeight, vw = innerWidth;
      const fold = [...document.querySelectorAll('[data-fold]')].filter((el) => el.offsetParent !== null || el.getClientRects().length).map((el) => {
        const b = el.getBoundingClientRect();
        return { label: el.dataset.fold || el.textContent.trim().slice(0, 40), ok: b.bottom <= vh + 1 && b.top >= 0 && b.right <= vw + 1 && b.left >= -1 };
      });
      const overflowX = document.documentElement.scrollWidth > vw + 1;
      // The app shell keeps its own scroll region (topbar/sidebar stay put, [data-slot="content"]
      // scrolls) so document.scrollHeight alone under-reports height for layout="app" screens.
      const content = document.querySelector('[data-slot="content"]');
      const shell = document.querySelector('[data-shell]');
      const chrome = shell ? (shell.querySelector('.shell-topbar')?.offsetHeight || 0) + (shell.querySelector('.shell-strips')?.offsetHeight || 0) : 0;
      const pageHeight = shell && content ? chrome + content.scrollHeight : document.documentElement.scrollHeight;
      const found = !document.querySelector('[data-screen-missing]');
      return { fold, overflowX, pageHeight, found };
    });
    const file = path.join(root, 'shots', `${id}-${w}x${h}.png`);
    if (full && report.pageHeight > h) {
      // Grow the real viewport to the content's natural height so the shell's internal scroll
      // region never needs to scroll, then take an ordinary (non-cropped) screenshot of it.
      await page.setViewportSize({ width: w, height: Math.ceil(report.pageHeight) + 8 });
      await page.waitForTimeout(30);
      await page.screenshot({ path: file, fullPage: false });
      await page.setViewportSize({ width: w, height: h });
    } else {
      await page.screenshot({ path: file, fullPage: false });
    }
    const bad = report.fold.filter((f) => !f.ok);
    const flags = [];
    if (!report.found) flags.push('SCREEN NOT FOUND');
    if (report.overflowX) flags.push('HORIZONTAL OVERFLOW');
    if (!report.fold.length) flags.push('no data-fold markers');
    if (bad.length) flags.push('below fold: ' + bad.map((b) => b.label).join(' | '));
    if (errors.length) flags.push('JS errors: ' + errors.join(' | '));
    if (flags.length && !(flags.length === 1 && flags[0] === 'no data-fold markers')) problems++;
    console.log(`${flags.length ? '✗' : '✓'} ${id} @${w}x${h} height=${report.pageHeight}${flags.length ? ' — ' + flags.join('; ') : ''}`);
  }
  await page.close();
}
await browser.close();
console.log(problems ? `${problems} screen/size combinations need attention` : 'All checked screens pass fold/overflow/error checks');
