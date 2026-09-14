"""Check the local POS browser routes in an existing synthetic HTTP Acceptance company.

Uses only http://127.0.0.1:18200 with in-memory cookies. Set PL_HTTP_EMAIL and
PL_HTTP_PASSWORD privately. Leaves one posted synthetic sale for inspection.
Never opens a browser, resets a database, collects money, or contacts a provider.
"""

from __future__ import annotations

import argparse
from datetime import datetime, timezone
from decimal import Decimal
import importlib.util
import json
import os
from pathlib import Path
import sys
from urllib.parse import parse_qs, urlparse


spec = importlib.util.spec_from_file_location("local_http_smoke", Path(__file__).with_name("http-smoke.py"))
http = importlib.util.module_from_spec(spec)
sys.modules[spec.name] = http
spec.loader.exec_module(http)


def run(company_id: int) -> dict:
    if company_id < 1:
        raise RuntimeError("Choose an existing synthetic HTTP Acceptance company.")
    email, password = os.environ.get("PL_HTTP_EMAIL", ""), os.environ.get("PL_HTTP_PASSWORD", "")
    if not email or not password:
        raise RuntimeError("Set PL_HTTP_EMAIL and PL_HTTP_PASSWORD privately before running.")
    session = http.Session()
    checks = []

    def check(condition, name):
        if not condition:
            raise AssertionError(name)
        checks.append(name)
        print("PASS:", name, flush=True)

    login = session.request("/login")
    signed_in = session.submit(login.markup.form_for("/login"), {"email": email, "password": password})
    check(signed_in.status == 200 and urlparse(signed_in.url).path == "/companies", "Login succeeds in an independent local session")
    form = next((form for form in signed_in.markup.forms if urlparse(form.action).path == "/company/select" and form.fields.get("company_id") == str(company_id)), None)
    if form is None:
        raise RuntimeError("The requested synthetic company is unavailable to this local account.")
    selected = session.submit(form)
    check(selected.status == 200 and "HTTP Acceptance " in selected.body, "Only the selected HTTP Acceptance business is used")
    date = datetime.now(timezone.utc).date().isoformat()
    before = http.totals(session.request("/reports/trial-balance?as_of=" + date))
    shop = session.request("/pos")
    checkout = shop.markup.form_for("/pos/checkout")
    check(shop.status == 200 and shop.body.count("data-pos-product data-sku=") == 6 and "Sample catalog" in shop.body, "Shop renders six products and an explicit sample boundary")
    values = checkout.fields | {"date": date, "cash_received": "20.00", "checkout_intent": "record_cash_sale"}
    for key, value in list(values.items()):
        if key.endswith("[sku]"):
            values[key[:-5] + "[quantity]"] = "2" if value == "NOTE-A5" else "3" if value == "PEN-BLUE" else "0"
    denied = session.request(checkout.action, values | {"csrf": "invalid-local-token"})
    check(denied.status == 403, "Checkout rejects invalid CSRF")
    missing_intent = session.request(checkout.action, {key: value for key, value in values.items() if key != "checkout_intent"})
    check(missing_intent.status == 422 and "Choose Record cash sale" in missing_intent.body, "Checkout requires an explicit cash-sale confirmation")
    check(http.totals(session.request("/reports/trial-balance?as_of=" + date)) == before, "Missing confirmation has no accounting effect")
    invalid_intent = session.request(checkout.action, values | {"checkout_intent": "edit_quantity"})
    check(invalid_intent.status == 422 and "Choose Record cash sale" in invalid_intent.body, "Checkout rejects an unrelated action as confirmation")
    check(http.totals(session.request("/reports/trial-balance?as_of=" + date)) == before, "Invalid confirmation has no accounting effect")
    scoped = session.request(checkout.action, values | {"book_id": str(int(values["book_id"]) + 1000000)})
    check(scoped.status == 422, "Checkout rejects altered book scope")
    forged = session.request(checkout.action, values | {"total": "0.01"})
    check(forged.status == 422 and "not custom prices or totals" in forged.body, "Checkout rejects a forged total")
    insufficient = session.request(checkout.action, values | {"cash_received": "12.74"})
    retained = insufficient.markup.form_for("/pos/checkout")
    check(insufficient.status == 422 and retained.fields["cash_received"] == "12.74" and retained.fields["checkout_key"] == values["checkout_key"] and all(retained.fields.get(key) == value for key, value in values.items() if key.startswith("items[")), "Insufficient cash retains the full cart and checkout identity")
    check(http.totals(session.request("/reports/trial-balance?as_of=" + date)) == before, "Rejected checkouts have no report effect")
    sale = session.request(checkout.action, values)
    check(sale.status == 200 and urlparse(sale.url).path == "/pos/receipt", "Cash checkout returns a durable receipt")
    document_id = int(parse_qs(urlparse(sale.url).query)["id"][0])
    rows = [row for table in sale.markup.tables for row in table]
    summaries = {row[0]: row[-1] for row in rows if row and row[0] in {"Sale total", "Cash received", "Change"}}
    check(summaries == {"Sale total": "12.75", "Cash received": "20.00", "Change": "7.25"}, "Receipt shows exact 12.75 total, 20.00 cash and 7.25 change")
    check("Recorded sale in USD" in sale.body and "NOTE-A5" in sale.body and "PEN-BLUE" in sale.body, "Receipt preserves base currency and product snapshots")
    source = session.request(sale.markup.link_for("/transactions/detail"))
    journal = session.request(sale.markup.link_for("/journals/detail"))
    check(source.status == 200 and parse_qs(urlparse(source.url).query)["id"] == [str(document_id)] and journal.status == 200 and parse_qs(urlparse(journal.markup.link_for("/transactions/detail")).query)["id"] == [str(document_id)], "Receipt and balanced journal point to the same source document")
    again = session.request(checkout.action, values)
    check(again.status == 200 and again.url == sale.url, "Repeated checkout returns the original receipt")
    after = http.totals(session.request("/reports/trial-balance?as_of=" + date))
    check(after == tuple(value + Decimal("12.75") for value in before), "Duplicate checkout has one balanced 12.75 report effect")
    conflict = session.request(checkout.action, values | {"cash_received": "30.00"})
    check(conflict.status == 422 and "different sale" in conflict.body and http.totals(session.request("/reports/trial-balance?as_of=" + date)) == after, "Changed content with an existing key is rejected without a new sale")
    check(session.request("/pos/checkout").status == 405, "Checkout rejects GET requests")
    session.submit(session.request("/companies").markup.form_for("/logout"))
    check(urlparse(session.request(f"/pos/receipt?id={document_id}").url).path == "/login", "Logged-out sessions cannot read the receipt")
    return {"passed": len(checks), "failed": 0, "target": http.ORIGIN, "company_id": company_id, "book_id": int(values["book_id"]), "document_id": document_id, "data": "One synthetic posted POS sale retained; no other company's books changed."}


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--company-id", type=int, required=True)
    options = parser.parse_args()
    try:
        print(json.dumps(run(options.company_id), indent=2))
    except Exception as error:
        print(f"FAIL: {error}", file=sys.stderr)
        sys.exit(1)
