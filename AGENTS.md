# AGENTS.md

## Cursor Cloud specific instructions

### Repository layout

The `main` branch contains only a stub `README.md`. The runnable Laravel 11 application is shipped as `gonulkoprusu-backend-web.zip` on branch `origin/mehmet49807-patch-1`. Extract it once before working:

```bash
git checkout origin/mehmet49807-patch-1 -- gonulkoprusu-backend-web.zip
unzip -qo gonulkoprusu-backend-web.zip
```

App root: `gonulkoprusu-backend-web/` (PHP ^8.2, Laravel 11, Blade frontend, Sanctum API).

### Shared-hosting `public/` layout (required for local dev)

Production deploys copy `vendor`, `bootstrap`, and `storage` under `public/`. The extracted zip does not include those copies. Create symlinks before serving:

```bash
cd gonulkoprusu-backend-web/public
ln -sf ../vendor vendor
ln -sf ../bootstrap bootstrap
ln -sf ../storage storage
```

Without these symlinks, `php artisan serve` returns HTTP 500 (`public/vendor/autoload.php` missing).

### Services

| Service | Required | Start |
|---------|----------|-------|
| MySQL 8+ | Yes | `sudo service mysql start` |
| Laravel web (`php artisan serve`) | Yes | `cd gonulkoprusu-backend-web && php artisan serve --host=0.0.0.0 --port=8000` |
| Admin panel (subdomain routing) | Optional | Same server; use host `adminlogin.localhost:8000` (add `/etc/hosts` entry) |

No Node/npm build step. Queue worker, Redis, FTP, and mail are optional for core flows (`QUEUE_CONNECTION=sync`, `FILESYSTEM_DISK=public` for local uploads).

### Local `.env` essentials

Copy `.env.example` to `.env`, run `php artisan key:generate`, then set at minimum:

- `APP_ENV=local`, `APP_DEBUG=true`, `APP_URL=http://localhost:8000`
- `DB_HOST=127.0.0.1` (use TCP; `localhost` socket may deny access in this VM)
- `DB_PASSWORD=localdev` (or your local MySQL password)
- `FILESYSTEM_DISK=public`, `CACHE_DRIVER=file`, `SESSION_DRIVER=file`

Import schema and demo data:

```bash
sudo mysql gonulkop_wepapp < database/schema.sql
sudo mysql gonulkop_wepapp < database/demo_accounts.sql
```

Demo password for all accounts: `Demo2026!` (see `docs/DEMO_ACCOUNTS.md`).

### Lint / test / run

| Task | Command (from `gonulkoprusu-backend-web/`) |
|------|---------------------------------------------|
| Lint (Pint) | `./vendor/bin/pint --test` |
| Tests | No `tests/` directory in the zip; PHPUnit has nothing to run |
| Dev server | `php artisan serve --host=0.0.0.0 --port=8000` |
| API smoke test | `POST /api/v1/auth/login` with `{"login":"demo_elif","password":"Demo2026!"}` |

Pint may report CRLF line-ending diffs (Windows-origin zip); that is expected and does not block running the app.

### API vs web auth

- Web UI: session cookies via `/login` form (`login` + `password` fields).
- Mobile/API: Bearer token from `POST /api/v1/auth/login` with JSON `login` (email or username) + `password`.
