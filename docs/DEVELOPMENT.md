# Local revival development

These instructions apply to the modern source containing `compose.yaml`, `composer.json` and `www/phpledger`. The published repository preserves the historical application under `legacy/`; it is not this runtime. Use the public [Wiki](https://github.com/rmak78/phpledger/wiki) for visitor documentation and package availability. The [foundation evaluation package](https://github.com/rmak78/phpledger/releases/tag/v0.1.0-preview) includes production dependencies; this page covers development from source.

## Start the verified environment

Use Docker Compose for PHP 8.5 and MySQL 8.4. Preserve any existing `.env`. For a fresh checkout, copy `.env.example` to `.env` and privately set independent random development database passwords.

```powershell
docker compose up -d --build
docker compose exec -T web php www/phpledger/install/migrate.php
```

Open the local sign-in screen at `http://127.0.0.1:18200/login`. Serve only `www/phpledger/public`, never the repository root or `legacy/`. Historical `legacy/install/` dumps are not part of the revival; never run them against the modern database. Use synthetic data and a separate development database.

Create the first administrator through the controlled command. Supply its password through standard input or a private `PL_ADMIN_PASSWORD` variable; never put the password in command arguments or committed files.

```powershell
docker compose exec -T -e PL_ADMIN_PASSWORD web php www/phpledger/install/create-admin.php --email=owner@example.test --name=Owner
```

Clear the private shell variable after use. Sign in, create a business or isolated sample, and follow setup through the first receipt or expense. Existing businesses require reviewed opening balances before posting. Installation, business onboarding and historical-data cutover are separate workflows.

## Verify changes

```powershell
docker compose --profile test run --rm test composer check
docker compose --profile test run --rm test composer validate --no-interaction
docker compose --profile test run --rm test composer audit --no-interaction
./tools/verify-backup-restore.ps1
```

The test profile uses `phpledger_test` in its separate `db_test` service. The restore check creates and removes only its own isolated test database. See [validation receipts](VALIDATION.md) for exact executed checks and remaining limits.

Optional browser-facing acceptance scripts are [core HTTP](../tests/http-smoke.py), [POS HTTP](../tests/pos-http-smoke.py) and [demo HTTP](../tests/demo-http-smoke.py). They use local synthetic records; read each script's scope and required private inputs before running it. Do not broaden their targets to production or real customer books.

## Working boundaries

Read [architecture](ARCHITECTURE.md), [contribution guidance](../CONTRIBUTING.md) and [repository instructions](../AGENTS.md). Reuse the bootstrap, MeekroDB helpers, explicit routes and central posting service. Preserve historical files and migration receipts. Configuration, dependencies and storage remain outside the public document root.

The [demo runbook](DEMO.md) covers separate synthetic storage, restricted runtime permissions, hourly UTC reset and deployment. The [roadmap](ROADMAP.md) preserves future language/formatting/FX, imports, regional accounting, inventory, production POS and industry modules. Development checks are not accounting sign-off, observed usability evidence or a stable-release claim.
