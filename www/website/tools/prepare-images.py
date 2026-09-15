#!/usr/bin/env python
"""prepare-images.py: derive the website's image assets with Pillow only.

Run from anywhere: python www/website/tools/prepare-images.py screens og logo

Subcommands (executed in the order given):
  screens  docs/design/product-screens/NN-name.png -> public/assets/screens/<name>.webp (native width,
           quality 86, written only when missing) and <name>-720.webp (720 px wide, quality 86; skipped
           when the source is 720 px wide or narrower, e.g. the 390 px phone captures). Manifest:
           docs/design/website/qa/screens-manifest.json
  photos   docs/design/website/photos/*.jpg -> public/assets/photos/<stem>-{480,960,1440}.webp for
           landscape sources or <stem>-{480,800}.webp for portrait ones, quality 78, never upscaled.
           Manifest: docs/design/website/qa/photos-manifest.json
  og       1200x630 PNG social images in public/assets/og/: navy background, white rounded card with the
           capture fitted to 1040x470 behind a 1 px #DCE1EB border, the logo on a white plate top-left
           (40 px margin) and a white caption strip. home.png is the existing social preview, copied as-is
           when it is a real PNG and otherwise re-saved as PNG with its pixels unchanged.
  logo     public/assets/brand/phpledger-logo-600.png (600 px wide), icon-512.png, icon-192.png,
           apple-touch-icon.png (180 px) and public/favicon.ico (16, 32, 48) cropped from the book/P
           symbol of www/phpledger/public/assets/brand/phpledger-horizontal.png. The symbol is the leftmost
           run of non-white columns up to the first vertical whitespace gap; its bounding box is printed.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import re
import shutil
import sys
from pathlib import Path

from PIL import Image, ImageDraw, ImageFont, ImageOps

REPO = Path(__file__).resolve().parents[3]
WEB = REPO / "www" / "website"
PUBLIC = WEB / "public"
QA = REPO / "docs" / "design" / "website" / "qa"
SCREENS_SRC = REPO / "docs" / "design" / "product-screens"
PHOTOS_SRC = REPO / "docs" / "design" / "website" / "photos"
LOGO_SRC = REPO / "www" / "phpledger" / "public" / "assets" / "brand" / "phpledger-horizontal.png"
SOCIAL_PREVIEW = REPO / "docs" / "repository" / "assets" / "social-preview-candidate.png"

NAVY = (0x0C, 0x20, 0x52)
LINE = (0xDC, 0xE1, 0xEB)
WHITE = (255, 255, 255)
OG_SIZE = (1200, 630)
OG_FIT = (1040, 470)
OG_MARGIN = 40
OG_CAPTION = "Development preview · Synthetic sample data"
OG_INPUTS = {
    "home": None,  # docs/repository/assets/social-preview-candidate.png, copied unchanged
    "product": "01-expense.png",
    "point-of-sale": "08-point-of-sale.png",
    "download": "04-owner-overview.png",
    "support": "04-owner-overview.png",
    "roadmap": "03-trial-balance.png",
    "news": "02-journal.png",
}
SCREEN_QUALITY = 86
PHOTO_QUALITY = 78
SCREEN_SMALL_WIDTH = 720
LANDSCAPE_WIDTHS = (480, 960, 1440)
PORTRAIT_WIDTHS = (480, 800)
ARTWORK_THRESHOLD = 235  # grey levels below this count as artwork rather than white background


def rel(path: Path) -> str:
    return path.resolve().relative_to(REPO).as_posix()


def sha256(path: Path) -> str:
    digest = hashlib.sha256()
    with path.open("rb") as handle:
        for chunk in iter(lambda: handle.read(1 << 16), b""):
            digest.update(chunk)
    return digest.hexdigest()


def open_rgb(path: Path) -> Image.Image:
    with Image.open(path) as opened:
        return ImageOps.exif_transpose(opened).convert("RGB")


def resize_width(im: Image.Image, width: int) -> Image.Image:
    height = max(1, round(im.height * width / im.width))
    return im.resize((width, height), Image.Resampling.LANCZOS)


def save_webp(im: Image.Image, path: Path, quality: int) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    im.save(path, format="WEBP", quality=quality, method=6)


def save_png(im: Image.Image, path: Path) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    im.save(path, format="PNG", optimize=True)


def manifest_entry(path: Path, source: Path) -> dict:
    with Image.open(path) as im:
        width, height = im.size
    return {
        "file": rel(path),
        "source": rel(source),
        "width": width,
        "height": height,
        "bytes": path.stat().st_size,
        "sha256": sha256(path),
    }


def write_manifest(path: Path, entries: list[dict]) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    ordered = sorted(entries, key=lambda entry: entry["file"])
    path.write_text(json.dumps(ordered, indent=2) + "\n", encoding="utf-8", newline="\n")
    print(f"manifest: {rel(path)} ({len(ordered)} entries)")


def report(path: Path, note: str = "") -> None:
    with Image.open(path) as im:
        size = f"{im.width}x{im.height}"
    print(f"  wrote {rel(path)} {size}, {path.stat().st_size} bytes{(' ' + note) if note else ''}")


# ---------- screens ----------

def cmd_screens() -> None:
    entries = []
    out_dir = PUBLIC / "assets" / "screens"
    for src in sorted(SCREENS_SRC.glob("*.png")):
        match = re.fullmatch(r"\d{2}-(.+)\.png", src.name)
        if not match:
            print(f"screens: skip {src.name} (not NN-name.png)")
            continue
        name = match.group(1)
        im = open_rgb(src)
        native = out_dir / f"{name}.webp"
        if native.exists():
            print(f"screens: keep existing {rel(native)}")
        else:
            save_webp(im, native, SCREEN_QUALITY)
            report(native)
        entries.append(manifest_entry(native, src))
        if im.width > SCREEN_SMALL_WIDTH:
            small = out_dir / f"{name}-{SCREEN_SMALL_WIDTH}.webp"
            save_webp(resize_width(im, SCREEN_SMALL_WIDTH), small, SCREEN_QUALITY)
            report(small)
            entries.append(manifest_entry(small, src))
        else:
            print(f"screens: {src.name} is {im.width} px wide; no -{SCREEN_SMALL_WIDTH} sibling")
    write_manifest(QA / "screens-manifest.json", entries)


# ---------- photos ----------

def cmd_photos() -> None:
    entries = []
    out_dir = PUBLIC / "assets" / "photos"
    sources = sorted(p for p in PHOTOS_SRC.iterdir() if p.suffix.lower() in (".jpg", ".jpeg"))
    if not sources:
        print(f"photos: no JPEG files in {rel(PHOTOS_SRC)}")
    for src in sources:
        im = open_rgb(src)
        landscape = im.width >= im.height
        widths = LANDSCAPE_WIDTHS if landscape else PORTRAIT_WIDTHS
        print(f"photos: {src.name} {im.width}x{im.height} ({'landscape' if landscape else 'portrait'})")
        for width in widths:
            if width > im.width:
                print(f"  skip {width} px rendition (source is only {im.width} px wide)")
                continue
            out = out_dir / f"{src.stem}-{width}.webp"
            save_webp(resize_width(im, width), out, PHOTO_QUALITY)
            report(out)
            entries.append(manifest_entry(out, src))
    write_manifest(QA / "photos-manifest.json", entries)


# ---------- logo geometry ----------

def artwork_mask(im: Image.Image) -> Image.Image:
    return im.convert("L").point(lambda value: 255 if value < ARTWORK_THRESHOLD else 0)


def column_runs(mask: Image.Image) -> list[tuple[int, int]]:
    columns, _rows = mask.getprojection()
    runs: list[tuple[int, int]] = []
    start = None
    for x, filled in enumerate(columns):
        if filled and start is None:
            start = x
        elif not filled and start is not None:
            runs.append((start, x - 1))
            start = None
    if start is not None:
        runs.append((start, len(columns) - 1))
    return runs


def symbol_box(logo: Image.Image) -> tuple[tuple[int, int, int, int], list[tuple[int, int]]]:
    """Exclusive crop box of the leftmost artwork run of columns, limited to its own rows."""
    mask = artwork_mask(logo)
    runs = column_runs(mask)
    if not runs:
        raise SystemExit("logo: no artwork found in the horizontal logo")
    x0, x1 = runs[0]
    rows = mask.crop((x0, 0, x1 + 1, mask.height)).getbbox()
    return (x0, rows[1], x1 + 1, rows[3]), runs


def default_font(size: int):
    try:
        return ImageFont.load_default(size=size)
    except TypeError:  # Pillow < 10.1
        return ImageFont.load_default()


def logo_plate(logo: Image.Image, width: int = 220, pad: int = 12) -> Image.Image:
    bbox = artwork_mask(logo).getbbox()
    margin = 24
    crop = logo.crop((max(0, bbox[0] - margin), max(0, bbox[1] - margin), min(logo.width, bbox[2] + margin), min(logo.height, bbox[3] + margin)))
    art = resize_width(crop, width)
    plate = Image.new("RGB", (art.width + 2 * pad, art.height + 2 * pad), NAVY)
    ImageDraw.Draw(plate).rounded_rectangle((0, 0, plate.width - 1, plate.height - 1), radius=10, fill=WHITE)
    plate.paste(art, (pad, pad))
    return plate


# ---------- og ----------

def render_og(capture: Image.Image, plate: Image.Image, out: Path) -> None:
    canvas = Image.new("RGB", OG_SIZE, NAVY)
    draw = ImageDraw.Draw(canvas)
    fitted = capture.copy()
    fitted.thumbnail(OG_FIT, Image.Resampling.LANCZOS)
    pad, border = 12, 1
    card_w = fitted.width + 2 * (pad + border)
    card_h = fitted.height + 2 * (pad + border)
    left = OG_MARGIN + plate.width + 24
    x0 = left + max(0, (OG_SIZE[0] - OG_MARGIN - left - card_w) // 2)
    y0 = OG_MARGIN
    draw.rounded_rectangle((x0, y0, x0 + card_w - 1, y0 + card_h - 1), radius=14, fill=WHITE)
    fx, fy = x0 + pad, y0 + pad
    draw.rectangle((fx, fy, fx + fitted.width + 1, fy + fitted.height + 1), outline=LINE, width=border)
    canvas.paste(fitted, (fx + 1, fy + 1))
    canvas.paste(plate, (OG_MARGIN, OG_MARGIN))
    font = default_font(28)
    left_off, _top, _right, bottom = draw.textbbox((0, 0), OG_CAPTION, font=font)
    draw.text((OG_MARGIN - left_off, OG_SIZE[1] - OG_MARGIN - bottom), OG_CAPTION, font=font, fill=WHITE)
    save_png(canvas, out)


def cmd_og() -> None:
    out_dir = PUBLIC / "assets" / "og"
    out_dir.mkdir(parents=True, exist_ok=True)
    plate = logo_plate(open_rgb(LOGO_SRC))
    for slug, source in OG_INPUTS.items():
        out = out_dir / f"{slug}.png"
        if source is None:
            with Image.open(SOCIAL_PREVIEW) as opened:
                actual = opened.format
                pixels = None if actual == "PNG" else opened.convert("RGB")
            if pixels is None:
                shutil.copyfile(SOCIAL_PREVIEW, out)
                report(out, f"(copied from {rel(SOCIAL_PREVIEW)})")
            else:
                save_png(pixels, out)
                report(out, f"({rel(SOCIAL_PREVIEW)} is a {actual} file despite its name; pixels re-saved unchanged as PNG)")
            continue
        src = SCREENS_SRC / source
        if not src.exists():
            print(f"og: {slug}: {rel(src)} is missing, skipped")
            continue
        render_og(open_rgb(src), plate, out)
        report(out, f"(from {source})")


# ---------- logo ----------

def cmd_logo() -> None:
    logo = open_rgb(LOGO_SRC)
    brand = PUBLIC / "assets" / "brand"
    save_png(resize_width(logo, 600), brand / "phpledger-logo-600.png")
    report(brand / "phpledger-logo-600.png")
    box, runs = symbol_box(logo)
    width, height = box[2] - box[0], box[3] - box[1]
    gap = runs[1][0] - runs[0][1] - 1 if len(runs) > 1 else None
    print(f"logo: symbol bounding box x {box[0]}-{box[2] - 1}, y {box[1]}-{box[3] - 1} ({width}x{height} px) in a "
          f"{logo.width}x{logo.height} logo; whitespace gap after the symbol {gap} px; column runs {runs}")
    side = max(width, height)
    pad = round(side * 0.08)
    canvas_side = side + 2 * pad
    canvas = Image.new("RGB", (canvas_side, canvas_side), WHITE)
    canvas.paste(logo.crop(box), ((canvas_side - width) // 2, (canvas_side - height) // 2))
    for name, px in (("icon-512.png", 512), ("icon-192.png", 192), ("apple-touch-icon.png", 180)):
        save_png(canvas.resize((px, px), Image.Resampling.LANCZOS), brand / name)
        report(brand / name)
    favicon = PUBLIC / "favicon.ico"
    canvas.save(favicon, format="ICO", sizes=[(16, 16), (32, 32), (48, 48)])
    print(f"  wrote {rel(favicon)} (16, 32, 48 px), {favicon.stat().st_size} bytes")


COMMANDS = {"screens": cmd_screens, "photos": cmd_photos, "og": cmd_og, "logo": cmd_logo}


def main(argv: list[str]) -> int:
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("commands", nargs="+", choices=sorted(COMMANDS), help="subcommands to run, in order")
    args = parser.parse_args(argv)
    for command in args.commands:
        print(f"== {command}")
        COMMANDS[command]()
    return 0


if __name__ == "__main__":
    sys.exit(main(sys.argv[1:]))
