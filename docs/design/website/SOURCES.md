# Website photography and visual provenance

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
- [Approved Review Console concept](../brand/06-review-console.png): generated application design reference containing synthetic company data. This is not a screenshot of completed product behavior.
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

The local website assets use actual application captures from 2026-09-14. All company/person/transaction data is synthetic. Original PNGs remain under `docs/design/product-screens`. The initial three captures use WebP quality 91; the owner overview uses quality 84 and the other expanded captures quality 88, retaining their actual dimensions. No reconstructed UI is used as a working product screenshot. Current captures are development baselines under review, not evidence that the user accepted the final reports/POS design.

| Website asset | Original capture | Dimensions | Bytes | Visible evidence |
|---|---|---|---|---|
| `assets/screens/expense.webp` | [01-expense.png](../product-screens/01-expense.png) | 1425 × 1106 | 68,930 | Posted `EXP-000002`, Harbor Office Supply, USD 125, 14 September 2026; this is an already-recorded expense, not the draft editor |
| `assets/screens/journal.webp` | [02-journal.png](../product-screens/02-journal.png) | 1425 × 1008 | 42,188 | Journal `PL-00000002`, USD 125 debit/credit and original source link |
| `assets/screens/trial-balance.webp` | [03-trial-balance.png](../product-screens/03-trial-balance.png) | 1425 × 1074 | 56,660 | USD 1,000 receipt less USD 125 expense gives USD 875 bank; equal USD 1,000 totals |
| `assets/screens/owner-overview.webp` | [04-owner-overview.png](../product-screens/04-owner-overview.png) | 1425 × 1188 | 63,500 | Cash 875, income 1,000, expenses 125 and profit 875; links to posted reports |
| `assets/screens/profit-loss.webp` | [05-profit-loss.png](../product-screens/05-profit-loss.png) | 1440 × 1200 | 54,788 | The same posted income/expense/profit with account detail |
| `assets/screens/balance-sheet.webp` | [06-balance-sheet.png](../product-screens/06-balance-sheet.png) | 1440 × 1200 | 55,862 | Assets 875, liabilities 0 and equity including earnings 875 |
| `assets/screens/cash-forecast.webp` | [07-cash-forecast.png](../product-screens/07-cash-forecast.png) | 1425 × 1188 | 58,422 | Editable scenario: 875 starting cash, 250 weekly inflow and 175 outflow over 12 weeks; not a prediction |
| `assets/screens/point-of-sale.webp` | [08-point-of-sale.png](../product-screens/08-point-of-sale.png) | 1425 × 1188 | 74,268 | Unposted notebook/pen basket 5.75, tender 10, change 4.25; separate synthetic company remains unchanged |

The interim horizontal logo source is retained unchanged at `www/phpledger/public/assets/brand/phpledger-horizontal.png`. Its website rendition is `assets/brand/phpledger-horizontal.webp` (640 × 213, 5,720 bytes, SHA-256 `EE010E798706FF666E4503FFE8235EAFA12EC23CADEBFEAB6D51ED1A618F8F8A`), made by ordinary proportional resize and WebP compression. This does not replace the designer's pending production vector artwork. Unused concept-screen and oversized logo copies were removed from the public website folder after their source files were verified as preserved.
