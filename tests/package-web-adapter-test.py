"""Serve a built release ZIP inside a website folder with Apache and check its web adapter.

Usage: python tests/package-web-adapter-test.py --zip <phpledger-VERSION.zip> [--image phpledger-test:latest]

The package is copied into a disposable container (no host folder is served) at /srv/site/sub,
the way an owner uploads it to public_html/sub or XAMPP's htdocs/sub. Three virtual hosts check:
  :80   .htaccess allowed (shared hosting / XAMPP): routing, public files, private-path refusal;
  :8081 .htaccess ignored: the package-root index.php must refuse to start;
  :8082 document root at www/phpledger/public: the most secure layout still works.
Requests run inside the container, so the installer sees a same-computer HTTP visitor.
"""
from __future__ import annotations

import argparse
import json
import pathlib
import subprocess
import sys
import tempfile
import time
import uuid
import zipfile

VHOSTS = """Listen 8081
Listen 8082
<VirtualHost *:80>
    DocumentRoot /srv/site
    <Directory /srv/site>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
<VirtualHost *:8081>
    DocumentRoot /srv/site
    <Directory /srv/site>
        AllowOverride None
        Require all granted
    </Directory>
</VirtualHost>
<VirtualHost *:8082>
    DocumentRoot /srv/site/sub/www/phpledger/public
    <Directory /srv/site/sub/www/phpledger/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
"""

# Runs inside the container: fetch each URL without following redirects and report what came back.
FETCH = r"""<?php
$results = [];
foreach (json_decode($argv[1], true) as $url) {
    $curl = curl_init($url);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 20]);
    $raw = (string) curl_exec($curl);
    $size = (int) curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $results[$url] = ['status' => (int) curl_getinfo($curl, CURLINFO_HTTP_CODE), 'headers' => substr($raw, 0, $size), 'body' => substr(substr($raw, $size), 0, 4000)];
    curl_close($curl);
}
echo json_encode($results);
"""


def docker(*args: str, check: bool = True) -> subprocess.CompletedProcess:
    return subprocess.run(["docker", *args], capture_output=True, text=True, check=check)


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--zip", required=True, type=pathlib.Path)
    parser.add_argument("--image", default="phpledger-test:latest")
    options = parser.parse_args()
    name = "pl-adapter-" + uuid.uuid4().hex[:10]
    checks = 0
    failures: list[str] = []

    def expect(condition: bool, message: str) -> None:
        nonlocal checks
        checks += 1
        if not condition:
            failures.append(message)

    with tempfile.TemporaryDirectory(prefix="pl-adapter-") as temporary:
        root = pathlib.Path(temporary)
        with zipfile.ZipFile(options.zip) as archive:
            tops = {member.split("/", 1)[0] for member in archive.namelist()}
            expect(tops == {"phpledger"}, f"archive root is {sorted(tops)}, expected only phpledger/")
            archive.extractall(root / "site")
        (root / "site" / "phpledger").rename(root / "site" / "sub")
        (root / "vhosts.conf").write_text(VHOSTS, encoding="utf-8", newline="\n")
        (root / "fetch.php").write_text(FETCH, encoding="utf-8", newline="\n")
        docker("create", "--name", name, options.image, "apache2-foreground")
        try:
            docker("cp", str(root / "site"), f"{name}:/srv/site")
            docker("cp", str(root / "vhosts.conf"), f"{name}:/etc/apache2/sites-available/000-default.conf")
            docker("cp", str(root / "fetch.php"), f"{name}:/srv/fetch.php")
            docker("start", name)
            docker("exec", name, "chown", "-R", "www-data:www-data", "/srv/site/sub/www/phpledger")
            deadline = time.time() + 30
            while docker("exec", name, "php", "-r", "exit(@fsockopen('127.0.0.1', 80) ? 0 : 1);", check=False).returncode and time.time() < deadline:
                time.sleep(0.5)

            def fetch(*urls: str) -> dict:
                result = docker("exec", name, "php", "/srv/fetch.php", json.dumps(list(urls)))
                return json.loads(result.stdout)

            site = "http://localhost/sub"
            r = fetch(site + "/", site + "/install", site + "/assets/app.css", site + "/assets/missing.css",
                      site + "/www/phpledger/public/index.php", site + "/maintenance.php")
            expect(r[site + "/"]["status"] == 303 and "Location: /sub/install" in r[site + "/"]["headers"], "fresh upload did not redirect to /sub/install")
            installer = r[site + "/install"]
            expect(installer["status"] == 200 and "Connect your database" in installer["body"], f"installer did not open without a key: {installer['status']}")
            expect("Local test on this computer" in installer["body"], "same-computer HTTP was not labelled")
            expect("could not confirm" not in installer["body"], "the private-folder check was inconclusive behind Apache")
            expect(r[site + "/assets/app.css"]["status"] == 200 and "text/css" in r[site + "/assets/app.css"]["headers"], "public stylesheet not served")
            expect(r[site + "/assets/missing.css"]["status"] == 404, "a missing asset did not return 404")
            direct = r[site + "/www/phpledger/public/index.php"]
            expect(direct["status"] in (302, 403), f"direct public entry was not sent back to the folder: {direct['status']}")
            maintenance = r[site + "/maintenance.php"]
            expect(maintenance["status"] != 404 and "nstallation" in maintenance["body"], f"maintenance entry point unreachable ({maintenance['status']})")

            docker("exec", name, "sh", "-c", "echo sample-private-marker > /srv/site/sub/www/phpledger/storage/installation/setup-code.txt")
            private = ["/vendor/autoload.php", "/vendor/composer/installed.json", "/www/phpledger/includes/config.local.example.php",
                       "/www/phpledger/includes/bootstrap.php", "/www/phpledger/install/migrate.php", "/www/phpledger/templates/views/install.php",
                       "/www/phpledger/storage/installation/setup-code.txt", "/www/phpledger/storage/installation/setup.json",
                       "/resources/modules/core.json", "/tools/resume-update.php", "/licenses/THIRD-PARTY-NOTICES.md",
                       "/PACKAGE-MANIFEST.json", "/README.txt", "/LICENSE", "/.htaccess", "/vendor/.htaccess"]
            r = fetch(*(site + path for path in private))
            for path in private:
                response = r[site + path]
                expect(response["status"] in (403, 404) and "sample-private-marker" not in response["body"], f"{path} was served ({response['status']})")

            r = fetch("http://localhost:8081/sub/", "http://localhost:8081/sub/index.php")
            for url, response in r.items():
                expect(response["status"] == 503 and "is not using the .htaccess file" in response["body"], f"{url} started without .htaccess rules ({response['status']})")

            r = fetch("http://localhost:8082/", "http://localhost:8082/install", "http://localhost:8082/assets/app.css")
            expect(r["http://localhost:8082/"]["status"] == 303 and "Location: /install" in r["http://localhost:8082/"]["headers"], "document-root layout did not redirect to /install")
            expect(r["http://localhost:8082/install"]["status"] == 200 and "Connect your database" in r["http://localhost:8082/install"]["body"], "document-root layout installer failed")
            expect(r["http://localhost:8082/assets/app.css"]["status"] == 200, "document-root layout assets failed")
        finally:
            docker("rm", "-f", name, check=False)
    for failure in failures:
        print("FAIL " + failure)
    print(f"Package web adapter: {checks - len(failures)} of {checks} checks passed.")
    return 1 if failures else 0


if __name__ == "__main__":
    sys.exit(main())
