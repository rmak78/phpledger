"""Exercise module administration and gated POS through local HTTP with sample records.

Only http://127.0.0.1:18200 and its existing local Compose database are used.
Creates independent owner/viewer identities in memory, retains sample books,
and records a sample cash sale; no payment, message or provider call is made.
"""
from __future__ import annotations

import importlib.util
import json
from pathlib import Path
import sys
from urllib.parse import urlparse

spec = importlib.util.spec_from_file_location("module_accounting_http", Path(__file__).with_name("accounting-http-smoke.py"))
accounting = importlib.util.module_from_spec(spec)
sys.modules[spec.name] = accounting
spec.loader.exec_module(accounting)
http = accounting.http


def run() -> dict:
    fixture, private = accounting.fixture()
    actor, company, book = (fixture[k] for k in ("owner_id", "company_id", "book_id"))
    accounting.php_local(
        f"$p=pl_preview_opening({actor},{company},{book}, ['cutover_date'=>'2026-01-01','source'=>'Sample module acceptance',"
        "'balances'=>[],'unpaid_documents'=>[],'zero_confirmed'=>true], 'module-opening');"
        f"pl_confirm_opening({actor},{company},{book}, (int)$p['id'], $p['payload_hash'],true);echo json_encode(['confirmed'=>true]);"
    )
    checks = []

    def check(label, condition):
        if not condition:
            raise AssertionError(label)
        checks.append(label)
        print("PASS:", label, flush=True)

    def login(role):
        client = http.Session()
        page = client.request("/login")
        page = client.submit(page.markup.form_for("/login"), {"email": private[role + "_email"], "password": private[role + "_password"]})
        chosen = next(f for f in page.markup.forms if urlparse(f.action).path == "/company/select" and f.fields.get("company_id") == str(company))
        client.submit(chosen)
        return client

    anonymous = http.Session()
    check("Module administration requires authentication", urlparse(anonymous.request("/modules").url).path == "/login")
    owner, viewer = login("owner"), login("viewer")
    page = owner.request("/modules")
    check("New company shows disabled POS and readable core", page.status == 200 and "Disabled" in page.body and "Your accounting core is always available" in page.body)
    check("Disabled POS is absent from navigation", not any(urlparse(href).path == "/pos" for href, _ in page.markup.links))
    for route in ["/pos", "/pos/review"]:
        check("Disabled capability rejects " + route, owner.request(route).status == 403)
    form = page.markup.form_for("/modules")
    enable = form.fields | {"enabled": "1", "reason": "Sample owner enables POS"}
    check("Module POST requires CSRF", owner.request("/modules", enable | {"csrf": "invalid"}).status == 403)
    check("Module POST rejects foreign company/book", owner.request("/modules", enable | {"company_id": str(fixture["other_company_id"]), "book_id": str(fixture["other_book_id"])}).status == 422)
    viewer_page = viewer.request("/modules")
    check("Viewer reads module status without mutation form", viewer_page.status == 200 and not any(urlparse(f.action).path == "/modules" for f in viewer_page.markup.forms))
    viewer_csrf = viewer_page.markup.form_for("/logout").fields["csrf"]
    check("Viewer forged enable is rejected", viewer.request("/modules", enable | {"csrf": viewer_csrf}).status == 422)
    check("Unknown module is rejected", owner.request("/modules", enable | {"module_id": "unknown"}).status == 422)
    check("Changed manifest preview is rejected", owner.request("/modules", enable | {"digest": "0" * 64}).status == 422)
    check("Reason validation retains escaped input", "&lt;script&gt;" in owner.request("/modules", enable | {"reason": "<script>", "digest": "0" * 64}).body)
    enabled = owner.request("/modules", enable)
    check("Owner enables a compatible reviewed module", enabled.status == 200 and "Enabled" in enabled.body)
    check("Repeated enable keeps one revision", owner.request("/modules", enable).markup.form_for("/modules").fields["revision"] == "1")
    check("Stale revision fails", owner.request("/modules", enable | {"enabled": "0", "request_key": "stale-module"}).status == 422)
    shop = owner.request("/pos")
    check("Enabled POS is available", shop.status == 200 and shop.body.count("data-pos-product data-sku=") == 6)
    cart = shop.markup.form_for("/pos/review")
    fields = cart.fields | {"date": "2026-09-15", "review_intent": "review_cart"}
    for key, value in list(fields.items()):
        if key.endswith("[sku]"):
            fields[key[:-5] + "[quantity]"] = "2" if value == "NOTE-A5" else "3" if value == "PEN-BLUE" else "0"
    reviewed = owner.request("/pos/review", fields)
    check("Enabled cart has an explicit server review", reviewed.status == 200 and "12.75" in reviewed.body)
    checkout = reviewed.markup.form_for("/pos/checkout")
    sale = owner.submit(checkout, {"cash_received": "20", "checkout_intent": "record_cash_sale"})
    check("Reviewed sample checkout posts one receipt", sale.status == 200 and "Receipt ready" in sale.body)
    receipt_url = sale.url
    before = http.totals(owner.request("/reports/trial-balance"))
    disable = owner.request("/modules").markup.form_for("/modules").fields | {"enabled": "0", "reason": "Sample owner disables POS"}
    disabled = owner.request("/modules", disable)
    check("Owner disables module and retains history", disabled.status == 200 and "Disabled" in disabled.body and "Sample owner enables POS" in disabled.body)
    check("Direct checkout and retry reject disabled module", owner.submit(checkout, {"cash_received": "20", "checkout_intent": "record_cash_sale"}).status == 403 and owner.request("/pos/retry", checkout.fields).status == 403)
    history = owner.request(receipt_url)
    check("Receipt remains readable without a new-sale action", history.status == 200 and "12.75" in history.body and "Start another sale" not in history.body)
    check("Viewer retains access to the historic receipt", viewer.request(receipt_url).status == 200)
    source_url = history.markup.link_for("/transactions/detail")
    check("Historic receipt still links to its source", owner.request(source_url).status == 200)
    check("Disabled module leaves core report and export available", owner.request("/reports/export?report=trial-balance&to=2026-09-15").headers.get_content_type() == "text/csv")
    check("Disabled attempts preserve ledger totals", http.totals(owner.request("/reports/trial-balance")) == before)
    reenable = owner.request("/modules").markup.form_for("/modules").fields | {"enabled": "1", "reason": "Sample owner reenables POS"}
    check("Compatible module reenables", owner.request("/modules", reenable).status == 200 and owner.request("/pos").status == 200)
    return {"passed": len(checks), "failed": 0, "target": http.ORIGIN, "company_id": company, "book_id": book, "receipt_url": receipt_url, "external_calls": False, "production_changed": False}


if __name__ == "__main__":
    print(json.dumps(run(), indent=2))
