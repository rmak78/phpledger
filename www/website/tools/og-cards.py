"""Generate 1200x630 Open Graph cards for the main pages: light canvas, the logo, a navy title and a blue accent.
Usage: python tools/og-cards.py  (writes src/static/assets/og/<slug>.png)"""
import os
from PIL import Image, ImageDraw, ImageFont
OUT = 'src/static/assets/og'
LOGO = 'public/assets/brand/phpledger-logo-600.png'
CARDS = {
    'home': ('Complete double-entry accounting', 'that runs on your own PHP hosting', 'Open source under AGPL. PHP 8.2+, MySQL or MariaDB. Version 1.1.0.'),
    'product': ('Everything in PHP Ledger 1.0,', 'workflow by workflow', 'Invoices, bills, purchase orders, stock, banking, reports, setup.'),
    'point-of-sale': ('A cash counter that posts', 'through the same books', 'Cart, cash and change, receipt, one balanced journal.'),
    'download': ('Download PHP Ledger 1.1.0', 'unzip it into any web folder', 'PHP 8.2+, MySQL 8.4 or MariaDB 10.4+, signed updates.'),
    'pricing': ('Free to run, with paid help', 'when you want it', 'AGPL-3.0-or-later. Commercial licence and services on request.'),
    'community': ('Built in the open, with people', 'who keep real books', 'Discussions, issues, reviews, pilots and the social channels.'),
    'learn': ('Learn the bookkeeping behind', 'an explainable business', 'Ten lessons, worked guides and a glossary with original examples.'),
    'compare': ('Compare accounting software', 'by the work it must do', 'Licence, hosting, daily workflows and recovery, with fair limits.'),
    'news': ('PHP Ledger release news', 'and project updates', 'Every release with what changed and what it fixes.'),
    'roadmap': ('From a first stable release', 'to independently reviewed books', 'Review gates instead of dates. Published limits.'),
}
def font(size, bold=True):
    for name in (['C:/Windows/Fonts/segoeuib.ttf'] if bold else ['C:/Windows/Fonts/segoeui.ttf']) + ['C:/Windows/Fonts/arialbd.ttf', 'C:/Windows/Fonts/arial.ttf']:
        if os.path.exists(name):
            return ImageFont.truetype(name, size)
    return ImageFont.load_default()
os.makedirs(OUT, exist_ok=True)
logo = Image.open(LOGO).convert('RGBA')
logo.thumbnail((330, 110))
for slug, (line1, line2, sub) in CARDS.items():
    im = Image.new('RGB', (1200, 630), '#F7F5F0')
    d = ImageDraw.Draw(im)
    d.rectangle([0, 0, 1200, 10], fill='#4656E8')
    im.paste(logo, (80, 70), logo)
    f1 = font(58); f2 = font(30, bold=False); f3 = font(24, bold=False)
    d.text((80, 240), line1, font=f1, fill='#0C2052')
    d.text((80, 312), line2, font=f1, fill='#0C2052')
    d.text((80, 420), sub, font=f2, fill='#2A2F3A')
    d.line([80, 520, 1120, 520], fill='#DCE1EB', width=2)
    d.text((80, 545), 'phpledger.com', font=f3, fill='#5B6478')
    d.text((1120 - d.textlength('Open-source, self-hosted accounting', font=f3), 545), 'Open-source, self-hosted accounting', font=f3, fill='#5B6478')
    im.save(os.path.join(OUT, slug + '.png'), optimize=True)
    print(slug, os.path.getsize(os.path.join(OUT, slug + '.png')) // 1024, 'kB')
