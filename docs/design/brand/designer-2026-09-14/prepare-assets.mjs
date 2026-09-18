// Reframe supplied SVG canvases. Embedded images and drawing data are unchanged.
import { readFile, writeFile, mkdir } from 'node:fs/promises';
import { createHash } from 'node:crypto';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const base = path.dirname(fileURLToPath(import.meta.url));
const manifest = JSON.parse(await readFile(path.join(base, 'originals/manifest.json'), 'utf8'));
const audit = JSON.parse(await readFile(path.join(base, 'originals/audit.json'), 'utf8'));
const roles = {
  12: 'symbol-dark', 13: 'symbol-light', 14: 'symbol-black', 15: 'symbol-white',
  16: 'stacked-dark', 17: 'stacked-white', 18: 'stacked-black', 19: 'stacked-light',
  20: 'horizontal-light', 21: 'horizontal-black', 22: 'horizontal-white', 23: 'horizontal-dark',
};
const out = path.join(base, 'prepared');
await mkdir(out, { recursive: true });
const derivatives = [];
for (const [number, role] of Object.entries(roles)) {
  const original = manifest.files.find(file => file.relative_path === `svg/${number}.svg`);
  const svgAudit = audit.svgs.find(file => file.file === `${number}.svg`);
  const pngAudit = audit.pngs.find(file => file.file === `${number}.png`);
  if (!original || !svgAudit?.static_security_screen_passed || !pngAudit?.alpha_bbox) {
    throw new Error(`Missing audited source for ${number}`);
  }
  const bytes = await readFile(path.join(base, 'originals', original.relative_path));
  if (createHash('sha256').update(bytes).digest('hex') !== original.sha256) {
    throw new Error(`Original hash changed: ${number}`);
  }
  const source = bytes.toString('utf8');
  const root = source.match(/^<svg\b[^>]*>/)?.[0];
  if (!root || !root.includes('viewBox="0 0 375 374.999991"')) throw new Error('Unexpected SVG canvas');
  const [left, top, right, bottom] = pngAudit.alpha_bbox;
  const contentWidth = right - left;
  const contentHeight = bottom - top;
  const margin = Math.max(8, Math.ceil(contentHeight * 0.08));
  const width = (role.startsWith('symbol') ? Math.max(contentWidth, contentHeight) : contentWidth) + margin * 2;
  const height = contentHeight + margin * 2;
  const x = left - (width - contentWidth) / 2;
  const y = top - margin;
  const viewBox = [x, y, width, height].map(value => Number((value * 0.75).toFixed(3))).join(' ');
  const revisedRoot = root.replace(/\bwidth="[^"]+"/, `width="${width}"`)
    .replace(/\bheight="[^"]+"/, `height="${height}"`)
    .replace(/\bviewBox="[^"]+"/, `viewBox="${viewBox}"`);
  const derivative = revisedRoot + source.slice(root.length);
  if (derivative.slice(revisedRoot.length) !== source.slice(root.length)) throw new Error('Drawing changed');
  const file = `phpledger-${role}.svg`;
  await writeFile(path.join(out, file), derivative);
  derivatives.push({ file, source: original.relative_path, source_sha256: original.sha256, width, height,
    viewBox, clear_space_px: margin, drawing_data_unchanged: true,
    sha256: createHash('sha256').update(derivative).digest('hex') });
}
await writeFile(path.join(out, 'manifest.json'), JSON.stringify({
  method: 'Root SVG canvas attributes only; unchanged drawing, lettering, palette and embedded PNG bytes. Bounds are derived from matching PNG alpha and require browser review.',
  derivatives,
}, null, 2) + '\n');
const cards = derivatives.map(asset => {
  const dark = /-(dark|white)\.svg$/.test(asset.file);
  return `<figure class="${dark ? 'dark' : ''}"><div class="art"><img src="${asset.file}" width="${asset.width}" height="${asset.height}" alt="${asset.file.replaceAll('-', ' ').replace('.svg', '')}"></div><figcaption>${asset.file.replace('phpledger-', '').replace('.svg', '').replaceAll('-', ' ')}<small>${asset.width} × ${asset.height} · cropped SVG canvas</small></figcaption></figure>`;
}).join('\n');
await writeFile(path.join(out, 'index.html'), `<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>PHP Ledger · prepared designer assets</title><style>
*{box-sizing:border-box}body{margin:0;padding:clamp(20px,5vw,64px);font:16px/1.6 system-ui,sans-serif;color:#0c2052;background:#f4f5f8}main{max-width:1200px;margin:auto}h1{font-size:clamp(26px,4vw,40px);margin:0 0 12px;letter-spacing:-.035em}p{max-width:850px;color:#424242}a{color:#263bbf;text-underline-offset:3px}.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px;margin-top:32px}figure{margin:0;background:white;border:1px solid #d8ddea;border-radius:6px;overflow:hidden}.art{height:220px;display:flex;align-items:center;justify-content:center;padding:25px}.art img{max-width:100%;max-height:170px;width:auto;height:auto;object-fit:contain}.dark .art{background:#0c2052}figcaption{padding:15px 20px;border-top:1px solid #d8ddea;text-transform:capitalize}small{display:block;color:#646d7f;text-transform:none}footer{margin-top:35px;font-size:14px}@media(max-width:800px){.grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:500px){.grid{grid-template-columns:1fr}}a:focus-visible{outline:3px solid #304dea;outline-offset:4px}
</style></head><body><main><h1>The designer's artwork, with usable framing.</h1><p>These 12 reusable variants preserve the supplied drawing, lettering, colours and embedded image data. Only the SVG canvas has been cropped. They are alternate supplied lockups: the preferred main header remains the existing bold PHP / lighter Ledger version.</p><p>The symbol is still bitmap artwork inside each SVG. Cropping does not make it a true vector master.</p><section class="grid" aria-label="Prepared logo variants">${cards}</section><footer><a href="README.md">Preparation and limitations</a> · <a href="manifest.json">Source and crop manifest</a></footer></main></body></html>\n`);
console.log(`Prepared ${derivatives.length} SVG canvas crops; all original hashes and drawing payloads preserved.`);
