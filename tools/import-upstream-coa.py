#!/usr/bin/env python3
"""Mirror upstream country charts of accounts into PHP Ledger's research format.

Reads pinned checkouts of Odoo (``addons/l10n_*/data/template/account.account-*.csv``
plus the ``@template`` Python metadata) and ERPNext
(``erpnext/accounts/doctype/account/chart_of_accounts/{verified,unverified}/*.json``
plus the two Python "Standard" charts) and writes one
``phpledger.coa.upstream-chart`` JSON document per chart, a flat CSV per chart and
an ``index.json`` catalogue.

The output is RESEARCH EVIDENCE for authoring PHP Ledger country packages. It is
not a runtime seed: every file carries ``runtime_importable: false`` and the
regional catalogue contract (docs/coa/regional-program/) still governs what may be
installed into a real company.

Usage (both source trees must already be checked out at the commits you want to pin):

    python3 tools/import-upstream-coa.py \
        --odoo-src /path/to/odoo --odoo-branch 19.0 \
        --erpnext-src /path/to/erpnext --erpnext-branch version-16 \
        --out resources/coa/upstream

Commit hashes and dates are read from the checkouts with ``git`` when available;
pass ``--odoo-commit/--odoo-commit-date`` (and the ERPNext equivalents) otherwise.
No network access is used.
"""

from __future__ import annotations

import argparse
import ast
import csv
import datetime as dt
import glob
import hashlib
import json
import os
import re
import subprocess
import sys
import types
from collections import Counter, OrderedDict, defaultdict

FORMAT = "phpledger.coa.upstream-chart"
FORMAT_VERSION = 1
INDEX_FORMAT = "phpledger.coa.upstream-index"

NOTICE = (
    "Upstream chart mirrored for research and country-package authoring. "
    "Not accountant reviewed, not a PHP Ledger release, not installable into a real "
    "company, and not a statement that any account number is legally mandated. "
    "Root types, normal balances and role hints were derived mechanically from the "
    "upstream account_type vocabulary and must be reviewed before reuse."
)

ROOT_TYPES = ("asset", "liability", "equity", "income", "expense")
NORMAL_BALANCE = {"asset": "debit", "expense": "debit", "liability": "credit", "equity": "credit", "income": "credit"}

# ---------------------------------------------------------------------------
# Unified vocabulary
# ---------------------------------------------------------------------------
# subtype -> (root_type, phpledger runtime role hint or None)
ODOO_TYPE_MAP = {
    "asset_receivable": ("asset", "receivable", "receivables"),
    "asset_cash": ("asset", "cash_bank", "cash_bank"),
    "asset_current": ("asset", "current_asset", None),
    "asset_non_current": ("asset", "non_current_asset", None),
    "asset_prepayments": ("asset", "prepayment", None),
    "asset_fixed": ("asset", "fixed_asset", None),
    "liability_payable": ("liability", "payable", "payables"),
    "liability_credit_card": ("liability", "credit_card", None),
    "liability_current": ("liability", "current_liability", None),
    "liability_non_current": ("liability", "non_current_liability", None),
    "equity": ("equity", "equity", None),
    "equity_unaffected": ("equity", "retained_earnings", None),
    "income": ("income", "income", None),
    "income_other": ("income", "other_income", None),
    "expense": ("expense", "expense", None),
    "expense_direct_cost": ("expense", "direct_cost", None),
    "expense_depreciation": ("expense", "depreciation", None),
    "expense_other": ("expense", "other_expense", None),
    "off_balance": (None, "off_balance", None),
}

ERPNEXT_ROOT_MAP = {"Asset": "asset", "Liability": "liability", "Equity": "equity", "Income": "income", "Expense": "expense"}

# ERPNext account_type -> (subtype, role hint). Root type always comes from root_type.
ERPNEXT_TYPE_MAP = {
    "Receivable": ("receivable", "receivables"),
    "Payable": ("payable", "payables"),
    "Bank": ("bank", "cash_bank"),
    "Cash": ("cash", "cash_bank"),
    "Stock": ("inventory", None),
    "Tax": ("tax", None),
    "Cost of Goods Sold": ("direct_cost", None),
    "Fixed Asset": ("fixed_asset", None),
    "Accumulated Depreciation": ("accumulated_depreciation", None),
    "Depreciation": ("depreciation", None),
    "Expense Account": ("expense", None),
    "Income Account": ("income", None),
    "Equity": ("equity", None),
    "Liability": ("current_liability", None),
    "Chargeable": ("chargeable", None),
    "Stock Received But Not Billed": ("stock_received_not_billed", None),
    "Asset Received But Not Billed": ("asset_received_not_billed", None),
    "Service Received But Not Billed": ("service_received_not_billed", None),
    "Stock Delivered But Not Billed": ("stock_delivered_not_billed", None),
    "Stock Adjustment": ("stock_adjustment", None),
    "Expenses Included In Valuation": ("valuation_expense", None),
    "Expenses Included In Asset Valuation": ("asset_valuation_expense", None),
    "Capital Work in Progress": ("capital_work_in_progress", None),
    "Temporary": ("temporary", None),
    "Round Off": ("round_off", None),
    "Indirect Expense": ("other_expense", None),
    "Indirect Income": ("other_income", None),
}
ERPNEXT_META_FIELDS = {"account_name", "account_number", "account_type", "account_category", "root_type", "is_group", "tax_rate", "account_currency"}


# Odoo templates whose account list is generated in Python from generic_coa.
# key: template code, value: id prefix applied to the generic_coa xmlids.
ODOO_DERIVED_FROM_GENERIC = {"ng": "l10n_ng_"}

# Odoo res.company / template_data keys that point at accounts, with the
# PHP Ledger-facing label used in ``role_defaults``.
ODOO_ROLE_FIELDS = OrderedDict([
    ("property_account_receivable_id", "receivable"),
    ("property_account_payable_id", "payable"),
    ("income_account_id", "income"),
    ("expense_account_id", "expense"),
    ("property_account_income_categ_id", "income"),
    ("property_account_expense_categ_id", "expense"),
    ("account_stock_valuation_id", "stock_valuation"),
    ("property_stock_valuation_account_id", "stock_valuation"),
    ("property_stock_account_input_categ_id", "stock_input"),
    ("property_stock_account_output_categ_id", "stock_output"),
    ("account_default_pos_receivable_account_id", "pos_receivable"),
    ("account_journal_suspense_account_id", "bank_suspense"),
    ("account_journal_payment_debit_account_id", "outstanding_receipts"),
    ("account_journal_payment_credit_account_id", "outstanding_payments"),
    ("income_currency_exchange_account_id", "currency_exchange_gain"),
    ("expense_currency_exchange_account_id", "currency_exchange_loss"),
    ("default_cash_difference_income_account_id", "cash_difference_gain"),
    ("default_cash_difference_expense_account_id", "cash_difference_loss"),
    ("account_journal_early_pay_discount_gain_account_id", "early_payment_discount_gain"),
    ("account_journal_early_pay_discount_loss_account_id", "early_payment_discount_loss"),
    ("account_production_wip_account_id", "production_wip"),
    ("account_production_wip_overhead_account_id", "production_overhead"),
    ("account_discount_income_allocation_id", "discount_income_allocation"),
    ("account_discount_expense_allocation_id", "discount_expense_allocation"),
    ("deferred_expense_account_id", "deferred_expense"),
    ("deferred_revenue_account_id", "deferred_revenue"),
    ("account_opening_date", None),
])
ODOO_PREFIX_FIELDS = OrderedDict([
    ("bank_account_code_prefix", "bank"),
    ("cash_account_code_prefix", "cash"),
    ("transfer_account_code_prefix", "transfer"),
])


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------
def sha256_file(path: str) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as fh:
        for chunk in iter(lambda: fh.read(1 << 16), b""):
            h.update(chunk)
    return h.hexdigest()


def git_info(path: str):
    try:
        out = subprocess.run(["git", "-C", path, "log", "-1", "--format=%H%n%cI"], capture_output=True, text=True, check=True).stdout.split()
        return out[0], out[1]
    except Exception:
        return None, None


def load_country_names(repo_root: str | None):
    names = {}
    candidates = []
    if repo_root:
        candidates.append(os.path.join(repo_root, "resources", "locale", "country-defaults-cldr48.json"))
    candidates.append(os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "resources", "locale", "country-defaults-cldr48.json"))
    for c in candidates:
        if os.path.exists(c):
            with open(c, encoding="utf-8") as fh:
                data = json.load(fh)
            for code, rec in data.get("countries", {}).items():
                if isinstance(rec, dict) and rec.get("name"):
                    names[code.upper()] = rec["name"]
            if names:
                return names, os.path.relpath(c, repo_root) if repo_root else c
    try:  # optional fallback
        import pycountry  # type: ignore

        for c in pycountry.countries:
            names[c.alpha_2] = getattr(c, "common_name", None) or c.name
        return names, "pycountry"
    except Exception:
        return names, None


def norm_bool(v):
    if v is None:
        return None
    if isinstance(v, bool):
        return v
    s = str(v).strip().lower()
    if s in ("true", "1", "yes"):
        return True
    if s in ("false", "0", "no", ""):
        return False
    return None


def split_list(v):
    if not v:
        return []
    if isinstance(v, (list, tuple)):
        return [str(x) for x in v]
    return [x.strip() for x in str(v).split(",") if x.strip()]


def const_dict(node):
    """Return {const_key: const_value} for a Python ast.Dict, tolerating `_()` calls."""
    out = {}
    if not isinstance(node, ast.Dict):
        return out
    for k, v in zip(node.keys, node.values):
        if not isinstance(k, ast.Constant):
            continue
        if isinstance(v, ast.Constant):
            out[k.value] = v.value
        elif isinstance(v, ast.Call) and getattr(v.func, "id", "") == "_" and v.args and isinstance(v.args[0], ast.Constant):
            out[k.value] = v.args[0].value
        elif isinstance(v, ast.Dict):
            out[k.value] = const_dict(v)
    return out


def canonical_json(obj) -> str:
    return json.dumps(obj, ensure_ascii=False, sort_keys=True, separators=(",", ":"))


def write_json(path, obj, row_keys=("accounts", "groups")):
    """Pretty-print the envelope but keep each account/group on one line (diff-friendly, ~40% smaller)."""
    os.makedirs(os.path.dirname(path), exist_ok=True)
    parts = []
    items = list(obj.items())
    for i, (k, v) in enumerate(items):
        if k in row_keys and isinstance(v, list):
            if v:
                body = ",\n".join("    " + json.dumps(x, ensure_ascii=False, separators=(", ", ": ")) for x in v)
                text = f'  "{k}": [\n{body}\n  ]'
            else:
                text = f'  "{k}": []'
        else:
            text = f'  "{k}": ' + json.dumps(v, ensure_ascii=False, indent=2).replace("\n", "\n  ")
        parts.append(text + ("," if i < len(items) - 1 else ""))
    with open(path, "w", encoding="utf-8", newline="\n") as fh:
        fh.write("{\n" + "\n".join(parts) + "\n}\n")


def compact_row(row):
    """Drop empty optional fields from an account/group row."""
    keep = OrderedDict()
    for k, v in row.items():
        if k in ("source_id", "name", "root_type", "subtype", "normal_balance", "is_group", "parent_source_id", "code"):
            keep[k] = v
        elif v not in (None, "", [], {}):
            keep[k] = v
    return keep


CSV_COLUMNS = ["code", "name", "root_type", "subtype", "normal_balance", "is_group", "role_hint", "parent_path", "source_type", "reconcile", "description", "source_id", "flags"]


def write_csv(path, doc):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    group_path = {}
    for g in doc.get("groups", []):
        group_path[g["source_id"]] = " / ".join(g.get("path", [])) or g.get("name", "")
    with open(path, "w", encoding="utf-8", newline="") as fh:
        w = csv.writer(fh)
        w.writerow(CSV_COLUMNS)
        for a in doc["accounts"]:
            src = a.get("source", {})
            parent = a.get("parent_source_id") or ""
            w.writerow([
                a.get("code") or "",
                a.get("name") or "",
                a.get("root_type") or "",
                a.get("subtype") or "",
                a.get("normal_balance") or "",
                "1" if a.get("is_group") else "0",
                a.get("role_hint") or "",
                group_path.get(parent, parent),
                src.get("account_type") or "",
                "" if a.get("reconcile") is None else ("1" if a.get("reconcile") else "0"),
                a.get("description") or "",
                a.get("source_id") or "",
                ";".join(a.get("flags", [])),
            ])


def dedupe(doc, seen):
    """Replace an account list that is byte-identical to an earlier chart with a pointer.

    Odoo's 17 OHADA country templates, the French overseas templates and Northern Ireland
    inherit their parent's rows unchanged; ERPNext ships one SYSCOHADA tree per member state.
    Keeping one copy avoids ~100 MB of duplicates while every country file still exists.
    """
    payload = canonical_json({"accounts": doc["accounts"], "groups": doc.get("groups", [])})
    digest = hashlib.sha256(payload.encode("utf-8")).hexdigest()
    doc["accounts_digest"] = digest
    if digest in seen and seen[digest] != doc["chart_id"]:
        doc["accounts_identical_to"] = seen[digest]
        doc["accounts"] = []
        if "groups" in doc:
            doc["groups"] = []
    else:
        seen.setdefault(digest, doc["chart_id"])
        doc["accounts_identical_to"] = None
    # keep the pointer near the top of the envelope
    for key in ("accounts_identical_to", "accounts_digest"):
        doc.move_to_end(key, last=False)
    for key in ("runtime_importable", "status", "format_version", "format"):
        doc.move_to_end(key, last=False)


def summarize(accounts):
    by_root = Counter(a.get("root_type") or "unclassified" for a in accounts)
    by_sub = Counter(a.get("subtype") or "unspecified" for a in accounts)
    codes = Counter(a["code"] for a in accounts if a.get("code") and not a.get("is_group"))
    dupes = sorted(c for c, n in codes.items() if n > 1)
    posting = [a for a in accounts if not a.get("is_group")]
    return {
        "accounts_total": len(accounts),
        "posting_accounts": len(posting),
        "group_accounts": len(accounts) - len(posting),
        "by_root_type": dict(sorted(by_root.items())),
        "by_subtype": dict(sorted(by_sub.items())),
        "unclassified_root_type": sum(1 for a in accounts if not a.get("root_type")),
        "duplicate_codes": dupes,
        "missing_codes": sum(1 for a in posting if not a.get("code")),
        "role_hints": dict(sorted(Counter(a["role_hint"] for a in accounts if a.get("role_hint")).items())),
    }


# ---------------------------------------------------------------------------
# Odoo
# ---------------------------------------------------------------------------
class OdooSource:
    def __init__(self, src, branch, commit, commit_date, retrieved_at):
        self.src = src
        self.branch = branch
        self.commit = commit
        self.commit_date = commit_date
        self.retrieved_at = retrieved_at
        self.addons = os.path.join(src, "addons")
        self.templates = {}  # code -> {"template_data":{}, "res_company":{}, "account_account":{}, "modules":set(), "python_files":set()}
        self.manifests = {}
        self.csv_files = defaultdict(list)  # (model, code) -> [paths]
        self.warnings = []
        self.license_file = None
        for cand in ("LICENSE", "COPYING"):
            p = os.path.join(src, cand)
            if os.path.exists(p):
                self.license_file = p
                break

    # -- discovery ----------------------------------------------------------
    def scan(self):
        for path in sorted(glob.glob(os.path.join(self.addons, "*", "models", "*.py"))):
            self._scan_python(path)
        for path in sorted(glob.glob(os.path.join(self.addons, "*", "data", "template", "*.csv"))):
            base = os.path.basename(path)[:-4]
            if "-" not in base:
                continue
            model, code = base.split("-", 1)
            self.csv_files[(model, code)].append(path)
        for path in sorted(glob.glob(os.path.join(self.addons, "*", "__manifest__.py"))):
            mod = os.path.basename(os.path.dirname(path))
            try:
                with open(path, encoding="utf-8") as fh:
                    self.manifests[mod] = ast.literal_eval(fh.read())
            except Exception as exc:  # pragma: no cover
                self.warnings.append(f"manifest unreadable: {path}: {exc}")

    def _scan_python(self, path):
        mod = os.path.relpath(path, self.addons).split(os.sep)[0]
        try:
            with open(path, encoding="utf-8") as fh:
                source = fh.read()
            tree = ast.parse(source)
        except Exception as exc:
            self.warnings.append(f"python unreadable: {path}: {exc}")
            return
        for node in ast.walk(tree):
            if not isinstance(node, ast.FunctionDef):
                continue
            for dec in node.decorator_list:
                if not (isinstance(dec, ast.Call) and getattr(dec.func, "id", "") == "template"):
                    continue
                args = [a.value for a in dec.args if isinstance(a, ast.Constant)]
                kwargs = {k.arg: k.value.value for k in dec.keywords if isinstance(k.value, ast.Constant)}
                code = args[0] if args else kwargs.get("template_code")
                model = args[1] if len(args) > 1 else kwargs.get("model", "template_data")
                if code is None:
                    continue  # generic hook such as @template(model='account.journal')
                rec = self.templates.setdefault(code, {"template_data": {}, "res_company": {}, "account_account": {}, "modules": set(), "python_files": set(), "non_literal": []})
                rec["modules"].add(mod)
                rec["python_files"].add(os.path.relpath(path, self.src))
                returns = [n for n in ast.walk(node) if isinstance(n, ast.Return) and n.value is not None]
                literal = returns[0].value if returns else None
                if model == "template_data":
                    if isinstance(literal, ast.Dict):
                        rec["template_data"].update(const_dict(literal))
                    else:
                        rec["non_literal"].append(f"{model}:{node.name}")
                elif model == "res.company":
                    if isinstance(literal, ast.Dict) and literal.values and isinstance(literal.values[0], ast.Dict):
                        rec["res_company"].update(const_dict(literal.values[0]))
                    else:
                        rec["non_literal"].append(f"{model}:{node.name}")
                elif model == "account.account":
                    if isinstance(literal, ast.Dict):
                        for xmlid, vals in const_dict(literal).items():
                            if isinstance(vals, dict):
                                rec["account_account"].setdefault(xmlid, {}).update(vals)
                    if "_parse_csv(" in source and code not in ODOO_DERIVED_FROM_GENERIC:
                        rec["non_literal"].append(f"{model}:{node.name} (parses another template's CSV)")
                    elif not isinstance(literal, ast.Dict):
                        rec["non_literal"].append(f"{model}:{node.name}")

    # -- chain / merge -------------------------------------------------------
    def chain(self, code):
        """Return [root_parent, ..., code] following template_data['parent']."""
        out = []
        seen = set()
        while code and code not in seen:
            seen.add(code)
            out.append(code)
            code = self.templates.get(code, {}).get("template_data", {}).get("parent")
        return out[::-1]

    def merged_template_data(self, code):
        data = {}
        for c in self.chain(code):
            data.update(self.templates.get(c, {}).get("template_data", {}))
        return data

    def merged_res_company(self, code):
        data = {}
        for c in self.chain(code):
            data.update(self.templates.get(c, {}).get("res_company", {}))
        return data

    def read_csv_rows(self, model, code):
        """Rows for one template code from every module providing the file (Odoo merge semantics)."""
        merged = OrderedDict()
        files = []
        for path in self.csv_files.get((model, code), []):
            files.append(path)
            with open(path, encoding="utf-8-sig", newline="") as fh:
                for row in csv.DictReader(fh):
                    rid = (row.get("id") or "").strip()
                    if rid:
                        rec = merged.setdefault(rid, {})
                        for key, value in row.items():
                            if key is None or key == "id" or "/" in key:
                                continue
                            if value is None:
                                continue
                            value = value.strip() if isinstance(value, str) else value
                            if value != "":
                                rec[key] = value
                    # continuation rows (x2many "/" columns) carry no scalar data we mirror
        return merged, files

    def merged_model(self, model, code):
        rows = OrderedDict()
        files = []
        for c in self.chain(code):
            part, part_files = self.read_csv_rows(model, c)
            files.extend(part_files)
            for rid, rec in part.items():
                rows.setdefault(rid, {}).update(rec)
        return rows, files

    # -- conversion ----------------------------------------------------------
    @staticmethod
    def _fiscal_code(value):
        """Odoo res.country xmlid (base.pk) -> ISO alpha-2. base.uk is the one non-ISO xmlid."""
        if isinstance(value, str) and value.startswith("base.") and len(value) == 7:
            return {"UK": "GB"}.get(value[5:].upper(), value[5:].upper())
        return None

    def country_for(self, code, own_company, merged_company, module, country_names):
        own = self._fiscal_code(own_company.get("account_fiscal_country_id"))
        if own:
            return own, "template res.company account_fiscal_country_id"
        # child templates such as gf/gp/mq/re/yt (France overseas) or bf/bj (OHADA) name their territory in the code
        cand = code.split("_")[0].upper()
        inherited = self._fiscal_code(merged_company.get("account_fiscal_country_id"))
        if len(cand) == 2 and cand.isalpha() and cand in country_names:
            basis = "derived from template code"
            if inherited and inherited != cand:
                basis += f"; Odoo fiscal country inherited from parent template is {inherited}"
            return cand, basis
        if inherited:
            return inherited, "fiscal country inherited from parent template"
        m = re.match(r"l10n_([a-z]{2})(?:_|$)", module or "")
        if m and m.group(1).upper() in country_names:
            return m.group(1).upper(), "derived from module name"
        return None, "no fiscal country declared (regional or generic template)"

    def build(self, code, country_names):
        chain = self.chain(code)
        tdata = self.merged_template_data(code)
        company = self.merged_res_company(code)
        accounts_raw, account_files = self.merged_model("account.account", code)
        groups_raw, group_files = self.merged_model("account.group", code)

        derived_prefix = ODOO_DERIVED_FROM_GENERIC.get(code)
        derived_from = None
        if derived_prefix:
            generic, generic_files = self.merged_model("account.account", "generic_coa")
            for rid, r in generic.items():
                accounts_raw.setdefault(derived_prefix + rid, {}).update(r)
            account_files = generic_files + account_files
            derived_from = "generic_coa"
            # The template copies generic_coa's defaults in Python with the id prefix applied.
            gen = self.templates.get("generic_coa", {})
            base_t = {k: (derived_prefix + v if isinstance(v, str) and k.endswith("_id") else v) for k, v in gen.get("template_data", {}).items() if k not in ("name", "country")}
            keep_unprefixed = ("account_fiscal_country_id", "account_stock_journal_id", "account_sale_tax_id", "account_purchase_tax_id")
            base_c = {k: (derived_prefix + v if isinstance(v, str) and k.endswith("_id") and k not in keep_unprefixed else v) for k, v in gen.get("res_company", {}).items() if k != "account_fiscal_country_id"}
            tdata = {**base_t, **tdata}
            company = {**base_c, **company}
        # python-defined account rows / overrides along the chain
        for c in chain:
            for xmlid, vals in self.templates.get(c, {}).get("account_account", {}).items():
                accounts_raw.setdefault(xmlid, {}).update({k: v for k, v in vals.items() if not isinstance(v, dict)})

        if not accounts_raw:
            return None

        modules = set()
        for c in chain:
            modules |= self.templates.get(c, {}).get("modules", set())
        for f in account_files + group_files:
            modules.add(os.path.relpath(f, self.addons).split(os.sep)[0])
        primary_module = None
        own = self.templates.get(code, {}).get("modules", set())
        if own:
            primary_module = sorted(own, key=lambda m: (not m.startswith("l10n_"), len(m)))[0]
        elif account_files:
            primary_module = os.path.relpath(account_files[-1], self.addons).split(os.sep)[0]
        manifest = self.manifests.get(primary_module, {})
        own_company = self.templates.get(code, {}).get("res_company", {})
        country_code, country_basis = self.country_for(code, own_company, company, primary_module, country_names)
        if code == "generic_coa":
            country_code, country_basis = None, "country-neutral generic template (Odoo sets US as a placeholder fiscal country)"

        # groups
        groups = []
        for gid, g in groups_raw.items():
            start = (g.get("code_prefix_start") or "").strip()
            end = (g.get("code_prefix_end") or "").strip() or start
            groups.append({"source_id": gid, "code_prefix_start": start, "code_prefix_end": end, "name": g.get("name", ""), "translations": {k[5:]: v for k, v in g.items() if k.startswith("name@")}})
        groups.sort(key=lambda g: (len(g["code_prefix_start"]), g["code_prefix_start"]))

        def group_contains(g, prefix):
            n = len(g["code_prefix_start"])
            if n == 0 or len(prefix) < n:
                return False
            p = prefix[:n]
            return g["code_prefix_start"] <= p <= g["code_prefix_end"][:n]

        def find_group(prefix, exclude=None):
            best = None
            for g in groups:
                if g is exclude or not group_contains(g, prefix):
                    continue
                if exclude is not None and len(g["code_prefix_start"]) >= len(exclude["code_prefix_start"]):
                    continue
                if best is None or len(g["code_prefix_start"]) > len(best["code_prefix_start"]):
                    best = g
            return best

        gindex = {g["source_id"]: g for g in groups}
        for g in groups:
            parent = find_group(g["code_prefix_start"], exclude=g)
            g["parent_source_id"] = parent["source_id"] if parent else None
        for g in groups:
            path = []
            cur = g
            guard = 0
            while cur and guard < 50:
                path.append(cur["name"])
                cur = gindex.get(cur["parent_source_id"]) if cur["parent_source_id"] else None
                guard += 1
            g["path"] = path[::-1]

        # accounts
        accounts = []
        unmapped = Counter()
        for xmlid, r in accounts_raw.items():
            code_ = (r.get("code") or "").strip()
            name = (r.get("name") or "").strip()
            atype = (r.get("account_type") or "").strip()
            flags = []
            if not code_:
                flags.append("missing_code")
            if not name:
                flags.append("missing_name")
            if not code_ and not name and not atype:
                # inherited override rows that only add tax_ids / asset models to a parent id we never saw
                flags.append("incomplete_override_row")
            root, subtype, role = (None, "unspecified", None)
            if atype in ODOO_TYPE_MAP:
                root, subtype, role = ODOO_TYPE_MAP[atype]
            elif atype:
                unmapped[atype] += 1
                flags.append("unmapped_account_type")
            else:
                flags.append("missing_account_type")
            if atype == "off_balance":
                flags.append("off_balance")
            active = norm_bool(r.get("active"))
            if active is False:
                flags.append("inactive_in_source")
            translations = {k[5:]: v for k, v in r.items() if k.startswith("name@") and v}
            descriptions = {k[12:]: v for k, v in r.items() if k.startswith("description@") and v}
            group = find_group(code_) if code_ else None
            accounts.append({
                "source_id": xmlid,
                "code": code_ or None,
                "name": name,
                "translations": translations,
                "root_type": root,
                "subtype": subtype,
                "normal_balance": NORMAL_BALANCE.get(root) if root else None,
                "is_group": False,
                "parent_source_id": group["source_id"] if group else None,
                "reconcile": norm_bool(r.get("reconcile")),
                "description": (r.get("description") or "").strip() or None,
                "role_hint": role,
                "flags": flags,
                "source": {k: v for k, v in {
                    "account_type": atype or None,
                    "tag_ids": split_list(r.get("tag_ids")),
                    "tax_ids": split_list(r.get("tax_ids")),
                    "asset_model_ids": split_list(r.get("asset_model_ids")),
                    "non_trade": norm_bool(r.get("non_trade")),
                    "active": active,
                    "descriptions": descriptions or None,
                    "extra": {k: v for k, v in r.items() if k not in ("code", "name", "account_type", "tag_ids", "tax_ids", "asset_model_ids", "non_trade", "active", "reconcile", "description") and "@" not in k} or None,
                }.items() if v not in (None, [], {})},
            })
        accounts.sort(key=lambda a: ((a["code"] or "~"), a["source_id"]))
        by_id = {a["source_id"]: a for a in accounts}

        # role defaults
        role_defaults = OrderedDict()
        merged_fields = OrderedDict()
        merged_fields.update(tdata)
        merged_fields.update(company)
        for field, label in ODOO_ROLE_FIELDS.items():
            if label is None or field not in merged_fields:
                continue
            xmlid = merged_fields[field]
            if not isinstance(xmlid, str):
                continue
            target = by_id.get(xmlid)
            entry = {"source_field": field, "source_id": xmlid, "code": target["code"] if target else None, "name": target["name"] if target else None, "resolved": target is not None}
            bucket = role_defaults.setdefault(label, [])
            if any(e["source_id"] == xmlid for e in bucket):
                continue
            bucket.append(entry)
            if target and label in ("receivable", "payable", "income", "expense") and not target.get("role_hint"):
                target["role_hint"] = {"receivable": "receivables", "payable": "payables", "income": "income", "expense": "expense"}[label]
                target["flags"].append("default_for_" + label)
        prefixes = OrderedDict((label, company[field]) for field, label in ODOO_PREFIX_FIELDS.items() if field in company)

        langs = sorted({lang for a in accounts for lang in a["translations"]})
        display_name = tdata.get("name") or manifest.get("name") or code
        summary = summarize(accounts)
        summary["unmapped_account_types"] = dict(unmapped)
        summary["translation_languages"] = langs
        summary["groups"] = len(groups)

        doc = OrderedDict()
        doc["format"] = FORMAT
        doc["format_version"] = FORMAT_VERSION
        doc["status"] = "Research"
        doc["runtime_importable"] = False
        doc["notice"] = NOTICE
        doc["chart_id"] = f"odoo-{self.branch}/{code}"
        doc["country_code"] = country_code
        doc["country_name"] = country_names.get(country_code) if country_code else None
        doc["country_basis"] = country_basis
        doc["name"] = display_name
        doc["source"] = OrderedDict([
            ("system", "odoo"),
            ("repository", "https://github.com/odoo/odoo"),
            ("branch", self.branch),
            ("commit", self.commit),
            ("commit_date", self.commit_date),
            ("retrieved_at", self.retrieved_at),
            ("license", "LGPL-3.0-only"),
            ("license_basis", f"module manifest '{primary_module}' declares {manifest.get('license') or 'no licence key'}; repository LICENSE file is LGPL-3"),
            ("attribution", manifest.get("author") or "Odoo S.A."),
            ("module", primary_module),
            ("module_name", manifest.get("name")),
            ("module_version", manifest.get("version")),
            ("contributing_modules", sorted(modules)),
            ("template_code", code),
            ("template_chain", chain),
            ("derived_from_template", derived_from),
            ("visible_in_odoo", tdata.get("visible", True) not in (False, 0)),
            ("files", [OrderedDict([("path", os.path.relpath(f, self.src).replace(os.sep, "/")), ("sha256", sha256_file(f)), ("rows", self._row_count(f))]) for f in account_files + group_files]),
            ("python_files", sorted(set().union(*[self.templates.get(c, {}).get("python_files", set()) for c in chain]))),
            ("non_literal_python", sorted(set().union(*[set(self.templates.get(c, {}).get("non_literal", [])) for c in chain]))),
        ])
        doc["template"] = OrderedDict([
            ("code_digits", tdata.get("code_digits")),
            ("anglo_saxon_accounting", company.get("anglo_saxon_accounting")),
            ("account_code_prefixes", prefixes),
            ("default_sale_tax", company.get("account_sale_tax_id")),
            ("default_purchase_tax", company.get("account_purchase_tax_id")),
            ("tax_calculation_rounding_method", company.get("tax_calculation_rounding_method")),
        ])
        doc["role_defaults"] = role_defaults
        doc["type_map"] = {k: {"root_type": v[0], "subtype": v[1], "role_hint": v[2]} for k, v in ODOO_TYPE_MAP.items()}
        doc["summary"] = summary
        doc["groups"] = [compact_row(g) for g in groups]
        doc["accounts"] = [compact_row(a) for a in accounts]
        return doc

    @staticmethod
    def _row_count(path):
        with open(path, encoding="utf-8-sig", newline="") as fh:
            return sum(1 for r in csv.DictReader(fh) if (r.get("id") or "").strip())


# ---------------------------------------------------------------------------
# ERPNext
# ---------------------------------------------------------------------------
class ErpnextSource:
    def __init__(self, src, branch, commit, commit_date, retrieved_at):
        self.src = src
        self.branch = branch
        self.commit = commit
        self.commit_date = commit_date
        self.retrieved_at = retrieved_at
        self.dir = os.path.join(src, "erpnext", "accounts", "doctype", "account", "chart_of_accounts")
        self.warnings = []
        self.license_file = None
        for cand in ("license.txt", "LICENSE"):
            p = os.path.join(src, cand)
            if os.path.exists(p):
                self.license_file = p
                break

    def charts(self):
        out = []
        for folder in ("verified", "unverified"):
            for path in sorted(glob.glob(os.path.join(self.dir, folder, "*.json"))):
                out.append((folder, path, None))
            for path in sorted(glob.glob(os.path.join(self.dir, folder, "*.py"))):
                base = os.path.basename(path)
                if base.startswith("standard_chart_of_accounts"):
                    out.append((folder, path, "python"))
        return out

    def load(self, path, kind):
        if kind == "python":
            with open(path, encoding="utf-8") as fh:
                source = fh.read()
            fake = types.ModuleType("frappe")
            fake._ = lambda s: s  # noqa: E731
            ns = {"__name__": "erpnext_standard_chart"}
            saved = sys.modules.get("frappe")
            sys.modules["frappe"] = fake
            try:
                exec(compile(source, path, "exec"), ns)  # trusted pinned upstream file
            finally:
                if saved is not None:
                    sys.modules["frappe"] = saved
                else:
                    sys.modules.pop("frappe", None)
            tree = ns["get"]()
            name = "Standard" if "with_account_number" not in path else "Standard with Numbers"
            return {"name": name, "country_code": None, "tree": tree}
        with open(path, encoding="utf-8") as fh:
            return json.load(fh, object_pairs_hook=OrderedDict)

    def build(self, folder, path, kind, country_names):
        data = self.load(path, kind)
        tree = data.get("tree") or {}
        raw_cc = data.get("country_code")
        country_code = None
        country_basis = "chart file country_code"
        if isinstance(raw_cc, str) and len(raw_cc.strip()) == 2:
            country_code = raw_cc.strip().upper()
        elif data.get("country"):
            cand = str(data["country"]).strip()
            rev = {v.lower(): k for k, v in country_names.items()}
            country_code = rev.get(cand.lower())
            country_basis = f"chart file country name '{cand}'"
        elif raw_cc:
            country_basis = f"chart file country_code '{raw_cc}' is not an ISO alpha-2 code (regional standard)"
        else:
            country_basis = "no country in chart file"
        if country_code and country_code not in country_names:
            country_basis += " (code not in CLDR territory list)"

        accounts = []
        unmapped = Counter()
        inferred_roots = []

        def walk(node, path, parent_id, root_name, root_type, root_inferred):
            for key, child in node.items():
                if key in ERPNEXT_META_FIELDS or not isinstance(child, dict):
                    continue
                name = (child.get("account_name") or key).strip() if isinstance(child.get("account_name"), str) else key.strip()
                cur_path = path + [name]
                source_id = " / ".join(cur_path)
                this_root_type = root_type
                this_inferred = root_inferred
                src_root = child.get("root_type")
                if isinstance(src_root, str) and src_root.strip():
                    mapped = ERPNEXT_ROOT_MAP.get(src_root.strip())
                    if mapped:
                        this_root_type, this_inferred = mapped, False
                if this_root_type is None and not path:  # root node without root_type: upstream defect, never guessed here
                    inferred_roots.append(name)
                children_keys = [k for k in child if k not in ERPNEXT_META_FIELDS and isinstance(child[k], dict)]
                is_group = bool(child.get("is_group")) or bool(children_keys)
                atype = child.get("account_type")
                atype = atype.strip() if isinstance(atype, str) else None
                subtype, role = ("group" if is_group else "unspecified"), None
                flags = []
                if atype:
                    if atype in ERPNEXT_TYPE_MAP:
                        subtype, role = ERPNEXT_TYPE_MAP[atype]
                    else:
                        unmapped[atype] += 1
                        flags.append("unmapped_account_type")
                if this_root_type is None:
                    flags.append("missing_root_type")
                if is_group and role:
                    role = None  # groups cannot receive postings; keep the subtype as evidence only
                number = child.get("account_number")
                number = str(number).strip() if number not in (None, "") else None
                if not number and not is_group:
                    flags.append("missing_code")
                normal = NORMAL_BALANCE.get(this_root_type) if this_root_type else None
                if subtype == "accumulated_depreciation" and this_root_type == "asset":
                    normal = "credit"
                    flags.append("contra")
                accounts.append({
                    "source_id": source_id,
                    "code": number,
                    "name": name,
                    "root_type": this_root_type,
                    "subtype": subtype,
                    "normal_balance": normal,
                    "is_group": is_group,
                    "parent_source_id": parent_id,
                    "depth": len(cur_path),
                    "role_hint": role,
                    "flags": flags,
                    "source": {k: v for k, v in {
                        "root_type": src_root if isinstance(src_root, str) and src_root.strip() else None,
                        "account_type": atype,
                        "account_category": child.get("account_category"),
                        "is_group": child.get("is_group"),
                        "tax_rate": child.get("tax_rate"),
                        "account_currency": child.get("account_currency"),
                    }.items() if v not in (None, "")},
                })
                walk(child, cur_path, source_id, root_name or name, this_root_type, this_inferred)

        walk(tree, [], None, None, None, False)
        numbers_present = any(a["code"] for a in accounts if not a["is_group"])
        if not numbers_present:  # a names-only chart: per-row flags would be pure noise
            for a in accounts:
                a["flags"] = [f for f in a["flags"] if f != "missing_code"]
        summary = summarize(accounts)
        summary["unmapped_account_types"] = dict(unmapped)
        summary["roots_without_root_type"] = inferred_roots
        summary["top_level_groups"] = [a["name"] for a in accounts if a["parent_source_id"] is None]
        summary["max_depth"] = max((a["depth"] for a in accounts), default=0)
        summary["account_numbers_present"] = numbers_present

        stem = os.path.splitext(os.path.basename(path))[0]
        doc = OrderedDict()
        doc["format"] = FORMAT
        doc["format_version"] = FORMAT_VERSION
        doc["status"] = "Research"
        doc["runtime_importable"] = False
        doc["notice"] = NOTICE
        doc["chart_id"] = f"erpnext-{self.branch}/{folder}/{stem}"
        doc["country_code"] = country_code
        doc["country_name"] = country_names.get(country_code) if country_code else None
        doc["country_basis"] = country_basis
        doc["name"] = data.get("name") or stem
        doc["source"] = OrderedDict([
            ("system", "erpnext"),
            ("repository", "https://github.com/frappe/erpnext"),
            ("branch", self.branch),
            ("commit", self.commit),
            ("commit_date", self.commit_date),
            ("retrieved_at", self.retrieved_at),
            ("license", "GPL-3.0-only"),
            ("license_basis", "repository license.txt (GNU GPL v3); chart files carry no separate licence header"),
            ("attribution", "Frappe Technologies Pvt. Ltd. and Contributors"),
            ("upstream_folder", folder),
            ("upstream_verified", folder == "verified"),
            ("upstream_disabled", bool(data.get("disabled"))),
            ("files", [OrderedDict([("path", os.path.relpath(path, self.src).replace(os.sep, "/")), ("sha256", sha256_file(path)), ("kind", kind or "json")])]),
        ])
        doc["template"] = OrderedDict([
            ("hierarchy", "nested groups; posting accounts are leaves; source_id is the full ' / ' separated path"),
            ("account_numbers_present", numbers_present),
        ])
        doc["role_defaults"] = OrderedDict()
        for label, sub in (("receivable", "receivable"), ("payable", "payable"), ("cash", "cash"), ("bank", "bank"), ("inventory", "inventory"), ("direct_cost", "direct_cost"), ("round_off", "round_off"), ("temporary", "temporary")):
            hits = [a for a in accounts if a["subtype"] == sub and not a["is_group"]]
            if hits:
                doc["role_defaults"][label] = [{"source_field": "account_type", "source_id": a["source_id"], "code": a["code"], "name": a["name"], "resolved": True} for a in hits[:5]]
        doc["type_map"] = {k: {"subtype": v[0], "role_hint": v[1]} for k, v in ERPNEXT_TYPE_MAP.items()}
        doc["summary"] = summary
        doc["accounts"] = [compact_row(a) for a in accounts]
        return doc, stem


# ---------------------------------------------------------------------------
# main
# ---------------------------------------------------------------------------
def main(argv=None):
    ap = argparse.ArgumentParser(description=__doc__, formatter_class=argparse.RawDescriptionHelpFormatter)
    ap.add_argument("--odoo-src")
    ap.add_argument("--odoo-branch", default="19.0")
    ap.add_argument("--odoo-commit")
    ap.add_argument("--odoo-commit-date")
    ap.add_argument("--erpnext-src")
    ap.add_argument("--erpnext-branch", default="version-16")
    ap.add_argument("--erpnext-commit")
    ap.add_argument("--erpnext-commit-date")
    ap.add_argument("--out", required=True, help="output directory, e.g. resources/coa/upstream")
    ap.add_argument("--repo-root", help="PHP Ledger repository root (for the CLDR country-name file)")
    ap.add_argument("--no-csv", action="store_true")
    args = ap.parse_args(argv)

    retrieved_at = dt.datetime.now(dt.timezone.utc).replace(microsecond=0).isoformat()
    country_names, names_source = load_country_names(args.repo_root)
    index_entries = []
    warnings = []
    licenses = []
    seen_accounts = {}

    if args.odoo_src:
        commit, cdate = git_info(args.odoo_src)
        commit = args.odoo_commit or commit
        cdate = args.odoo_commit_date or cdate
        if not commit:
            ap.error("--odoo-commit is required when the Odoo checkout is not a git repository")
        odoo = OdooSource(args.odoo_src, args.odoo_branch, commit, cdate, retrieved_at)
        odoo.scan()
        warnings.extend(f"odoo: {w}" for w in odoo.warnings)
        out_dir = os.path.join(args.out, f"odoo-{args.odoo_branch}")
        codes = sorted(set(odoo.templates) | {c for (m, c) in odoo.csv_files if m == "account.account"})
        codes.sort(key=lambda c: (len(odoo.chain(c)), c))  # parents before the templates that inherit them
        for code in codes:
            doc = odoo.build(code, country_names)
            if doc is None:
                warnings.append(f"odoo: template '{code}' has no account rows (parent-only or python-generated); skipped")
                continue
            dedupe(doc, seen_accounts)
            write_json(os.path.join(out_dir, f"{code}.json"), doc)
            if not args.no_csv and not doc.get("accounts_identical_to"):
                write_csv(os.path.join(out_dir, "csv", f"{code}.csv"), doc)
            index_entries.append(OrderedDict([
                ("chart_id", doc["chart_id"]),
                ("system", "odoo"),
                ("country_code", doc["country_code"]),
                ("country_name", doc["country_name"]),
                ("name", doc["name"]),
                ("file", os.path.relpath(os.path.join(out_dir, f"{code}.json"), args.out).replace(os.sep, "/")),
                ("csv", None if (args.no_csv or doc.get("accounts_identical_to")) else os.path.relpath(os.path.join(out_dir, "csv", f"{code}.csv"), args.out).replace(os.sep, "/")),
                ("accounts_identical_to", doc.get("accounts_identical_to")),
                ("module", doc["source"]["module"]),
                ("template_chain", doc["source"]["template_chain"]),
                ("visible_in_odoo", doc["source"]["visible_in_odoo"]),
                ("license", doc["source"]["license"]),
                ("accounts_total", doc["summary"]["accounts_total"]),
                ("posting_accounts", doc["summary"]["posting_accounts"]),
                ("groups", doc["summary"]["groups"]),
                ("unclassified_root_type", doc["summary"]["unclassified_root_type"]),
                ("duplicate_codes", len(doc["summary"]["duplicate_codes"])),
                ("translation_languages", doc["summary"]["translation_languages"]),
                ("role_defaults_resolved", {k: all(e["resolved"] for e in v) for k, v in doc["role_defaults"].items()}),
            ]))
        if odoo.license_file:
            dst = os.path.join(out_dir, "LICENSE")
            os.makedirs(out_dir, exist_ok=True)
            with open(odoo.license_file, "rb") as src, open(dst, "wb") as dstf:
                dstf.write(src.read())
            licenses.append(OrderedDict([("system", "odoo"), ("file", os.path.relpath(dst, args.out).replace(os.sep, "/")), ("sha256", sha256_file(dst)), ("spdx", "LGPL-3.0-only")]))

    if args.erpnext_src:
        commit, cdate = git_info(args.erpnext_src)
        commit = args.erpnext_commit or commit
        cdate = args.erpnext_commit_date or cdate
        if not commit:
            ap.error("--erpnext-commit is required when the ERPNext checkout is not a git repository")
        erp = ErpnextSource(args.erpnext_src, args.erpnext_branch, commit, cdate, retrieved_at)
        out_dir = os.path.join(args.out, f"erpnext-{args.erpnext_branch}")
        charts = erp.charts()
        # regional base charts (SYSCOHADA) and the Standard charts first so country copies alias to them
        charts.sort(key=lambda t: (0 if os.path.basename(t[1]).startswith(("syscohada", "standard")) else 1, t[0] != "verified", t[1]))
        for folder, path, kind in charts:
            try:
                doc, stem = erp.build(folder, path, kind, country_names)
            except Exception as exc:
                warnings.append(f"erpnext: {path}: {exc}")
                continue
            dedupe(doc, seen_accounts)
            target = os.path.join(out_dir, folder, f"{stem}.json")
            write_json(target, doc)
            if not args.no_csv and not doc.get("accounts_identical_to"):
                write_csv(os.path.join(out_dir, folder, "csv", f"{stem}.csv"), doc)
            index_entries.append(OrderedDict([
                ("chart_id", doc["chart_id"]),
                ("system", "erpnext"),
                ("country_code", doc["country_code"]),
                ("country_name", doc["country_name"]),
                ("name", doc["name"]),
                ("file", os.path.relpath(target, args.out).replace(os.sep, "/")),
                ("csv", None if (args.no_csv or doc.get("accounts_identical_to")) else os.path.relpath(os.path.join(out_dir, folder, "csv", f"{stem}.csv"), args.out).replace(os.sep, "/")),
                ("accounts_identical_to", doc.get("accounts_identical_to")),
                ("upstream_folder", folder),
                ("upstream_disabled", doc["source"]["upstream_disabled"]),
                ("license", doc["source"]["license"]),
                ("accounts_total", doc["summary"]["accounts_total"]),
                ("posting_accounts", doc["summary"]["posting_accounts"]),
                ("groups", doc["summary"]["group_accounts"]),
                ("unclassified_root_type", doc["summary"]["unclassified_root_type"]),
                ("duplicate_codes", len(doc["summary"]["duplicate_codes"])),
                ("account_numbers_present", doc["template"]["account_numbers_present"]),
                ("roots_without_root_type", len(doc["summary"]["roots_without_root_type"])),
            ]))
        warnings.extend(f"erpnext: {w}" for w in erp.warnings)
        if erp.license_file:
            dst = os.path.join(out_dir, "LICENSE")
            os.makedirs(out_dir, exist_ok=True)
            with open(erp.license_file, "rb") as src, open(dst, "wb") as dstf:
                dstf.write(src.read())
            licenses.append(OrderedDict([("system", "erpnext"), ("file", os.path.relpath(dst, args.out).replace(os.sep, "/")), ("sha256", sha256_file(dst)), ("spdx", "GPL-3.0-only")]))

    # country roll-up
    by_country = OrderedDict()
    for e in sorted(index_entries, key=lambda e: (e["country_code"] or "~~", e["system"], e["chart_id"])):
        key = e["country_code"] or "none"
        rec = by_country.setdefault(key, OrderedDict([("country_code", e["country_code"]), ("country_name", e["country_name"]), ("odoo", []), ("erpnext", [])]))
        rec[e["system"]].append(e["chart_id"])

    index = OrderedDict([
        ("format", INDEX_FORMAT),
        ("format_version", FORMAT_VERSION),
        ("status", "Research"),
        ("runtime_importable", False),
        ("generated_at", retrieved_at),
        ("generator", "tools/import-upstream-coa.py"),
        ("notice", NOTICE),
        ("country_names_source", names_source),
        ("sources", [s for s in [
            OrderedDict([("system", "odoo"), ("repository", "https://github.com/odoo/odoo"), ("branch", args.odoo_branch), ("commit", args.odoo_commit or git_info(args.odoo_src)[0]), ("commit_date", args.odoo_commit_date or git_info(args.odoo_src)[1]), ("license", "LGPL-3.0-only")]) if args.odoo_src else None,
            OrderedDict([("system", "erpnext"), ("repository", "https://github.com/frappe/erpnext"), ("branch", args.erpnext_branch), ("commit", args.erpnext_commit or git_info(args.erpnext_src)[0]), ("commit_date", args.erpnext_commit_date or git_info(args.erpnext_src)[1]), ("license", "GPL-3.0-only")]) if args.erpnext_src else None,
        ] if s]),
        ("licenses", licenses),
        ("totals", OrderedDict([
            ("charts", len(index_entries)),
            ("odoo_charts", sum(1 for e in index_entries if e["system"] == "odoo")),
            ("erpnext_charts", sum(1 for e in index_entries if e["system"] == "erpnext")),
            ("countries_with_any_chart", sum(1 for k in by_country if k != "none")),
            ("countries_with_both_sources", sum(1 for r in by_country.values() if r["country_code"] and r["odoo"] and r["erpnext"])),
            ("accounts_total", sum(e["accounts_total"] for e in index_entries)),
        ])),
        ("warnings", warnings),
        ("countries", list(by_country.values())),
        ("charts", sorted(index_entries, key=lambda e: e["chart_id"])),
    ])
    write_json(os.path.join(args.out, "index.json"), index)
    print(f"wrote {len(index_entries)} charts to {args.out}; {len(warnings)} warning(s)")
    for w in warnings:
        print("  -", w)
    return 0


if __name__ == "__main__":
    sys.exit(main())
