"""Build original, synthetic demo histories with independent Decimal checkpoints.

No database access. --check verifies that the pinned fixtures are reproducible.
Amounts are illustrative base-currency units, with no tax/payroll jurisdiction.
"""
import argparse
import calendar
from collections import defaultdict
from decimal import Decimal
import hashlib
import json
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
DEST = ROOT / "resources" / "demo-packs"
D = Decimal


def money(value):
    return format(D(value), ".4f")


def line(code, signed, description=""):
    value = D(signed)
    assert value != 0
    return {"code": code, "debit": money(max(value, 0)),
            "credit": money(max(-value, 0)), "description": description}


def build(slug, name, business, revenue, inventory):
    accounts = [
        ("1010", "Reserve bank", "asset", "cash_bank"),
        ("1020", "Cash till", "asset", "cash_bank"),
        ("1030", "Petty cash", "asset", "cash_bank"),
        ("1200", "Prepaid insurance", "asset", None),
        ("1300", "Equipment at cost", "asset", None),
        ("1390", "Accumulated depreciation", "asset", None),
        ("2100", "Accrued staff bonus", "liability", None),
        ("2200", "Term loan", "liability", None),
        ("5100", "Fictional staff salaries", "expense", "expense"),
        ("5200", "Rent", "expense", "expense"),
        ("5300", "Utilities", "expense", "expense"),
        ("5400", "Insurance expense", "expense", "expense"),
        ("5500", "Depreciation expense", "expense", "expense"),
        ("5600", "Loan interest", "expense", "expense"),
    ]
    if inventory:
        accounts += [("1400", "Stock - manual support schedule", "asset", None),
                     ("5700", "Cost of sales - manual schedule", "expense", "expense")]
    definitions = [{"code": c, "name": n, "type": t, "role": r} for c, n, t, r in accounts]
    types = {"1000": "asset", "1100": "asset", "2000": "liability", "3000": "equity",
             "4000": "income", "5000": "expense", **{a[0]: a[2] for a in accounts}}
    events = []

    def journal(key, date, description, entries, reverse=False):
        rows = [line(*entry) for entry in entries]
        assert sum(D(r["debit"]) - D(r["credit"]) for r in rows) == 0, key
        events.append({"key": key, "kind": "general_journal", "date": date,
                       "reference": f"{slug}/{key}", "description": description,
                       "lines": rows, "reverse": reverse})

    journal("capital", "2024-01-01", "Fictional owner introduces starting capital", [("1000", 25000), ("3000", -25000)])
    journal("reserve-transfer", "2024-01-02", "Move funds between primary and reserve banks", [("1010", 12000), ("1000", -12000)])
    journal("cash-float", "2024-01-03", "Fund the cash till", [("1020", 600), ("1000", -600)])
    journal("petty-float", "2024-01-03", "Establish a separately counted petty cash float", [("1030", 500), ("1000", -500)])
    journal("equipment", "2024-02-01", "Equipment: 2400 cost, no residual, 60-month illustrative life from February 2024", [("1300", 2400), ("1000", -2400)])
    for year in (2024, 2025, 2026):
        journal(f"insurance-{year}", f"{year}-01-01", f"Prepay {year} insurance: 1200, released at 100 per month", [("1200", 1200), ("1000", -1200)])
    journal("loan-advance", "2024-03-01", "Fictional term-loan advance; separate principal and interest support", [("1000", 6000), ("2200", -6000)])
    for year in (2024, 2025):
        journal(f"loan-payment-{year}", f"{year}-12-20", "Annual loan instalment: principal 1200 and illustrative interest 300", [("2200", 1200), ("5600", 300), ("1000", -1500)])
    journal("bonus-accrual", "2024-12-31", "Accrue fictional staff bonus: Mira Vale 168 and Noel Reed 252; paid next January", [("5100", 420), ("2100", -420)])
    journal("bonus-payment", "2025-01-10", "Settle the prior-year staff bonus; no second salary expense", [("2100", 420), ("1000", -420)])
    journal("outstanding-sale", "2024-12-29", "Completed work for fictional Alder Customer: 800 outstanding at year end; manual receivable support", [("1100", 800), ("4000", -800)])
    journal("collect-prior-sale", "2025-02-10", "Collect Alder Customer's prior-year 800; no second income entry", [("1000", 800), ("1100", -800)])
    journal("outstanding-bill", "2025-12-29", "Fictional Harbor maintenance: 550 expense incurred, unpaid at year end; manual payable support", [("5000", 550), ("2000", -550)])
    journal("pay-prior-bill", "2026-01-10", "Pay Harbor's prior-year maintenance bill; no second expense", [("2000", 550), ("1000", -550)])
    journal("wrong-cost", "2025-08-20", "Correction exercise: 25 recorded instead of 45; this entry is reversed in full", [("5000", 25), ("1000", -25)], reverse=True)
    journal("correct-cost", "2025-08-21", "Replacement for wrong-cost: correct maintenance cost 45, original and reversal retained", [("5000", 45), ("1000", -45)])
    for year, amount in ((2024, 2500), (2025, 3000)):
        journal(f"cash-deposit-{year}", f"{year}-12-30", "Deposit counted till cash into primary bank; transfer has no income effect", [("1000", amount), ("1020", -amount)])
    monthly_schedule = []
    for year, month in [(y, m) for y in (2024, 2025) for m in range(1, 13)] + [(2026, 1)]:
        ym = f"{year}-{month:02d}"
        gross = D(revenue[month - 1] if isinstance(revenue, list) else revenue)
        bank = gross * D("0.9")
        events.append({"key": f"receipts-{ym}", "kind": "receipt", "date": f"{ym}-15",
                       "reference": f"{slug}/receipts-{ym}", "description": f"{business}: monthly bank receipt summary; cash sales are in the separate operating schedule",
                       "amount": money(bank), "money_code": "1000", "category_code": "4000",
                       "counterparty": "Fictional monthly customer receipts"})
        entries = [("1020", gross - bank, "Cash sales per manual daily summaries"), ("4000", bank - gross),
                   ("5100", 600, "Mira Vale, fictional staff: illustrative salary"), ("5100", 900, "Noel Reed, fictional staff: illustrative salary"),
                   ("1000", -1500), ("5200", 400), ("1010", -400), ("5300", 100),
                   ("1000", -90), ("1030", -10), ("5400", 100), ("1200", -100)]
        depreciation = 0 if ym == "2024-01" else 40
        if depreciation:
            entries += [("5500", depreciation), ("1390", -depreciation)]
        purchases, cost, units_in, units_out, unit_cost = inventory or (0, 0, 0, 0, 0)
        if inventory:
            entries += [("1400", purchases, "Manual stock purchases"), ("1000", -purchases),
                        ("5700", cost, "Manual units sold at pinned cost"), ("1400", -cost)]
        journal(f"operations-{ym}", f"{ym}-{calendar.monthrange(year, month)[1]}",
                f"{ym} manual cash, staff, rent, utilities, prepayment and depreciation support" + ("; includes stock and cost-of-sales schedule" if inventory else ""), entries)
        monthly_schedule.append({"month": ym, "gross_receipts": money(gross), "bank_receipts": money(bank),
                                 "cash_receipts": money(gross-bank), "salaries": "1500.0000", "rent": "400.0000",
                                 "utilities": "100.0000", "insurance_release": "100.0000", "depreciation": money(depreciation),
                                 "stock_purchases": money(purchases), "cost_of_sales": money(cost),
                                 "units_in": units_in, "units_out": units_out, "unit_cost": money(unit_cost)})
    events.sort(key=lambda e: (e["date"], e["key"]))
    # Expand the authored sources independently of PHP's posting/report implementation.
    postings = []
    for event in events:
        rows = event.get("lines") or [line(event["money_code"], event["amount"]), line(event["category_code"], -D(event["amount"]))]
        postings.append((event["date"], rows))
        if event.get("reverse"):
            postings.append((event["date"], [{**r, "debit": r["credit"], "credit": r["debit"]} for r in rows]))
    checkpoints = []
    for year in (2024, 2025, 2026):
        for month in range(1, 13):
            start = f"{year}-{month:02d}-01"
            end = f"{year}-{month:02d}-{calendar.monthrange(year, month)[1]}"
            balances = defaultdict(Decimal, {code: D(0) for code in types})
            income, expense = D(0), D(0)
            for date, rows in postings:
                if date <= end:
                    for row in rows:
                        signed = D(row["debit"]) - D(row["credit"])
                        balances[row["code"]] += signed
                        if start <= date:
                            if types[row["code"]] == "income": income -= signed
                            if types[row["code"]] == "expense": expense += signed
            assert sum(balances.values()) == 0
            for code in ("1000", "1010", "1020", "1030", "1100", "1200", "1300", "1400"):
                assert balances[code] >= 0, (slug, end, code, balances[code])
            # Only chart accounts enter the checkpoint; stock is absent in service packs.
            checkpoints.append({"from": start, "to": end, "balances": {code: money(balances[code]) for code in sorted(types)},
                                "income": money(income), "expenses": money(expense), "profit": money(income-expense)})
    drafts = [{"key": "practice-receipt", "kind": "receipt", "date": "2026-02-02", "amount": "225.0000", "counterparty": "Fictional new customer", "reference": "PRACTICE-RECEIPT", "memo": "Editable practice receipt. Review before posting."},
              {"key": "practice-expense", "kind": "expense", "date": "2026-02-03", "amount": "65.0000", "counterparty": "Harbor Office Supply", "reference": "PRACTICE-EXPENSE", "memo": "Editable practice expense. No effect on books until posted."},
              {"key": "practice-petty", "kind": "expense", "date": "2026-02-04", "amount": "12.5000", "money_code": "1030", "counterparty": "Fictional local stationery", "reference": "PRACTICE-PETTY", "memo": "Compare the petty cash statement before and after posting."}]
    assert 50 <= len(events) + len(drafts) <= 80
    return {"id": slug, "version": "1.0.0", "name": name, "business": business, "demo_only": True,
            "notice": "Original synthetic general-ledger examples. Manual staff, loan, outstanding-item and stock schedules do not implement payroll, AR/AP, inventory or tax modules. Period closure blocks posting; it is not statutory financial-statement approval or an earnings-transfer journal.",
            "start_date": "2024-01-01", "history_end": "2025-12-31", "practice_end": "2026-12-31",
            "source_count": len(events) + len(drafts), "journal_count": len(postings), "draft_count": len(drafts),
            "accounts": definitions, "events": events, "drafts": drafts, "monthly_support": monthly_schedule,
            "checkpoints": checkpoints}


def main():
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--check", action="store_true")
    args = parser.parse_args()
    packs = [build("service-agency", "Cedar Studio", "Service agency", 3000, None),
             build("retail-shop", "Willow Corner Shop", "Retail shop", 4500, (1050, 1000, 210, 200, 5)),
             build("seasonal-business", "Sunrise Garden Services", "Seasonal business", [1200, 1200, 2500, 4500, 5500, 6000, 6000, 5500, 4500, 2500, 1200, 1200], None),
             build("distributor", "Harbor Supply Company", "Distributor", 6000, (1700, 1600, 170, 160, 10))]
    files = {}
    catalog = []
    for pack in packs:
        filename = f"{pack['id']}-{pack['version']}.json"
        content = json.dumps(pack, indent=2, ensure_ascii=False) + "\n"
        files[filename] = content
        catalog.append({k: pack[k] for k in ("id", "version", "name", "business", "source_count", "journal_count", "draft_count")} | {"file": filename, "sha256": hashlib.sha256(content.encode()).hexdigest()})
    files["catalog.json"] = json.dumps(catalog, indent=2) + "\n"
    DEST.mkdir(parents=True, exist_ok=True)
    for filename, content in files.items():
        path = DEST / filename
        if args.check:
            if not path.exists() or path.read_text(encoding="utf-8") != content:
                raise SystemExit(f"Fixture differs: {path.relative_to(ROOT)}")
        else:
            path.write_text(content, encoding="utf-8", newline="\n")
    print(f"{'Verified' if args.check else 'Built'} {len(packs)} original packs: " + ", ".join(f"{p['id']} {p['source_count']} sources / {len(p['checkpoints'])} month checkpoints" for p in packs))


if __name__ == "__main__":
    main()
