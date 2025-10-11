# Deploy IntelliTask

This guide helps you push to GitHub and deploy the Laravel app.

## 1) Prepare the repository

- Ensure `.env.example` exists (it does). Never commit `.env`.
- Ensure `APP_KEY` will be set in production.

## 2) Initialize Git and push to GitHub

1. Create a new GitHub repository (no README/license; we have one).
2. Add the remote and push:

```powershell
# From the project root
git add .
git commit -m "Initial commit: IntelliTask"
# Replace the URL with your repo
git remote add origin https://github.com/<your-username>/IntelliTask.git
git branch -M main
git push -u origin main
```

## 3) Choose a deployment option

Pick one of the following.

### Option A: Render (Docker)

- This repo contains a `Dockerfile` and `render.yaml`.
- Create a PostgreSQL instance on Render (optional but recommended).
- Create a new Web Service from Blueprint and point to your GitHub repo.
- In Render dashboard, set environment variables:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_KEY` (generate locally: `php artisan key:generate --show`)
  - Database variables (if using Postgres). Set `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.
- Add a post-deploy hook to run migrations (Render settings → Deploy hooks) or run manually:
  - `php artisan migrate --force`
- Build will run asset compilation in Docker. Ensure `vite.config.js` builds to `public/build`.

### Option B: Railway (Nixpacks)

- Create a new project from your GitHub repo.
- Set environment variables as above.
- Add a Railway PostgreSQL plugin and map env vars.
- Set start command to `nginx` via Dockerfile (provided). Railway will use the Dockerfile.

### Option C: Traditional VPS / cPanel

- Point the document root to `public/`.
- Ensure PHP 8.2+, Composer installed.
- Run:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
php artisan migrate --force
npm ci && npm run build
php artisan storage:link
```

## Notes

- SQLite in `database/database.sqlite` is OK for local dev. For production, use MySQL or Postgres.
- Storage and cache directories must be writable by the web server.
