"""Isolated package safety and reproducibility tests; no network or real data."""
import importlib.util
import json
import subprocess
import tempfile
import unittest
import zipfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
spec = importlib.util.spec_from_file_location("package_builder", ROOT / "tools/build-package.py")
builder = importlib.util.module_from_spec(spec)
spec.loader.exec_module(builder)


class PackageTests(unittest.TestCase):
    def setUp(self):
        self.base = ROOT / ".cache/package-tests"
        self.base.mkdir(parents=True, exist_ok=True)
        self.temporary = tempfile.TemporaryDirectory(dir=self.base)
        self.root = Path(self.temporary.name)
        self.source = self.root / "source"
        self.vendor = self.root / "vendor"
        self.source.mkdir()
        (self.vendor / "composer").mkdir(parents=True)
        self.package = {"name": "example/library", "version": "1.0.0", "source": {"reference": "fixture"}}
        (self.vendor / "autoload.php").write_text("<?php // synthetic loader", encoding="utf-8")
        (self.vendor / "composer/installed.json").write_text(json.dumps({"dev": False, "dev-package-names": [], "packages": [self.package]}), encoding="utf-8")
        files = {"composer.json": "{}", "composer.lock": json.dumps({"packages": [self.package]})}
        files.update({name: "Synthetic test fixture {{VERSION}} {{SOURCE_COMMIT}}" for name in ("README.md", "INSTALL.md", "UPGRADE.md", "RELEASE-NOTES.md", "LICENSE", "THIRD-PARTY-NOTICES.md")})
        for name, content in files.items():
            (self.source / name).write_text(content, encoding="utf-8")
        self.policy = {"project_license": "MIT", "files": [{"source": name, "destination": name} for name in files]}
        self.git("init", "-q")
        self.git("config", "user.name", "Package fixture")
        self.git("config", "user.email", "fixture@example.invalid")
        self.git("add", ".")
        self.git("commit", "-qm", "Synthetic package fixture")

    def tearDown(self):
        if not self.root.resolve().is_relative_to(self.base.resolve()):
            raise AssertionError("Refusing cleanup outside the isolated test workspace")
        self.temporary.cleanup()

    def git(self, *args):
        subprocess.run(["git", "-C", str(self.source), *args], check=True, capture_output=True)

    def build(self, output="one"):
        return builder.build(self.source, self.vendor, self.policy, self.root / output, "0.1.0-preview")

    def test_repeat_builds_are_identical_and_manifest_covers_files(self):
        first, second = self.build(), self.build("two")
        self.assertEqual(first.read_bytes(), second.read_bytes())
        with zipfile.ZipFile(first) as archive:
            prefix = "phpledger-0.1.0-preview/"
            manifest = json.loads(archive.read(prefix + "PACKAGE-MANIFEST.json"))
            self.assertEqual(len(archive.namelist()), len(manifest["files"]) + 1)
            for item in manifest["files"]:
                import hashlib
                self.assertEqual(hashlib.sha256(archive.read(prefix + item["path"])).hexdigest(), item["sha256"])
        with self.assertRaises(builder.PackageError):
            self.build()

    def test_dirty_source_is_rejected_before_output(self):
        (self.source / "README.md").write_text("changed", encoding="utf-8")
        with self.assertRaisesRegex(builder.PackageError, "clean"):
            self.build()
        self.assertFalse((self.root / "one").exists())

    def test_upstream_test_dump_is_excluded_but_runtime_remains(self):
        library = self.vendor / "sergeytsalkov/meekrodb"
        (library / "simpletest").mkdir(parents=True)
        (library / "simpletest/statements.sql").write_text("Synthetic upstream fixture")
        (library / "db.class.php").write_text("<?php // synthetic library")
        with zipfile.ZipFile(self.build()) as archive:
            names = archive.namelist()
            self.assertFalse(any(name.endswith("statements.sql") for name in names))
            self.assertTrue(any(name.endswith("db.class.php") for name in names))

    def test_unapproved_licence_and_development_vendor_are_rejected(self):
        self.policy["project_license"] = None
        with self.assertRaisesRegex(builder.PackageError, "licence"):
            self.build()
        self.policy["project_license"] = "MIT"
        (self.vendor / "composer/installed.json").write_text(json.dumps({"dev": True, "packages": [self.package]}), encoding="utf-8")
        with self.assertRaisesRegex(builder.PackageError, "production-only"):
            self.build()

    def test_private_and_escaping_destinations_are_rejected(self):
        for name in ("../escape", "/absolute", "a/../../escape", ".env", "config.local.php", "storage/data", "database.sql"):
            with self.subTest(name=name):
                self.policy["files"][0]["destination"] = name
                with self.assertRaises(builder.PackageError):
                    self.build()


if __name__ == "__main__":
    unittest.main()
