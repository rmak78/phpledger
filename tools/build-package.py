"""Build a reproducible release ZIP from committed, explicitly selected inputs."""
from __future__ import annotations

import argparse
import hashlib
import json
import re
import stat
import subprocess
import sys
import zipfile
from pathlib import Path, PurePosixPath


class PackageError(Exception):
    pass


# The archive unpacks to one folder, like WordPress's "wordpress/"; the file name keeps the version.
PACKAGE_ROOT = "phpledger"
# Files the release must contain so it installs by "unzip and open in the browser".
REQUIRED_FILES = {"LICENSE", "README.txt", "index.php", ".htaccess", "licenses/THIRD-PARTY-NOTICES.md", "www/phpledger/public/.htaccess"}
# Folders the web adapter keeps private; each also gets a deny-all .htaccess as a second layer.
PRIVATE_FOLDERS = ("vendor", "resources", "tools", "licenses", "www/phpledger/includes", "www/phpledger/install", "www/phpledger/templates")
DENY_SOURCE = "resources/release/deny.htaccess"
# Documentation-only files inside dependencies; licence and NOTICE files always stay.
VENDOR_DOCUMENT = re.compile(r"(?:^|/)(?:README|CHANGELOG|CONTRIBUTING|SECURITY|ROADMAP|UPGRADE|UPGRADING|CODE_OF_CONDUCT)[^/]*\.(?:md|rst|txt)$|(?:^|/)[^/]*\.md$"
                             r"|(?:^|/)(?:\.gitattributes|\.gitignore|\.editorconfig|phpdoc\.dist\.xml|phpunit\.xml(?:\.dist)?|phpstan\.neon(?:\.dist)?|psalm\.xml(?:\.dist)?)$"
                             r"|(?:^|/)(?:adr|\.github)/", re.IGNORECASE)
VENDOR_KEEP = re.compile(r"(?:^|/)(?:LICEN[CS]E|COPYING|NOTICE)[^/]*$", re.IGNORECASE)


def vendor_runtime_file(relative: str) -> bool:
    """Keep code and legal notices; drop upstream fixtures, command-line helpers and documentation."""
    if relative.startswith("sergeytsalkov/meekrodb/simpletest/") or relative.startswith("bin/"):
        return False
    return bool(VENDOR_KEEP.search(relative)) or not VENDOR_DOCUMENT.search(relative)


def safe_path(value: str) -> str:
    if not isinstance(value, str) or not re.fullmatch(r"[A-Za-z0-9_.\-/]+", value):
        raise PackageError("Invalid package path")
    path = PurePosixPath(value)
    if path.is_absolute() or any(part in (".", "..") for part in value.split("/")):
        raise PackageError("Package path escapes its root")
    if any(part.lower() in {".git", ".env", ".cache", "storage", "uploads"} for part in path.parts):
        raise PackageError("Private state is excluded from packages")
    if path.name.lower() == "config.local.php" or path.suffix.lower() in {".log", ".sql", ".pem", ".key"}:
        raise PackageError("Private configuration, logs and dumps are excluded")
    return str(path)


def read_file(root: Path, name: str) -> bytes:
    name = safe_path(name)
    current = root
    for part in PurePosixPath(name).parts:
        current = current / part
        if current.is_symlink() or getattr(current, "is_junction", lambda: False)():
            raise PackageError("Symlinks and junctions are excluded")
    if not current.is_file() or not current.resolve().is_relative_to(root.resolve()):
        raise PackageError(f"Missing or unsafe input: {name}")
    data = current.read_bytes()
    if len(data) > 10_000_000:
        raise PackageError(f"Unexpectedly large input: {name}")
    if b"-----BEGIN PRIVATE KEY-----" in data or b"-----BEGIN OPENSSH PRIVATE KEY-----" in data:
        raise PackageError("Private-key material cannot be packaged")
    return data


def committed_file(root: Path, name: str) -> bytes:
    read_file(root, name)  # Validate the local path before accessing its Git object.
    result = subprocess.run(["git", "-C", str(root), "show", f"HEAD:{name}"], capture_output=True)
    if result.returncode:
        raise PackageError(f"Cannot read committed input: {name}")
    return result.stdout


def release_input(root: Path, entry: dict, tracked: set[str]) -> bytes:
    """Read a package input from Git, or an explicitly checksummed local file.

    The public source checkout deliberately ignores local ``docs/``.  Release
    documentation may therefore be supplied from the operator's local release
    inputs, but only when the package specification pins its exact bytes.
    """
    origin = safe_path(entry["source"])
    if origin in tracked:
        data = committed_file(root, origin)
    else:
        expected = entry.get("sha256")
        if not isinstance(expected, str) or not re.fullmatch(r"[0-9a-fA-F]{64}", expected):
            raise PackageError(f"Untracked input requires a SHA-256 pin: {origin}")
        data = read_file(root, origin)
    expected = entry.get("sha256")
    if expected is not None:
        if not isinstance(expected, str) or not re.fullmatch(r"[0-9a-fA-F]{64}", expected):
            raise PackageError(f"Invalid SHA-256 pin: {origin}")
        actual = hashlib.sha256(data).hexdigest()
        if actual.lower() != expected.lower():
            raise PackageError(f"SHA-256 pin does not match local input: {origin}")
    return data


def git(root: Path, *args: str) -> str:
    result = subprocess.run(["git", "-C", str(root), *args], capture_output=True, text=True)
    if result.returncode:
        raise PackageError("Cannot verify source revision")
    return result.stdout.strip()


def release_identity(version: str, channel: str | None = None) -> dict[str, str]:
    """Channels follow the version, never an operator-supplied stability claim."""
    number = r"(?:0|[1-9][0-9]*)"
    identifier = r"(?:0|[1-9][0-9]*|[0-9]*[a-z-][0-9a-z-]*)"
    if not isinstance(version, str) or not re.fullmatch(
        rf"{number}\.{number}\.{number}(?:-{identifier}(?:\.{identifier})*)?", version
    ):
        raise PackageError("Use a semantic version such as 0.7.0-preview, 1.0.0-rc.1 or 1.0.0")
    expected = "preview" if "-" in version else "stable"
    if channel is not None and channel != expected:
        raise PackageError("Release channel does not match the version")
    status = "stable" if expected == "stable" else "development-preview"
    if re.search(r"-rc(?:\.|$)", version):
        status = "release-candidate"
    return {"version": version, "channel": expected, "status": status}


def gather(source: Path, vendor: Path, specification: dict, version: str, channel: str | None = None) -> tuple[dict[str, bytes], str]:
    identity = release_identity(version, channel)
    if not specification.get("project_license"):
        raise PackageError("Record the owner-approved project licence before building")
    if git(source, "status", "--porcelain", "--untracked-files=normal"):
        raise PackageError("Source checkout must be clean; do not package a working tree")
    revision = git(source, "rev-parse", "HEAD")
    tracked = set(git(source, "ls-files").splitlines())
    payload: dict[str, bytes] = {}
    for entry in specification["files"]:
        origin, destination = safe_path(entry["source"]), safe_path(entry["destination"])
        if origin not in tracked:
            if "sha256" not in entry:
                raise PackageError(f"Input is not in the recorded source revision and has no SHA-256 pin: {origin}")
        if destination in payload or destination.startswith("vendor/"):
            raise PackageError("Duplicate or reserved package destination")
        data = release_input(source, entry, tracked)
        if destination in {"README.txt", "README.md", "INSTALL.md", "UPGRADE.md", "RELEASE-NOTES.md"}:
            data = data.replace(b"{{VERSION}}", version.encode()).replace(b"{{SOURCE_COMMIT}}", revision.encode())
            if b"{{" in data:
                raise PackageError("Unresolved operator-document placeholder")
        payload[destination] = data
    if not REQUIRED_FILES.issubset(payload):
        raise PackageError("Required entry points, instructions or notices are missing: " + ", ".join(sorted(REQUIRED_FILES - set(payload))))
    # The lockfile verifies the bundled dependencies; the package itself does not need Composer.
    lock_bytes = committed_file(source, "composer.lock")
    lock = json.loads(lock_bytes)
    installed = json.loads(read_file(vendor, "composer/installed.json"))
    if not isinstance(installed, dict) or installed.get("dev") is not False or installed.get("dev-package-names"):
        raise PackageError("Generate production-only vendor from the lockfile")
    expected = {p["name"]: (p["version"], p.get("source", {}).get("reference")) for p in lock["packages"]}
    actual = {p["name"]: (p["version"], p.get("source", {}).get("reference")) for p in installed["packages"]}
    if expected != actual:
        raise PackageError("Production dependencies do not match the lockfile")
    for path in sorted(vendor.rglob("*")):
        if path.is_symlink() or getattr(path, "is_junction", lambda: False)():
            raise PackageError("Vendor contains a symlink or junction")
        if path.is_file():
            relative = path.relative_to(vendor).as_posix()
            # Upstream ships development fixtures (including an SQL dump), command-line
            # helpers and documentation; the runtime needs only code and licence notices.
            if not vendor_runtime_file(relative):
                continue
            payload["vendor/" + safe_path(relative)] = read_file(vendor, relative)
    if "vendor/autoload.php" not in payload:
        raise PackageError("Production autoload is missing")
    deny = committed_file(source, DENY_SOURCE)
    for folder in PRIVATE_FOLDERS:
        if any(name.startswith(folder + "/") for name in payload):
            payload.setdefault(folder + "/.htaccess", deny)
    if sum(map(len, payload.values())) > 100_000_000:
        raise PackageError("Unexpectedly large package")
    manifest = {
        **identity, "source_commit": revision,
        "project_license": specification["project_license"],
        "composer_lock_sha256": hashlib.sha256(lock_bytes).hexdigest(),
        "files": [{"path": name, "bytes": len(data), "sha256": hashlib.sha256(data).hexdigest()} for name, data in sorted(payload.items())],
    }
    payload["PACKAGE-MANIFEST.json"] = (json.dumps(manifest, indent=2, sort_keys=True) + "\n").encode()
    return payload, revision


def build(source: Path, vendor: Path, specification: dict, output: Path, version: str, channel: str | None = None) -> Path:
    payload, _ = gather(source.resolve(), vendor.resolve(), specification, version, channel)
    output.mkdir(parents=True, exist_ok=True)
    archive = output / f"phpledger-{version}.zip"
    checksum = output / (archive.name + ".sha256")
    if archive.exists() or checksum.exists():
        raise PackageError("Output already exists; choose an empty output directory")
    # Exclusive creation never overwrites an existing artifact. Stable ZIP metadata
    # makes repeat builds comparable; the source commit is inside the manifest.
    with archive.open("xb") as stream:
        with zipfile.ZipFile(stream, "w", compression=zipfile.ZIP_DEFLATED, compresslevel=9) as result:
            for name, data in sorted(payload.items()):
                info = zipfile.ZipInfo(f"{PACKAGE_ROOT}/{name}", date_time=(1980, 1, 1, 0, 0, 0))
                info.create_system = 3
                info.external_attr = (stat.S_IFREG | 0o644) << 16
                info.compress_type = zipfile.ZIP_DEFLATED
                result.writestr(info, data, compresslevel=9)
    with zipfile.ZipFile(archive) as result:
        if result.testzip() is not None:
            raise PackageError("Archive integrity check failed; do not distribute it")
        for name, data in payload.items():
            if result.read(f"{PACKAGE_ROOT}/{name}") != data:
                raise PackageError("Archive content differs from inventory")
    digest = hashlib.sha256(archive.read_bytes()).hexdigest()
    with checksum.open("x", encoding="ascii", newline="\n") as stream:
        stream.write(f"{digest}  {archive.name}\n")
    return archive


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--source", required=True, type=Path)
    parser.add_argument("--vendor", required=True, type=Path, help="Clean production Composer vendor, installed from this source lockfile")
    parser.add_argument("--specification", type=Path, default=Path(__file__).with_name("package-files.json"))
    parser.add_argument("--output", required=True, type=Path)
    parser.add_argument("--version", default="0.1.0-preview")
    parser.add_argument("--channel", choices=("stable", "preview"), help="Optional assertion; must match the version")
    args = parser.parse_args()
    try:
        spec = json.loads(args.specification.read_text(encoding="utf-8"))
        print(build(args.source, args.vendor, spec, args.output, args.version, args.channel))
        return 0
    except (PackageError, OSError, ValueError, KeyError) as error:
        print(f"Package not built: {error}", file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())
