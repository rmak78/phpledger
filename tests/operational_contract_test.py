"""Static admission checks for all eleven nested operational contracts."""
import json
import unittest
from decimal import Decimal
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SLUGS = (
    "service-agency", "seasonal-business", "retail-shop", "trader", "distributor",
    "restaurant", "membership-club", "pharmacy", "jewelry-studio",
    "light-manufacturing", "service-workshop",
)


class OperationalContractTests(unittest.TestCase):
    def pack(self, slug):
        with (ROOT / "resources" / "demo-packs" / f"{slug}-1.0.0.json").open(encoding="utf-8") as handle:
            return json.load(handle)

    def test_all_eleven_have_durable_balanced_contracts(self):
        for slug in SLUGS:
            with self.subTest(slug=slug):
                pack = self.pack(slug)
                evidence = pack["source_material"]["research_evidence"]
                events = evidence["operational_event_contract"]
                self.assertGreaterEqual(len(events), 8)
                ids = set()
                references = set()
                for event in events:
                    self.assertRegex(event["id"], r"^[A-Za-z0-9._:-]+$")
                    self.assertNotIn(event["id"], ids)
                    ids.add(event["id"])
                    self.assertRegex(event["date"], r"^2026-\d{2}-\d{2}$")
                    reference = event.get("idempotency_key") or event.get("source_reference")
                    self.assertTrue(reference)
                    self.assertNotIn(reference, references)
                    references.add(reference)
                    debit = sum((Decimal(line["debit"]) for line in event["expected_journal"]), Decimal("0"))
                    credit = sum((Decimal(line["credit"]) for line in event["expected_journal"]), Decimal("0"))
                    self.assertEqual(debit, credit)

                expected = evidence["expected_reports"]
                self.assertTrue(expected)
                self.assertTrue(evidence.get("contacts"))
                self.assertTrue(evidence.get("products"))

    def test_each_sample_has_a_research_backed_vertical_profile(self):
        with (ROOT / "resources" / "coa" / "industry-profiles-0.5.0.json").open(encoding="utf-8") as handle:
            catalogue = json.load(handle)
        with (ROOT / "resources" / "coa" / "research-index.json").open(encoding="utf-8") as handle:
            research_index = json.load(handle)
        registered_sources = {source["id"] for source in research_index["sources"]}
        profiles = {sample_id: profile for profile in catalogue["profiles"] for sample_id in profile["sample_ids"]}
        self.assertEqual(set(profiles), set(SLUGS))
        for slug in SLUGS:
            with self.subTest(slug=slug):
                profile = profiles[slug]
                self.assertTrue(profile["accounts"])
                self.assertTrue(profile["research_basis"])
                self.assertTrue(profile["seed_role_labels"])
                pack = self.pack(slug)
                evidence = pack["source_material"]["research_evidence"]
                self.assertEqual(evidence["industry_profile_id"], profile["id"])
                self.assertEqual(pack["source_material"]["industry_profile_id"], profile["id"])
                account_source_ids = {source_id for account in profile["accounts"] for source_id in account.get("source_ids", [])}
                self.assertTrue(account_source_ids)
                self.assertTrue(account_source_ids <= registered_sources, sorted(account_source_ids - registered_sources))
                self.assertTrue(all(reference.get("url") for reference in profile["research_basis"]))

    def test_each_sample_covers_the_shared_bookkeeping_and_reconciliation_trail(self):
        with (ROOT / "resources" / "coa" / "industry-profiles-0.5.0.json").open(encoding="utf-8") as handle:
            catalogue = json.load(handle)
        profiles = {sample_id: profile for profile in catalogue["profiles"] for sample_id in profile["sample_ids"]}
        for slug in SLUGS:
            with self.subTest(slug=slug):
                pack = self.pack(slug)
                evidence = pack["source_material"]["research_evidence"]
                events = evidence["operational_event_contract"]
                kinds = {event["kind"] for event in events}
                self.assertTrue(kinds & {"invoice", "credit_sale"})
                self.assertTrue(kinds & {"bill", "expense_bill", "purchase"})
                self.assertIn("receipt", kinds)
                self.assertIn("supplier_payment", kinds)
                self.assertTrue(kinds & {"customer_credit", "sales_return", "credit_sale"})
                corrections = [event for event in events if event["kind"] == "reversal"]
                self.assertTrue(corrections)
                self.assertTrue(all(event.get("reverses_event_id") for event in corrections))
                self.assertTrue(any(event.get("document_id") or event.get("related_document_id") or event.get("allocations") for event in events))

                receipt_settlements = [event for event in events if event["kind"] == "receipt"]
                self.assertTrue(len(receipt_settlements) >= 2 or any(len(event.get("allocations", [])) > 1 for event in receipt_settlements))
                reports = evidence["expected_reports"]
                ageing = reports["ageing"]
                receivable_total = reports.get("ar_ap", {}).get("receivable_balance", reports.get("accounts_receivable", "0.0000"))
                payable_total = reports.get("ar_ap", {}).get("payable_balance", reports.get("accounts_payable", "0.0000"))
                self.assertEqual(ageing["receivables"]["total"], receivable_total)
                self.assertEqual(ageing["payables"]["total"], payable_total)

                has_stock = any(item.get("kind") == "stock" for item in evidence.get("products", []))
                if has_stock:
                    movements = [movement for event in events for movement in event.get("stock_movements", [])]
                    self.assertTrue(any(Decimal(movement["quantity_delta"]) > 0 for movement in movements))
                    self.assertTrue(any(Decimal(movement["quantity_delta"]) < 0 for movement in movements))
                    counts = [event for event in events if event["kind"] == "stock_count"]
                    self.assertTrue(counts)
                    self.assertTrue(all(event.get("valuation", {}).get("method") == "moving_weighted_average" for event in counts))
                    self.assertTrue(reports.get("inventory") or reports.get("inventory_value"))


if __name__ == "__main__":
    unittest.main()
