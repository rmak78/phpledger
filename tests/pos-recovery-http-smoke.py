"""Local synthetic recovery checks. Requires a test-database Compose web service.
Uses the existing localhost-only HTTP smoke guard and caller-supplied synthetic login.
Injects a lost-response recovery record into only this test session; no production hooks.
"""
import argparse
import importlib.util
import json
import os
from pathlib import Path
import subprocess
import sys
from datetime import datetime, timezone
from urllib.parse import urlparse, parse_qs

spec = importlib.util.spec_from_file_location("recovery_http", Path(__file__).with_name("http-smoke.py"))
http = importlib.util.module_from_spec(spec)
sys.modules[spec.name] = http
spec.loader.exec_module(http)


def run(company_id, compose):
    session = http.Session()
    checks = []
    def check(ok, name):
        if not ok:
            raise AssertionError(name)
        checks.append(name)
        print("PASS:", name, flush=True)
    login = session.request("/login")
    companies = session.submit(login.markup.form_for("/login"), {"email": os.environ["PL_HTTP_EMAIL"], "password": os.environ["PL_HTTP_PASSWORD"]})
    select = next(f for f in companies.markup.forms if urlparse(f.action).path == "/company/select" and f.fields.get("company_id") == str(company_id))
    company = session.submit(select)
    check("HTTP Acceptance " in company.body, "Selected synthetic HTTP company")
    shop = session.request("/pos")
    cart = shop.markup.form_for("/pos/review")
    cart_values = cart.fields | {"review_intent": "review_cart", "date": datetime.now(timezone.utc).date().isoformat()}
    for key, value in list(cart_values.items()):
        if key.endswith("[sku]"):
            cart_values[key[:-5] + "[quantity]"] = "1" if value == "NOTE-A5" else "0"
    review = session.request(cart.action, cart_values)
    checkout = review.markup.form_for("/pos/checkout")
    posted = checkout.fields | {"cash_received": "10.00", "checkout_intent": "record_cash_sale"}
    receipt = session.request(checkout.action, posted)
    original_id = parse_qs(urlparse(receipt.url).query)["id"][0]
    before = http.totals(session.request("/reports/trial-balance?as_of=" + cart_values["date"]))
    request = {"checkout_key": posted["checkout_key"], "catalog_digest": posted["catalog_digest"], "date": posted["date"], "cash_received": posted["cash_received"], "items": [{"sku": "NOTE-A5", "quantity": "1"}]}
    cookie = next(c.value for c in session.cookies if c.name == "phpledger_session")
    # Cookie and fixture data travel on stdin, never command-line output or committed evidence.
    php = """if (getenv('PL_DB_NAME') !== 'phpledger_test' || !in_array(getenv('PL_ENV'), ['local','test'], true)) { exit(2); }
require 'www/phpledger/includes/bootstrap.php';
$job=json_decode(file_get_contents('php://stdin'), true, 512, JSON_THROW_ON_ERROR);
session_id($job['session']); session_start();
$company=pl_company_context((int) $_SESSION['user_id'], (int) $job['company_id']);
if (!str_starts_with($company['name'], 'HTTP Acceptance ') || (int)($_SESSION['company_id'] ?? 0) !== (int)$company['id']) { exit(3); }
$quoteInput=$job['request']; unset($quoteInput['cash_received']);
$state=pl_pos_recovery((int)$company['id'], (int)$company['book_id'], $job['request'], pl_pos_quote($quoteInput));
$_SESSION['pos_recoveries'][$company['id'].':'.$company['book_id']]=$state;
session_write_close();"""
    def inject(original):
        result = subprocess.run(["docker", "compose", "-f", str(compose), "exec", "-T", "web", "php", "-r", php], input=json.dumps({"session": cookie, "company_id": company_id, "request": original}), text=True, capture_output=True)
        if result.returncode:
            raise RuntimeError("Synthetic recovery injection failed: " + result.stderr)
    inject(request)
    pending = session.request("/pos/review")
    check(pending.status == 503 and "data-pos-recovery" in pending.body and "10.00" in pending.body, "Uncertain committed request retains original cash and warning")
    check(session.request("/pos/review").status == 503 and "could not confirm" in session.request("/pos/review").body, "Uncertain warning persists after refresh")
    check("data-pos-edit" not in pending.body and "data-pos-cash" not in pending.body, "Uncertain financial inputs cannot be edited")
    blocked = session.request("/pos/edit", posted | {"review_intent": "edit_cart", "cash_received": "999"})
    check(blocked.status == 503 and "data-pos-recovery" in blocked.body, "An older tab cannot edit an uncertain sale")
    check(session.request("/pos").status == 503, "New sale waits for unresolved attempt")
    retry = pending.markup.form_for("/pos/retry")
    check(session.request(retry.action, retry.fields | {"retry_intent": "retry_original", "csrf": "bad"}).status == 403, "Recovery retry enforces CSRF")
    check(session.request(retry.action, retry.fields | {"retry_intent": "retry_original", "cash_received": "999"}).status == 403, "Recovery retry rejects replacement financial fields")
    check(session.request(retry.action, retry.fields | {"retry_intent": "retry_original", "book_id": str(int(retry.fields["book_id"]) + 9999)}).status == 403, "Recovery retry enforces company/book scope")
    recovered = session.submit(retry, {"retry_intent": "retry_original"})
    check(parse_qs(urlparse(recovered.url).query)["id"][0] == original_id, "Committed recovery returns original receipt")
    check(http.totals(session.request("/reports/trial-balance?as_of=" + cart_values["date"])) == before, "Committed recovery creates no second accounting effect")
    check(session.request("/pos").status == 200, "Successful recovery allows next sale")
    rejected = request | {"checkout_key": request["checkout_key"] + "-rejected", "cash_received": "1.00"}
    inject(rejected)
    pending = session.request("/pos/review")
    corrected = session.submit(pending.markup.form_for("/pos/retry"), {"retry_intent": "retry_original"})
    check(corrected.status == 422 and "Cash received must cover" in corrected.body and "data-pos-form" in corrected.body, "Definite rejection restores editable original cart")
    check(http.totals(session.request("/reports/trial-balance?as_of=" + cart_values["date"])) == before, "Rejected recovery has no accounting effect")
    return {"checks": len(checks), "failures": 0}

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--company-id", type=int, required=True)
    parser.add_argument("--compose", type=Path, required=True)
    args = parser.parse_args()
    print(json.dumps(run(args.company_id, args.compose)))
