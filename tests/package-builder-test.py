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
        (self.vendor / "autoload.php").write_text("<?php // sample loader", encoding="utf-8")
        (self.vendor / "composer/installed.json").write_text(json.dumps({"dev": False, "dev-package-names": [], "packages": [self.package]}), encoding="utf-8")
        files = {"composer.json": "{}", "composer.lock": json.dumps({"packages": [self.package]})}
        files.update({name: "Sample test fixture {{VERSION}} {{SOURCE_COMMIT}}" for name in ("README.md", "INSTALL.md", "UPGRADE.md", "RELEASE-NOTES.md", "LICENSE", "THIRD-PARTY-NOTICES.md")})
        for name, content in files.items():
            (self.source / name).write_text(content, encoding="utf-8")
        self.policy = {"project_license": "MIT", "files": [{"source": name, "destination": name} for name in files]}
        self.git("init", "-q")
        self.git("config", "user.name", "Package fixture")
        self.git("config", "user.email", "fixture@example.invalid")
        self.git("add", ".")
        self.git("commit", "-qm", "Sample package fixture")

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

    def test_current_runtime_services_views_and_migrations_are_in_the_allowlist(self):
        policy = json.loads((ROOT / "tools/package-files.json").read_text(encoding="utf-8"))
        sources = {entry["source"] for entry in policy["files"]}
        for folder in ("www/phpledger/includes/functions", "www/phpledger/templates/views", "www/phpledger/install/migrations"):
            for path in (ROOT / folder).glob("*.php"):
                source = path.relative_to(ROOT).as_posix()
                self.assertTrue(source in sources, "Runtime dependency omitted from package: " + source)
        for source in sources:
            self.assertTrue((ROOT / source).is_file(), "Missing allowlisted source: " + source)

    def test_dirty_source_is_rejected_before_output(self):
        (self.source / "README.md").write_text("changed", encoding="utf-8")
        with self.assertRaisesRegex(builder.PackageError, "clean"):
            self.build()
        self.assertFalse((self.root / "one").exists())

    def test_stable_and_candidate_channels_are_version_bound(self):
        for version, channel, status in (("1.0.0", "stable", "stable"), ("1.0.0-rc.1", "preview", "release-candidate"), ("0.9.0-beta", "preview", "development-preview")):
            with self.subTest(version=version):
                archive = builder.build(self.source, self.vendor, self.policy, self.root / version, version, channel)
                with zipfile.ZipFile(archive) as package:
                    manifest = json.loads(package.read(f"phpledger-{version}/PACKAGE-MANIFEST.json"))
                    self.assertEqual(channel, manifest["channel"])
                    self.assertEqual(status, manifest["status"])
        for version, channel in (("1.0.0-rc.1", "stable"), ("1.0.0", "preview")):
            with self.assertRaisesRegex(builder.PackageError, "channel"):
                builder.release_identity(version, channel)

    def test_ambiguous_or_unsafe_release_versions_are_rejected(self):
        for version in ("1.0", "01.0.0", "1.0.0-rc.01", "1.0.0-", "1.0.0-rc..1", "1.0.0/escape", "1.0.0+untracked"):
            with self.subTest(version=version), self.assertRaises(builder.PackageError):
                builder.release_identity(version)

    def test_untracked_release_input_requires_and_checks_sha256(self):
        local_doc = self.source / "docs/local-release.md"
        local_doc.parent.mkdir()
        local_doc.write_text("Sample local release input", encoding="utf-8")
        (self.source / ".git/info/exclude").write_text("/docs/local-release.md\n", encoding="utf-8")
        self.policy["files"].append({"source": "docs/local-release.md", "destination": "docs/local-release.md"})
        with self.assertRaisesRegex(builder.PackageError, "SHA-256 pin"):
            self.build()
        import hashlib
        self.policy["files"][-1]["sha256"] = hashlib.sha256(local_doc.read_bytes()).hexdigest()
        # The ignored/untracked file is accepted only after its exact bytes are pinned.
        self.assertTrue(self.build().is_file())

    def test_upstream_test_dump_is_excluded_but_runtime_remains(self):
        library = self.vendor / "sergeytsalkov/meekrodb"
        (library / "simpletest").mkdir(parents=True)
        (library / "simpletest/statements.sql").write_text("Sample upstream fixture")
        (library / "db.class.php").write_text("<?php // sample library")
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
