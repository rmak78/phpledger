"""Generated candidate-contract invariants; no database or network access."""
import importlib.util
import unittest
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location("demo_pack_builder", ROOT / "tools/build-demo-packs.py")
builder = importlib.util.module_from_spec(spec)
spec.loader.exec_module(builder)


class CandidateTests(unittest.TestCase):
    candidates = ("service-agency", "seasonal-business", "jewelry-studio", "light-manufacturing")

    def contract(self, slug):
        evidence = builder.generated_candidate_material(slug)
        return {"events": evidence["operational_event_contract"]}

    def test_generated_candidates_are_balanced_and_identity_safe(self):
        for slug in self.candidates:
            with self.subTest(slug=slug):
                evidence = builder.generated_candidate_material(slug)
                self.assertGreaterEqual(len(evidence["operational_event_contract"]), 8)
                builder.validate_generated_candidate(slug, self.contract(slug))
                self.assertTrue(evidence["industry_profile"]["accounts"])
                self.assertTrue(evidence["industry_profile"]["research_basis"])

    def test_expected_report_projections_keep_credit_and_stock_results_explicit(self):
        jewelry = builder.generated_candidate_material("jewelry-studio")
        jewelry_reports = builder.candidate_expected_reports(self.contract("jewelry-studio"))
        self.assertEqual(jewelry_reports["annual"]["2026"]["income"], "1080.0000")
        self.assertEqual(jewelry_reports["annual"]["2026"]["receivable_balance"], "0.0000")
        self.assertEqual(jewelry_reports["inventory"]["silver-pendant|studio"]["quantity"], "2.0000")
        self.assertEqual(len(jewelry["operational_event_contract"]), 13)
        seasonal_reports = builder.candidate_expected_reports(self.contract("seasonal-business"))
        self.assertEqual(seasonal_reports["monthly"]["2026-08"]["receivable_balance"], "825.0000")

    def test_duplicate_event_identity_is_rejected(self):
        contract = self.contract("service-agency")
        contract["events"][1]["id"] = contract["events"][0]["id"]
        with self.assertRaisesRegex(ValueError, "IDs"):
            builder.validate_generated_candidate("service-agency", contract)

    def test_unbalanced_expected_journal_is_rejected(self):
        contract = self.contract("seasonal-business")
        contract["events"][0]["expected_journal"][0]["debit"] = "2201.0000"
        with self.assertRaisesRegex(ValueError, "balanced"):
            builder.validate_generated_candidate("seasonal-business", contract)

    def test_invalid_stock_movement_is_rejected(self):
        contract = self.contract("jewelry-studio")
        contract["events"][0]["stock_movements"][0]["quantity_delta"] = "0.0000"
        with self.assertRaisesRegex(ValueError, "stock movement"):
            builder.validate_generated_candidate("jewelry-studio", contract)


if __name__ == "__main__":
    unittest.main()
