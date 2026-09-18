# Website photography and visual provenance

## 1.0.0 site assets — 18 September 2026

- Product captures: the 66 WebP files under `www/website/public/assets/screens/v1.0.0/` (copied from `docs/design/website-1.0/web`) are unaltered derivatives of the 1.0.0 captures recorded in `docs/design/website-1.0/screens-manifest.json`, taken from the local application with the fictional Willow Corner Shop sample. Phone captures (`m1` to `m4`) were taken at 390 px.
- Social preview cards: `www/website/tools/og-cards.py` (Pillow) draws each 1200 by 630 card from the supplied logo, a title and the canvas colour; no photographs or generated imagery. Output in `www/website/src/static/assets/og/`.
- Icons: seven Tabler-style outline icons were added to `www/website/src/icons/` (check, x, mail, package, message, rss, clipboard) under the MIT licence noted in `src/icons/SOURCE.txt`, alongside the existing brand icons.
- Photographs: none are displayed on the rebuilt pages. The eleven Pexels photographs below and their renditions remain in the repository with their provenance; a new photo pack from the owner will be recorded here before use.
## Current application captures — 18 September 2026

The current product pages use fresh 0.6.0-preview captures from the local application at `127.0.0.1:18219`. Willow Corner Shop and Cedar Studio were created through the local sample chooser. Eighteen captures and 36 resized WebP assets are indexed, captioned and hashed in `www/website/public/assets/screens/v0.6.0-preview/manifest.json`. The source PNGs are in `output/playwright/website-060-*.png`. No interface elements or financial figures were composited or generated. The current social preview places the actual Home capture on a labelled background.

The POS screenshots show an isolated local sample notebook/pen sale, including cart, cash review and receipt. Cedar Studio guide figures were checked against the fresh sample. Historical release screenshots and photography retain their existing provenance. See `www/website/design-qa.md` for the local browser checks; this refresh has not been published.

## Redesign completion: 15 September 2026

The completed source under `www/website/src` preserves the inherited photographs, responsive derivatives, application captures and supplied brand assets. No new image generation, download or source-image alteration was performed in this completion pass. `/credits/` now credits all eleven prepared photographs and the local Manrope, Poppins and Inter fonts, whose OFL notices are included under `public/assets/fonts`. Application typography is unchanged. Actual setup/receipt/reversal image dimensions were checked against `qa/screens-manifest.json` and corrected in the new page markup. Current local browser evidence is in `qa/redesign-*.png` and the JSON receipts; historical captures below remain preserved.

Checked 2026-09-14. These are real photographs downloaded for the local PHP Ledger website design and implementation, not generated people or evidence that the pictured businesses use PHP Ledger. Keep each downloaded source intact. Responsive web renditions must retain this provenance and must not imply endorsement.

## License

All three source pages mark the photographs free to use under the [Pexels license](https://www.pexels.com/license/). The license permits free use and modification on websites and in product promotion; attribution is appreciated rather than required. It prohibits implied endorsement, offensive portrayals of identifiable people, selling unaltered copies, redistribution as stock, and using photographs as a trademark. These photographs retain their own license; a future PHP Ledger software license must not relabel them.

Use a visible photography credit link in the website footer and describe scenes as illustrative photography. Do not attach customer quotes, satisfaction claims, or PHP Ledger identity to the people or their businesses. Source-page descriptions and a stock license are not evidence of customer consent to endorsement.

## Preserved downloads

| Asset | Photographer / source | Verified source context | Dimensions / bytes | SHA-256 |
|---|---|---|---|---|
| [Kolkata shop](photos/kolkata-shop-gorky-sinha.jpg) | Gorky Sinha · [Pexels photo 35168686](https://www.pexels.com/photo/street-shop-in-kolkata-capturing-daily-life-35168686/) | Working shop in Kolkata, India, as stated on its source page | 5126 × 3583 / 1,737,387 | `DD6D6DED96B1A13E7EA00EE8A867DB39402DE5EA05C8763F8801DD487C0BCF10` |
| [Café](photos/cafe-rachel-claire.jpg) | Rachel Claire · [Pexels photo 5491053](https://www.pexels.com/photo/faceless-barista-working-at-counter-5491053/) | Barista working at a café counter; no country claim is made | 5472 × 3648 / 1,977,646 | `ABC41E9F90FDEFA017A961D9F1F3771256EF5AA7F191577DB06D3F9415529645` |
| [Workshop](photos/workshop-tima-miroshnichenko.jpg) | Tima Miroshnichenko · [Pexels photo 5059639](https://www.pexels.com/photo/carpenter-working-in-workshop-5059639/) | Woodworker sanding a plank while wearing protective equipment; no country claim is made | 4000 × 6000 / 2,743,202 | `565982B8549A12ED0E7D54C71542C3F0B94FB6951072C8CD1C0CCFB5E13106CD` |

The images were downloaded from the source pages' actual **Free download** links:

- [Kolkata download](https://images.pexels.com/photos/35168686/pexels-photo-35168686.jpeg?cs=srgb&dl=pexels-gorky-35168686.jpg&fm=jpg)
- [Café download](https://images.pexels.com/photos/5491053/pexels-photo-5491053.jpeg?cs=srgb&dl=pexels-rachel-claire-5491053.jpg&fm=jpg)
- [Workshop download](https://images.pexels.com/photos/5059639/pexels-photo-5059639.jpeg?cs=srgb&dl=pexels-tima-miroshnichenko-5059639.jpg&fm=jpg)

Visual inspection confirmed natural photographic content and the expected people/workplace scenes. Downloaded files are unchanged. Visible third-party names and marks inside a photograph are part of the scene, not partners or endorsements.

## Other references

- [Designer-supplied logo](../brand/logo-designer-reference.png): user-supplied reference, preserved elsewhere. Final vector variants remain pending; do not replace it with an invented symbol.
- [Approved Review Console concept](../brand/06-review-console.png): generated application design reference containing sample company data. This is not a screenshot of completed product behavior.
- Exact implementation colors: navy `#0C2052`, charcoal `#424242`, proposed action blue `#4656E8`; Inter typography per [Brand](../../BRAND.md).

Homepage compositions created from these references are generated design proposals. Production photography must use the preserved real source images, and product walkthroughs must use verified captures of the implemented sprint journey before they are described as working screens. Do not extract generated people or reconstructed interface pixels from a composition and pass them off as documentary evidence.

No accounts were created, forms submitted, messages sent, money collected, or live site changed to obtain these materials.

## Local web renditions and font preparation

Responsive photographs were downloaded from the same Pexels image service with width, WebP encoding and quality parameters. They remain real source photographs; no AI retouching, scene replacement, or generated-person asset is used. Files are local so the future page makes no third-party image requests. The full downloads above remain unchanged.

| Local asset under `www/website/public/assets/photos` | Rendition source | Dimensions | Bytes |
|---|---|---|---|
| `kolkata-shop-480.webp` | [Pexels rendition](https://images.pexels.com/photos/35168686/pexels-photo-35168686.jpeg?auto=compress&cs=tinysrgb&w=480&q=78&fm=webp) | 480 × 336 | 25,856 |
| `kolkata-shop-960.webp` | [Pexels rendition](https://images.pexels.com/photos/35168686/pexels-photo-35168686.jpeg?auto=compress&cs=tinysrgb&w=960&q=78&fm=webp) | 960 × 671 | 70,412 |
| `kolkata-shop-1440.webp` | [Pexels rendition](https://images.pexels.com/photos/35168686/pexels-photo-35168686.jpeg?auto=compress&cs=tinysrgb&w=1440&q=78&fm=webp) | 1440 × 1007 | 131,370 |
| `cafe-960.webp` | [Pexels rendition](https://images.pexels.com/photos/5491053/pexels-photo-5491053.jpeg?auto=compress&cs=tinysrgb&w=960&q=78&fm=webp) | 960 × 640 | 65,910 |
| `workshop-640.webp` | [Pexels rendition](https://images.pexels.com/photos/5059639/pexels-photo-5059639.jpeg?auto=compress&cs=tinysrgb&w=640&q=78&fm=webp) | 640 × 960 | 40,784 |

The local app's `InterVariable.woff2` was subset for English/Latin website content using FontTools 4.63.0 and Brotli 1.2.0. Retained ranges are `U+0000-024F,U+2000-206F,U+20A0-20CF,U+2190-21FF`, with layout features retained. The result is `www/website/public/assets/fonts/inter-latin.woff2` (156,032 bytes), accompanied by the unchanged [SIL Open Font License](../../../www/website/public/assets/fonts/LICENSE.txt). Tabular numerals (`tnum`), optical sizing 14–32 and weight range 100–900 were verified in the output. This Latin subset is not a claim of Urdu, Arabic, Devanagari or other script support.

- Source font internal version: `Version 4.001;git-9221beed3`.
- Source font SHA-256: `693B77D4F32EE9B8BFC995589B5FAD5E99ADF2832738661F5402F9978429A8E3`.
- Website subset SHA-256: `6AFE52FC6A5AF99E93BB7242969B1ADD80967DC33B5383A0F08EEA926F36C6CE`.

Only asset preparation occurred before composition selection. These file sizes are a budget input, not a measured webpage load or performance result. Initial font-tool attempts found missing FontTools/Brotli in the bundled runtime; the available Python 3.14 FontTools installation and a project-cache Brotli dependency produced the verified final subset. No runtime dependency on Python or these build tools is introduced into the static website.

## Implemented product captures and identity

### Header framing, 14 September 2026

The user retained the existing horizontal mark with bold PHP and lighter Ledger. Header presentation now frames the unchanged 2172 × 724 source at **x=285, y=125, width=1620, height=475**, leaving a small clear margin around the artwork. CSS positions the original image inside an overflow-hidden wrapper; no raster pixels were edited. The existing 640 × 213 website WebP uses equivalent proportions. Application and typography source PNGs share SHA-256 `859A749124E98C121B096CC37E24AB70D095225FE4B4DA8B0554ECA50051C6F4`; the website WebP still matches the hash recorded below. Stylesheet version queries refresh cached CSS while retaining existing asset paths, including the demo prefix. Application fonts remain unchanged.

Evidence: [app desktop](qa/brand-framing/app-desktop.png), [app mobile](qa/brand-framing/app-mobile.png), [website desktop](qa/brand-framing/website-desktop.png), [website mobile](qa/brand-framing/website-mobile.png), [typography desktop](../typography-review/logo-framed-desktop.png), [typography mobile](../typography-review/logo-framed-mobile.png). These are local preview checks; publication remains held.

The local website assets use actual application captures from 2026-09-14. All company/person/transaction data is sample. Original PNGs remain under `docs/design/product-screens`. The initial three captures use WebP quality 91; the owner overview uses quality 84 and the other expanded captures quality 88, retaining their actual dimensions. No reconstructed UI is used as a working product screenshot. Current captures are development baselines under review, not evidence that the user accepted the final reports/POS design.

| Website asset | Original capture | Dimensions | Bytes | Visible evidence |
|---|---|---|---|---|
| `assets/screens/expense.webp` | [01-expense.png](../product-screens/01-expense.png) | 1425 × 1106 | 68,930 | Posted `EXP-000002`, Harbor Office Supply, USD 125, 14 September 2026; this is an already-recorded expense, not the draft editor |
| `assets/screens/journal.webp` | [02-journal.png](../product-screens/02-journal.png) | 1425 × 1008 | 42,188 | Journal `PL-00000002`, USD 125 debit/credit and original source link |
| `assets/screens/trial-balance.webp` | [03-trial-balance.png](../product-screens/03-trial-balance.png) | 1425 × 1074 | 56,660 | USD 1,000 receipt less USD 125 expense gives USD 875 bank; equal USD 1,000 totals |
| `assets/screens/owner-overview.webp` | [04-owner-overview.png](../product-screens/04-owner-overview.png) | 1425 × 1188 | 63,500 | Cash 875, income 1,000, expenses 125 and profit 875; links to posted reports |
| `assets/screens/profit-loss.webp` | [05-profit-loss.png](../product-screens/05-profit-loss.png) | 1440 × 1200 | 54,788 | The same posted income/expense/profit with account detail |
| `assets/screens/balance-sheet.webp` | [06-balance-sheet.png](../product-screens/06-balance-sheet.png) | 1440 × 1200 | 55,862 | Assets 875, liabilities 0 and equity including earnings 875 |
| `assets/screens/cash-forecast.webp` | [07-cash-forecast.png](../product-screens/07-cash-forecast.png) | 1425 × 1188 | 58,422 | Editable scenario: 875 starting cash, 250 weekly inflow and 175 outflow over 12 weeks; not a prediction |
| `assets/screens/point-of-sale.webp` | [08-point-of-sale.png](../product-screens/08-point-of-sale.png) | 1425 × 1188 | 74,268 | Unposted notebook/pen basket 5.75, tender 10, change 4.25; separate sample company remains unchanged |

The interim horizontal logo source is retained unchanged at `www/phpledger/public/assets/brand/phpledger-horizontal.png`. Its website rendition is `assets/brand/phpledger-horizontal.webp` (640 × 213, 5,720 bytes, SHA-256 `EE010E798706FF666E4503FFE8235EAFA12EC23CADEBFEAB6D51ED1A618F8F8A`), made by ordinary proportional resize and WebP compression. This does not replace the designer's pending production vector artwork. Unused concept-screen and oversized logo copies were removed from the public website folder after their source files were verified as preserved.

## Redesign photographs, 15 September 2026

Eight additional real photographs were downloaded from their Pexels pages' original-size download links on 15 September 2026 for the product-led redesign. Each remains under the [Pexels license](https://www.pexels.com/license/): illustrative use only, no implied endorsement, photographer credited on the page. The originals are preserved unchanged; responsive WebP renditions (landscape 480/960/1440, portrait 480/800, quality 78) are generated by `www/website/tools/prepare-images.py photos` and listed with dimensions, bytes and SHA-256 in [`qa/photos-manifest.json`](qa/photos-manifest.json). Captions name the scene and photographer, and a place only where the source page states it. Machine-readable metadata for the eight downloads is in [`photos/downloads-2026-09-15.json`](photos/downloads-2026-09-15.json).

| Asset | Photographer / source | Source-page description | Dimensions / bytes | SHA-256 |
|---|---|---|---|---|
| [man-sorting-through-pile-of-groceries-14040401.jpg](photos/man-sorting-through-pile-of-groceries-14040401.jpg) | Fahad Ali · [Pexels photo 14040401](https://www.pexels.com/photo/man-sorting-through-pile-of-groceries-14040401/) | A local vendor organizing groceries outside a shop in Punjab, showcasing everyday life in Pakistan. | 3341 × 4471 / 2,039,631 | `A22FA10554F362FB296D7543A9E760EEBEE5A85339C8A9A753F6A26712E560BE` |
| [salesman-standing-at-counter-in-store-18240432.jpg](photos/salesman-standing-at-counter-in-store-18240432.jpg) | Ahmet Hezretov (Istanbul) · [Pexels photo 18240432](https://www.pexels.com/photo/salesman-standing-at-counter-in-store-18240432/) | Customers gather around a cash desk in a bustling Ankara convenience store at night. | 3991 × 5987 / 1,436,445 | `FAB0653194E6D0C2E95918A4B256BD74A47E8CC44499C0083F26860CC8D2C7E0` |
| [man-working-in-grocery-store-12326636.jpg](photos/man-working-in-grocery-store-12326636.jpg) | Kenan Turguç (İstanbul) · [Pexels photo 12326636](https://www.pexels.com/photo/man-working-in-grocery-store-12326636/) | Man managing transactions in a vibrant small grocery store, capturing the essence of local retail. | 4000 × 6000 / 2,883,805 | `D5508215260BC56E9B6B6AA5F34148F113ACC431D775AB25BB8E9ACE0D4A2D54` |
| [men-in-store-3025495.jpg](photos/men-in-store-3025495.jpg) | Aa Dil (Faisalabad, Pakistan) · [Pexels photo 3025495](https://www.pexels.com/photo/men-in-store-3025495/) | Colorful street market in Faisalabad at night, showcasing local vendors and vibrant lights. | 6115 × 4077 / 2,631,350 | `12B808F69784909FDBD3E70E490E26DEF9848982281267821427A9BC89DBDD18` |
| [man-preparing-a-hot-drink-at-a-market-18413481.jpg](photos/man-preparing-a-hot-drink-at-a-market-18413481.jpg) | GOWTHAM AGM · [Pexels photo 18413481](https://www.pexels.com/photo/man-preparing-a-hot-drink-at-a-market-18413481/) | A man making traditional masala chai in an Indian tea stall, surrounded by steam and kitchen utensils. | 6000 × 4000 / 3,770,164 | `CC8356EDF62D3F0557979D7896914D8DBF305D26B2B6526E6F34F3D4A3D7A74C` |
| [tailor-sewing-clothes-with-industrial-machine-34211795.jpg](photos/tailor-sewing-clothes-with-industrial-machine-34211795.jpg) | İrem Çevik · [Pexels photo 34211795](https://www.pexels.com/photo/tailor-sewing-clothes-with-industrial-machine-34211795/) | A skilled tailor focuses on sewing using an industrial sewing machine, indoors. | 4928 × 3264 / 2,517,360 | `6456D2459CF1E4DF5FB8239852B9EF942B838E9F13ABD63817EFF3574036079E` |
| [focused-young-asian-female-bartender-typing-on-laptop-4350167.jpg](photos/focused-young-asian-female-bartender-typing-on-laptop-4350167.jpg) | Ketut Subiyanto · [Pexels photo 4350167](https://www.pexels.com/photo/focused-young-asian-female-bartender-typing-on-laptop-4350167/) | A focused young Asian female bartender uses her laptop while working in a bustling bar. | 3166 × 3922 / 1,574,065 | `B57AC57B88A94E7AD2A4711D56BCF7D785E8FC2D10979C57593F70010F2DF240` |
| [man-sitting-inside-a-store-13326556.jpg](photos/man-sitting-inside-a-store-13326556.jpg) | Ankit Rainloure (Gujarat india) · [Pexels photo 13326556](https://www.pexels.com/photo/man-sitting-inside-a-store-13326556/) | Elderly vendor in Pahur, India, managing a local small business stall. | 3936 × 2624 / 1,483,963 | `D56B30A5EB6BD600CB19D1E03D31E06F94E5CC8133904D2D3CD0C7BF606697A2` |

Renditions generated on 15 September 2026: 28 WebP files under `www/website/public/assets/photos/`, covering the three earlier photographs and the eight new ones. The earlier hand-prepared renditions (`kolkata-shop-*.webp`, `cafe-960.webp`, `workshop-640.webp`) are superseded and removed once no page references them.
