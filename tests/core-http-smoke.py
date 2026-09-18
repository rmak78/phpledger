"""Exercise core browser workflows against a local sample PHP Ledger installation.

Uses the forms, parser and in-memory sessions from http-smoke.py. Configure
--base-url, --email/PL_HTTP_EMAIL, --password/PL_HTTP_PASSWORD and optionally
--company-id. Without a company ID, creates an isolated sample company.
Creates two accounts, one general journal plus its linked reversal, and one
additional empty company for cross-company rejection checks; keeps all fixtures.
A failed run can leave a partial fixture; emitted IDs identify it for inspection.
The provided company must be sample, writable and have open test dates.

Optional PL_HTTP_VIEWER_EMAIL/PL_HTTP_VIEWER_PASSWORD (or CLI equivalents) must
identify a viewer of the selected company. Missing viewer credentials produce an
explicit skip. No direct database writes, provider calls, public targets, cleanup
or period changes are performed. Passwords are never included in results.
"""

from __future__ import annotations

import argparse
from datetime import date, datetime, timedelta, timezone
from decimal import Decimal
import importlib.util
import json
import os
from pathlib import Path
import re
import sys
from urllib.parse import parse_qs, urlencode, urljoin, urlparse
import uuid


spec = importlib.util.spec_from_file_location("core_local_http", Path(__file__).with_name("http-smoke.py"))
http = importlib.util.module_from_spec(spec)
sys.modules[spec.name] = http
spec.loader.exec_module(http)


def configure_target(base_url: str) -> str:
    """Keep both initial requests and redirects inside one explicit local origin."""
    base = urlparse(base_url)
    if (base.scheme not in {"http", "https"} or base.hostname not in {"127.0.0.1", "localhost", "::1"}
            or base.username or base.password or base.query or base.fragment):
        raise ValueError("Choose a loopback HTTP(S) base URL without credentials, query or fragment.")
    prefix = base.path.rstrip("/")
    if "%" in prefix or ".." in prefix.split("/"):
        raise ValueError("Use a plain local application base path.")
    origin = f"{base.scheme}://{base.netloc}"
    root = origin + prefix

    def local_url(value: str) -> str:
        parsed = urlparse(value)
        if parsed.scheme or parsed.netloc:
            result = value
        elif value.startswith("/") and prefix and not (value == prefix or value.startswith(prefix + "/")):
            result = origin + prefix + value
        else:
            result = urljoin(root + "/", value)
        target = urlparse(result)
        if ((target.scheme, target.hostname, target.port) != (base.scheme, base.hostname, base.port)
                or target.username or target.password
                or not (target.path == prefix or target.path.startswith(prefix + "/"))):
            raise RuntimeError("Core HTTP smoke refuses a non-local or out-of-scope request/redirect.")
        return result

    http.local_url = local_url
    http.ORIGIN = root
    return prefix


def run(options: argparse.Namespace) -> dict:
    prefix = configure_target(options.base_url)
    if not options.email or not options.password:
        raise ValueError("Provide PL_HTTP_EMAIL/PL_HTTP_PASSWORD or the matching CLI options.")
    if bool(options.viewer_email) != bool(options.viewer_password):
        raise ValueError("Provide both viewer email and password, or neither.")
    post_date = date.fromisoformat(options.date)
    reverse_date = date.fromisoformat(options.reversal_date) if options.reversal_date else post_date + timedelta(days=1)
    if reverse_date <= post_date:
        raise ValueError("Use a reversal date after the posting date to verify dated report effects.")
    checks: list[str] = []
    skipped: list[str] = []
    tag = uuid.uuid4().hex[:10]
    amount = Decimal("37.1250")

    def path(route: str) -> str:
        return prefix + route

    def check(condition: bool, label: str) -> None:
        if not condition:
            raise AssertionError(label)
        checks.append(label)
        print("PASS:", label, flush=True)

    def ok(page, label: str):
        if page.status != 200:
            raise AssertionError(f"{label}: HTTP {page.status} at {urlparse(page.url).path}.")
        return page

    def form(page, route: str):
        return page.markup.form_for(path(route))

    def has_form(page, route: str) -> bool:
        return any(urlparse(f.action).path == path(route) for f in page.markup.forms)

    def link(page, route: str, label: str | None = None) -> str:
        return page.markup.link_for(path(route), label)

    def ident(page) -> int:
        return int(parse_qs(urlparse(page.url).query)["id"][0])

    def login(email: str, password: str):
        client = http.Session()
        page = ok(client.request("/login"), "Login page")
        signed = ok(client.submit(form(page, "/login"), {"email": email, "password": password}), "Sign-in")
        check(urlparse(signed.url).path == path("/companies"), "Sample login reaches business selection")
        return client

    def select_company(client, company_id: int):
        businesses = ok(client.request("/companies"), "Business selection")
        selected = next((f for f in businesses.markup.forms
                         if urlparse(f.action).path == path("/company/select")
                         and f.fields.get("company_id") == str(company_id)), None)
        if selected is None:
            raise AssertionError("Requested sample company is unavailable to this login.")
        return ok(client.submit(selected), "Select sample company")

    def create_company(client, suffix: str) -> int:
        name = "Core HTTP Acceptance " + tag + " " + suffix
        setup = ok(client.request("/onboarding"), "Onboarding")
        preview = ok(client.submit(form(setup, "/onboarding"), {
            "action": "preview", "name": name, "currency": "USD",
            "start_date": f"{post_date.year}-01-01", "fiscal_year_end": "12-31",
            "start_mode": "fresh", "zero_balances_confirmed": "1",
        }), "Setup preview")
        created = ok(client.submit(form(preview, "/onboarding")), "Setup confirmation")
        check(name in created.body, "Onboarding creates a separate sample business")
        account_page = ok(client.request("/accounts?new=1"), "New account scope")
        return int(form(account_page, "/accounts/save").fields["company_id"])

    def report(client, as_of: date):
        return ok(client.request("/reports/trial-balance?" + urlencode({"as_of": as_of.isoformat()})), "Trial balance")

    def statement(client, account_id: int, start: date, end: date):
        return ok(client.request("/reports/account?" + urlencode({
            "id": account_id, "from": start.isoformat(), "as_of": end.isoformat(),
        })), "Account statement")

    def statement_summary(page) -> dict[str, str]:
        match = re.search(r'<dl class="statement-summary".*?</dl>', page.body, re.S)
        if not match:
            raise AssertionError("Statement summary is missing.")
        result = {}
        for label, value in re.findall(r"<dt>(.*?)</dt>\s*<dd[^>]*>(.*?)</dd>", match.group(0), re.S):
            result[re.sub("<[^>]+>", "", label)] = re.sub("<[^>]+>", "", value).strip()
        return result

    def journal_totals(page) -> tuple[Decimal, Decimal]:
        for table in page.markup.tables:
            for row in table:
                if row and row[0].startswith("Total"):
                    return tuple(Decimal(value.replace(",", "")) for value in row[-2:])
            if table and table[0] == ["Account", "Description", "Debit", "Credit"] and len(table) > 1:
                return tuple(sum((Decimal(row[index].replace(",", "")) for row in table[1:]), Decimal("0"))
                             for index in (-2, -1))
        raise AssertionError("Journal totals are missing.")

    client = login(options.email, options.password)
    company_id = options.company_id or create_company(client, "main")
    if options.company_id:
        select_company(client, company_id)
    account_new = ok(client.request("/accounts?new=1"), "Chart management")
    create_form = form(account_new, "/accounts/save")
    book_id = int(create_form.fields["book_id"])
    check(create_form.fields["company_id"] == str(company_id), "Account form uses the selected company/book")
    before = http.totals(report(client, post_date))
    before_reversal_date = http.totals(report(client, reverse_date))
    created_reason = "Sample account creation " + tag
    account_values = {
        "code": "HE" + tag, "name": "HTTP expense " + tag, "type": "expense", "role": "expense",
        "is_active": "1", "reason": created_reason,
    }
    check(client.submit(create_form, account_values | {"csrf": "invalid-local-csrf"}).status == 403,
          "Account creation rejects invalid CSRF")
    account_page = ok(client.submit(create_form, account_values), "Create expense account")
    account_id = ident(account_page)
    account_edit = form(account_page, "/accounts/save")
    check(account_edit.fields["revision"] == "1" and created_reason in account_page.body,
          "New account begins at revision one with its creation reason in history")
    duplicate = ok(client.submit(create_form, account_values), "Retry account creation")
    check(ident(duplicate) == account_id, "Identical account creation retry returns one durable account")

    changed_name = "HTTP revised expense " + tag
    changed_reason = "Rename and deactivate sample account " + tag
    deactivation = account_edit.fields | {"name": changed_name, "reason": changed_reason}
    deactivation.pop("is_active", None)
    updated = ok(client.request(account_edit.action, deactivation), "Rename and deactivate")
    updated_form = form(updated, "/accounts/save")
    check(updated_form.fields["revision"] == "2" and updated_form.fields["name"] == changed_name
          and "is_active" not in updated_form.fields and changed_reason in updated.body and created_reason in updated.body,
          "Name/status update advances revision and retains both audit reasons")

    stale_values = deactivation | {"name": "Stale account name " + tag, "reason": "Stale account edit " + tag}
    stale_account = client.request(account_edit.action, stale_values)
    stale_account_form = form(stale_account, "/accounts/save")
    check(stale_account.status == 422 and stale_account_form.fields["revision"] == "1"
          and stale_account_form.fields["name"] == stale_values["name"]
          and "is_active" not in stale_account_form.fields and "Reload the latest saved version" in stale_account.body,
          "Stale account edit retains submitted name/status and old revision")
    latest_account = ok(client.request(f"/accounts?id={account_id}"), "Reload account")
    check(form(latest_account, "/accounts/save").fields["revision"] == "2"
          and form(latest_account, "/accounts/save").fields["name"] == changed_name,
          "Rejected stale account edit leaves the saved version unchanged")
    reactivated = ok(client.submit(form(latest_account, "/accounts/save"), {
        "is_active": "1", "reason": "Reactivate for sample journals " + tag,
    }), "Reactivate account")
    account_edit = form(reactivated, "/accounts/save")
    check(account_edit.fields["revision"] == "3" and account_edit.fields.get("is_active") == "1",
          "Reactivation is a separate audited revision")
    for override, label in [
        ({"code": "XX" + tag}, "code"),
        ({"type": "income", "role": "income"}, "classification"),
        ({"role": ""}, "purpose"),
    ]:
        denied = client.submit(account_edit, override | {"reason": "Reject changed " + label})
        check(denied.status == 422 and "fixed" in denied.body,
              f"Existing account {label} cannot be changed through submitted fields")
    check(form(ok(client.request(f"/accounts?id={account_id}"), "Reload fixed account"),
               "/accounts/save").fields["revision"] == "3",
          "Rejected reclassifications leave account revision unchanged")

    income_new = form(ok(client.request("/accounts?new=1"), "New income account"), "/accounts/save")
    income = ok(client.submit(income_new, {
        "code": "HI" + tag, "name": "HTTP income " + tag, "type": "income", "role": "income",
        "is_active": "1", "reason": "Sample balancing account " + tag,
    }), "Create income account")
    income_id = ident(income)
    new_draft = form(ok(client.request("/general-journals/new"), "New journal"), "/general-journals/save")
    values = {
        "date": post_date.isoformat(), "reference": "HTTP-" + tag,
        "description": "Sample <script>retained()</script> " + tag,
        "lines[0][account_id]": str(account_id), "lines[0][debit]": "37.1250",
        "lines[0][credit]": "", "lines[0][description]": "Expense line " + tag,
        "lines[1][account_id]": str(income_id), "lines[1][debit]": "",
        "lines[1][credit]": "20.00", "lines[1][description]": "Income line " + tag,
    }
    check(client.submit(new_draft, values | {"csrf": "invalid-local-csrf"}).status == 403,
          "General draft save rejects invalid CSRF")
    bad_amount = client.submit(new_draft, values | {"lines[0][debit]": "not-a-number"})
    retained = form(bad_amount, "/general-journals/save")
    check(bad_amount.status == 422 and retained.fields["lines[0][debit]"] == "not-a-number"
          and retained.fields["description"] == values["description"]
          and retained.fields["creation_key"] == new_draft.fields["creation_key"],
          "Invalid draft retains exact input and creation identity")
    check("<script>retained()</script>" not in bad_amount.body,
          "Invalid draft escapes retained untrusted text")

    saved = ok(client.submit(retained, values), "Save unbalanced draft")
    draft_id = ident(saved)
    print("FIXTURE:", json.dumps({"company_id": company_id, "book_id": book_id,
          "account_ids": [account_id, income_id], "general_draft_id": draft_id, "tag": tag}), flush=True)
    check("Needs balancing" in saved.body and not has_form(saved, "/general-journals/post"),
          "Unbalanced draft is saved for review without a post button")
    repeated_save = ok(client.submit(new_draft, values), "Retry draft creation")
    check(ident(repeated_save) == draft_id, "Identical draft creation retry returns one saved draft")
    check(http.totals(report(client, post_date)) == before, "Draft saves have no trial-balance effect")

    old_editor = form(ok(client.request(f"/general-journals/edit?id={draft_id}"), "Edit draft"),
                      "/general-journals/save")
    post_fields = {key: old_editor.fields[key] for key in ("csrf", "company_id", "book_id", "id", "revision")}
    post_fields["intent"] = "post_reviewed_journal"
    unbalanced_post = client.request("/general-journals/post", post_fields)
    check(unbalanced_post.status == 422 and "balance" in unbalanced_post.body.lower(),
          "Direct HTTP posting cannot bypass unbalanced-draft validation")
    check(http.totals(report(client, post_date)) == before, "Rejected unbalanced post has no financial effect")

    corrected_values = {"description": "Saved balanced journal " + tag, "lines[1][credit]": "37.1250"}
    balanced = ok(client.submit(old_editor, corrected_values), "Save balanced correction")
    post_form = form(balanced, "/general-journals/post")
    check(post_form.fields["revision"] == "2" and journal_totals(balanced) == (amount, amount),
          "Corrected draft advances revision and shows exact balanced totals")
    stale_changes = {
        "date": reverse_date.isoformat(), "reference": "STALE-" + tag,
        "description": "Keep my stale <script>draft()</script> " + tag,
        "lines[0][debit]": "41.0001", "lines[1][credit]": "41.0001",
        "lines[0][description]": "Retain stale debit " + tag,
    }
    stale_draft = client.submit(old_editor, stale_changes)
    retained_stale = form(stale_draft, "/general-journals/save")
    check(stale_draft.status == 422 and retained_stale.fields["revision"] == "1"
          and all(retained_stale.fields[key] == value for key, value in stale_changes.items())
          and "Reload the latest saved version" in stale_draft.body,
          "Stale draft recovery retains submitted date, text, amounts and old revision")
    check("<script>draft()</script>" not in stale_draft.body, "Stale draft recovery escapes submitted text")
    stale_post = client.request("/general-journals/post", post_fields)
    check(stale_post.status == 422 and form(stale_post, "/general-journals/post").fields["revision"] == "1",
          "Stale post keeps the old review revision instead of silently authorizing the new draft")
    reloaded = ok(client.request(f"/general-journals/detail?id={draft_id}"), "Reload saved draft")
    post_form = form(reloaded, "/general-journals/post")
    check(post_form.fields["revision"] == "2" and corrected_values["description"] in reloaded.body
          and journal_totals(reloaded) == (amount, amount), "Reload shows unchanged saved revision and balances")
    check(http.totals(report(client, post_date)) == before, "Stale edits and posts leave reports unchanged")

    check(client.submit(post_form, {"csrf": "invalid-local-csrf"}).status == 403,
          "General journal posting rejects invalid CSRF")
    missing_intent = dict(post_form.fields)
    missing_intent.pop("intent")
    check(client.request(post_form.action, missing_intent).status == 422,
          "Posting requires explicit saved-journal review intent")
    wrong_book = client.submit(post_form, {"book_id": str(book_id + 1000000)})
    check(wrong_book.status == 422, "Posting rejects an altered company/book scope")

    posted = ok(client.submit(post_form, {
        "date": "2000-01-01", "description": "UNSAVED BROWSER VALUES",
        "lines[0][debit]": "999999.00", "lines[1][credit]": "999999.00",
    }), "Post saved journal")
    journal_url = link(posted, "/journals/detail", "Open posted journal")
    journal_id = int(parse_qs(urlparse(journal_url).query)["id"][0])
    posted_journal = ok(client.request(journal_url), "Posted journal")
    check(journal_totals(posted_journal) == (amount, amount)
          and "UNSAVED BROWSER VALUES" not in posted.body
          and corrected_values["description"] in posted.body,
          "Posting uses only saved lines and description despite unsaved browser fields")
    check(parse_qs(urlparse(link(posted_journal, "/general-journals/detail")).query)["id"] == [str(draft_id)],
          "Posted journal links back to its saved general-journal source")
    after = tuple(value + amount for value in before)
    check(http.totals(report(client, post_date)) == after,
          "Saved posting date and exact amount produce one balanced report effect")
    again = ok(client.submit(post_form), "Retry posted journal")
    check(link(again, "/journals/detail", "Open posted journal") == journal_url
          and http.totals(report(client, post_date)) == after,
          "Identical posting retry returns the same journal without another report effect")
    account_statement = statement(client, account_id, post_date, post_date)
    check(statement_summary(account_statement) == {
        "Opening balance": "0.00", "Total debits": "37.1250",
        "Total credits": "0.00", "Closing balance": "37.1250 Dr",
    }, "Account statement reconciles the original exact debit and closing balance")
    check(parse_qs(urlparse(link(account_statement, "/general-journals/detail")).query)["id"] == [str(draft_id)]
          and parse_qs(urlparse(link(account_statement, "/journals/detail")).query)["id"] == [str(journal_id)],
          "Account statement links to both saved source and posted journal")

    reverse_form = form(posted, "/general-journals/reverse")
    check(client.submit(reverse_form, {"date": reverse_date.isoformat(), "reason": "Sample correction",
                                      "csrf": "invalid-local-csrf"}).status == 403,
          "General reversal rejects invalid CSRF")
    bad_reverse = client.submit(reverse_form, {"date": "2000-01-01", "reason": "Keep reversal reason " + tag})
    retained_reverse = form(bad_reverse, "/general-journals/reverse")
    check(bad_reverse.status == 422 and retained_reverse.fields["date"] == "2000-01-01"
          and retained_reverse.fields["reason"] == "Keep reversal reason " + tag,
          "Rejected reversal preserves submitted date and reason")
    reverse_values = {"date": reverse_date.isoformat(), "reason": "Dated sample correction " + tag}
    reversed_page = ok(client.submit(retained_reverse, reverse_values), "Post dated reversal")
    reversal_url = link(reversed_page, "/journals/detail", "Open linked reversal")
    reversal_id = int(parse_qs(urlparse(reversal_url).query)["id"][0])
    reversal = ok(client.request(reversal_url), "Reversal journal")
    check(reversal_id != journal_id
          and parse_qs(urlparse(link(reversal, "/journals/detail", "original")).query)["id"] == [str(journal_id)],
          "Dated reversal creates a separate journal linked to the preserved original")
    check(http.totals(report(client, post_date)) == after
          and http.totals(report(client, reverse_date)) == before_reversal_date,
          "Original-date report retains posting while reversal-date report returns to its baseline")
    reversal_statement = statement(client, account_id, reverse_date, reverse_date)
    check(statement_summary(reversal_statement) == {
        "Opening balance": "37.1250 Dr", "Total debits": "0.00",
        "Total credits": "37.1250", "Closing balance": "0.00",
    }, "Reversal-date statement carries opening forward and reconciles opposite movement to zero")
    repeat_reversal = ok(client.submit(reverse_form, reverse_values), "Retry reversal")
    check(link(repeat_reversal, "/journals/detail", "Open linked reversal") == reversal_url,
          "Identical reversal retry returns the existing linked reversal")
    changed_reversal = client.submit(reverse_form, reverse_values | {"reason": "Different retry reason " + tag})
    check(changed_reversal.status == 422 and http.totals(report(client, reverse_date)) == before_reversal_date,
          "Changed reversal retry is rejected without another accounting effect")

    chooser = ok(client.request('/reports/account'), 'Account ledger chooser')
    check('id="statement-account"' in chooser.body and f'value="{account_id}"' in chooser.body,
          'Ledger chooser lists an authorized account without requiring a trial-balance detour')
    check(client.request('/reports/account?as_of=invalid').status == 403,
          'Ledger chooser rejects malformed dates before opening an account')

    if options.viewer_email:
        viewer = login(options.viewer_email, options.viewer_password)
        select_company(viewer, company_id)
        check(viewer.request('/reports/account').status == 200,
              'Viewer can open the account ledger chooser')
        viewer_accounts = ok(viewer.request(f"/accounts?id={account_id}"), "Viewer account")
        check(not has_form(viewer_accounts, "/accounts/save"), "Viewer account page has no mutation form")
        viewer_detail = ok(viewer.request(f"/general-journals/detail?id={draft_id}"), "Viewer general journal")
        check(not has_form(viewer_detail, "/general-journals/post")
              and not has_form(viewer_detail, "/general-journals/reverse"),
              "Viewer can read the journal without posting/reversal forms")
        viewer_csrf = form(ok(viewer.request("/companies"), "Viewer session"), "/logout").fields["csrf"]
        check(viewer.request("/general-journals/new").status == 403, "Viewer cannot open journal editor")
        denied_account = viewer.request("/accounts/save", account_edit.fields | {"csrf": viewer_csrf})
        check(denied_account.status == 422, "Viewer cannot submit a crafted account edit")
        denied_save = viewer.request("/general-journals/save", new_draft.fields | values | {"csrf": viewer_csrf})
        check(denied_save.status == 403, "Viewer cannot submit a crafted general draft")
        check(viewer.request("/general-journals/post", post_form.fields | {"csrf": viewer_csrf}).status == 422,
              "Viewer cannot post by reusing an owner's journal ID")
        check(viewer.request("/general-journals/reverse", reverse_form.fields | reverse_values
                             | {"csrf": viewer_csrf}).status == 422,
              "Viewer cannot reverse by reusing an owner's journal ID")
        check(http.totals(report(client, reverse_date)) == before_reversal_date,
              "Viewer mutation attempts leave report totals unchanged")
    else:
        skipped.append("Viewer HTTP checks: provide viewer credentials with membership in the selected company.")
        print("SKIP:", skipped[-1], flush=True)

    other_company_id = create_company(client, "scope-boundary")
    other_form = form(ok(client.request("/accounts?new=1"), "Other company scope"), "/accounts/save")
    other_book_id = int(other_form.fields["book_id"])
    check(other_company_id != company_id, "Cross-company checks use a distinct sample company")
    for route in [f"/accounts?id={account_id}", f"/reports/account?id={account_id}",
                  f"/general-journals/detail?id={draft_id}", f"/general-journals/edit?id={draft_id}",
                  f"/journals/detail?id={journal_id}"]:
        denied = client.request(route)
        check(denied.status == 403, f"Selected-company scope rejects foreign object at {urlparse(route).path}")
    foreign_account = client.request("/accounts/save", account_edit.fields | {
        "company_id": str(other_company_id), "book_id": str(other_book_id), "reason": "Foreign account attempt",
    })
    check(foreign_account.status == 403, "Cross-company account mutation rejects an existing foreign account ID")
    foreign_draft = client.request("/general-journals/save", new_draft.fields | values | {
        "company_id": str(other_company_id), "book_id": str(other_book_id), "creation_key": uuid.uuid4().hex,
    })
    check(foreign_draft.status == 422, "New draft cannot reference accounts from another company")
    for route, fields in [("/general-journals/post", post_form.fields),
                          ("/general-journals/reverse", reverse_form.fields | reverse_values)]:
        denied = client.request(route, fields | {"company_id": str(other_company_id), "book_id": str(other_book_id)})
        check(denied.status == 403, f"Cross-company mutation rejects a foreign journal at {route}")
    check(http.totals(report(client, reverse_date)) == (Decimal("0"), Decimal("0")),
          "Foreign-object attempts leave the second company's ledger empty")
    select_company(client, company_id)
    check(http.totals(report(client, reverse_date)) == before_reversal_date,
          "Cross-company attempts preserve the original company's reconciled balance")

    for route in ["/accounts/save", "/general-journals/save", "/general-journals/post", "/general-journals/reverse"]:
        denied = client.request(route)
        check(denied.status == 405 and denied.headers.get("Allow") == "POST",
              f"{route} rejects GET")
    client.submit(form(ok(client.request("/companies"), "Logout"), "/logout"))
    for route in ["/accounts", "/general-journals", f"/general-journals/detail?id={draft_id}"]:
        check(urlparse(client.request(route).url).path == path("/login"),
              f"Logged-out session cannot read {urlparse(route).path}")
    return {
        "passed": len(checks), "failed": 0, "skipped": skipped, "target": http.ORIGIN,
        "company_id": company_id, "book_id": book_id, "account_ids": [account_id, income_id],
        "general_draft_id": draft_id, "journal_id": journal_id, "reversal_journal_id": reversal_id,
        "posting_date": post_date.isoformat(), "reversal_date": reverse_date.isoformat(),
        "cross_company_id": other_company_id,
        "data": "Two sample accounts and a fully reversed journal retained; an empty scope-test company retained.",
    }


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    parser.add_argument("--base-url", default="http://127.0.0.1:18205", help="Loopback HTTP(S) installation URL.")
    parser.add_argument("--email", default=os.environ.get("PL_HTTP_EMAIL", ""))
    parser.add_argument("--password", default=os.environ.get("PL_HTTP_PASSWORD", ""), help="Prefer PL_HTTP_PASSWORD.")
    parser.add_argument("--company-id", type=int, help="Existing sample writable company; otherwise create one.")
    parser.add_argument("--date", default=datetime.now(timezone.utc).date().isoformat(), help="Open posting date, ISO format.")
    parser.add_argument("--reversal-date", help="Later open date; defaults to posting date plus one day.")
    parser.add_argument("--viewer-email", default=os.environ.get("PL_HTTP_VIEWER_EMAIL", ""))
    parser.add_argument("--viewer-password", default=os.environ.get("PL_HTTP_VIEWER_PASSWORD", ""),
                        help="Prefer PL_HTTP_VIEWER_PASSWORD; viewer must belong to the selected company.")
    options = parser.parse_args()
    try:
        if options.company_id is not None and options.company_id < 1:
            raise ValueError("Company ID must be positive.")
        print(json.dumps(run(options), indent=2))
    except Exception as error:
        print(f"FAIL: {error}", file=sys.stderr)
        sys.exit(1)
