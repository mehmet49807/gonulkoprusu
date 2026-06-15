# gonulkoprusu (Gönül Köprüsü)

Topluluk yardımlaşma platformu. Kullanıcılar şikayet (complaint) oluşturur, yönetici (admin) bunları
sonuçlandırır ve sonuç **kullanıcıya bildirim (notification) olarak** iletilir.

## Stack & layout

- `server/` — Express + `better-sqlite3` REST API (port `4000`). Entry: `server/src/index.js`, schema/seed: `server/src/db.js`.
- `client/` — React + Vite SPA (port `5173`). Vite proxies `/api` → `http://localhost:4000`.
- npm workspaces; root scripts orchestrate both.

## Commands

- Dev (both servers): `npm run dev` (root). Individually: `npm run dev:server`, `npm run dev:client`.
- Lint: `npm run lint` (root, runs server + client).
- Build (client): `npm run build`.
- Prod server: `npm start`.

## Deployment

- Auto-deploy via GitHub Actions FTP on every `main` push — see `.github/workflows/deploy.yml` and `DEPLOYMENT.md`.
- Frontend builds two targets via `VITE_APP_TARGET` (`public` → `/public_html`, `admin` → `/admin.gonulkoprusu.com`). API base is configurable with `VITE_API_BASE`.
- FTP credentials live only in GitHub Secrets (`FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`), never in the repo.
- The Node/Express+SQLite backend cannot run on plain shared FTP hosting; only the static frontend is FTP-deployed. See `DEPLOYMENT.md` for backend hosting options.

## Cursor Cloud specific instructions

- Direct outbound FTP **data** transfers from the Cursor Cloud VM to `ftp.gonulkoprusu.com` are unreliable (passive-mode data port blocked); FTP login succeeds but uploads time out. Do deployments through the GitHub Actions workflow, not from the VM.

- The update script only runs `npm install`. Start services manually with `npm run dev` (do not add it to the update script).
- `npm run dev` runs the API (`:4000`) and Vite (`:5173`) together via `concurrently`. Open the app at `http://localhost:5173`; the API is not meant to be browsed directly.
- SQLite data lives at `server/data.sqlite` (gitignored, WAL mode). It is auto-created and seeded on first server start with users `admin` (yönetici), `ayse`, `mehmet` (kullanıcı). Login is by username only (no password — demo). To reset state, stop the dev server, delete `server/data.sqlite*`, then restart — the running server holds the file handle, so deleting without restarting will not take effect.
- Core flow to verify the app works: log in as a `user` → create a complaint → log in as `admin` → resolve/reject with a result → the user receives a notification (bell badge) containing the admin's result. The frontend polls every 4s, so notification/status updates appear without a manual refresh.
