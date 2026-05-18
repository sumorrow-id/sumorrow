# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Sumorrow is a Laravel 13 / PHP 8.4 application for Indonesian mountain hikers — directory + personal-assistant features (mountain info, weather, ratings, hiking logs). Frontend is server-rendered Blade with Vite + Tailwind 4. Database is MySQL (local dev assumes XAMPP on port 3306).

## Commands

Setup and development:

- `composer run setup` — one-shot bootstrap (install, copy `.env`, `key:generate`, migrate, npm install, build). For MySQL, create the `sumorrow` database first or follow [docs/local-setup.md](docs/local-setup.md) instead.
- `composer run dev` — runs `php artisan serve`, `php artisan queue:listen`, and `npm run dev` concurrently (the canonical dev command — do not start them individually unless debugging).
- `php artisan migrate` — run after every pull that includes migration files. Never edit migrations that are already shared; add a new one.
- `php artisan migrate:fresh --seed` — wipe + reseed from `database/data/mountains_seeder.json` (see [docs/seeding.md](docs/seeding.md)).
- `php artisan db:seed` — re-run seeder without dropping tables; idempotent via `updateOrCreate`.

Tests and lint:

- `composer run test` — clears config then runs `php artisan test`. Tests use a separate MySQL database `sumorrow_test` (see [phpunit.xml](phpunit.xml)) — create it before first run.
- `php artisan test --filter=TestName` — single test class or method.
- `php artisan test tests/Feature/Api/V1/MountainControllerTest.php` — single file.
- `./vendor/bin/pint` — Laravel Pint formatter (the only PHP linter in use).
- `npm run build` — Vite production build; `npm run dev` for HMR (already included in `composer run dev`).

## Architecture

### Two route surfaces

The app exposes two distinct route trees, both wired in [bootstrap/app.php](bootstrap/app.php):

- **Web** ([routes/web.php](routes/web.php)) — Blade UI, session auth, Google OAuth via Socialite. Public: `/home`, `/explore`. Guest-only: `/login`, `/register`, `/auth/google/*`. Auth-only: `/profile`, email verification, `/admin/*` (additionally gated by `admin` middleware alias).
- **API** ([routes/api.php](routes/api.php)) — versioned under `/api/v1`, read-only mountain catalog (provinces, mountains, basecamps, images, ratings). All endpoints require Sanctum (`auth:sanctum`) and use the `throttle:api` limiter. The only write endpoint is `POST /api/v1/login` which issues a personal access token.

Anything under `/api/*` is forced into JSON responses by exception handlers in [bootstrap/app.php](bootstrap/app.php) — `AuthenticationException`, `ValidationException`, `ModelNotFoundException`, `NotFoundHttpException`, and `ThrottleRequestsException` are all rewritten to a `{ message, status, ... }` envelope. When adding new error paths, preserve this envelope shape; see [docs/architecture/api-endpoint/README.md](docs/architecture/api-endpoint/README.md) for the full contract.

### API layer conventions

- Controllers live in `app/Http/Controllers/Api/V1/` and stay thin — query, filter, paginate, return a Resource. Form requests in `app/Http/Requests/Api/V1/` own validation **and** sort/order/perPage defaults (see `IndexMountainRequest`).
- All API JSON shapes go through `App\Http\Resources\*` — do not return raw model arrays. Conditional includes use `$this->whenLoaded(...)` (see `MountainResource`).
- Rate limiting is configured in `AppServiceProvider::boot()`: 30/min per authenticated user, 10/min per IP for guests. Change the limiter there, not on individual routes.

### Models and IDs

- Most catalog models (Mountain, Province, Basecamp, etc.) use auto-increment integer PKs.
- **`User` uses UUIDs** (`HasUuids`), has `$incrementing = false`, `keyType = 'string'`, and **`UPDATED_AT = null`** (no `updated_at` column). It stores the password in `password_hash` (not `password`) — `getAuthPassword()` is overridden accordingly. There is a `role` column with `admin` as the privileged value, checked by `AdminMiddleware` and login redirects.
- `Mountain::$timestamps = false` — the mountains table has no timestamps either.
- `hasVerifiedEmail()` on `User` is custom: it returns true if either `created_at` is set or `google_id` is present (Google-OAuth users are auto-verified).

### Pluggable social auth

Social login is abstracted behind `App\Contracts\SocialAuthInterface` and bound to `App\Services\GoogleAuthService` in `AppServiceProvider::register()`. `LoginController` depends on the interface, not the concrete service — when adding another provider (e.g. GitHub), implement the contract and rebind, don't edit the controller.

### Frontend

Vite entrypoints are `resources/css/app.css` and `resources/js/app.js` ([vite.config.js](vite.config.js)). Tailwind 4 is loaded via the `@tailwindcss/vite` plugin (not the legacy PostCSS pipeline). Blade views live in `resources/views/` organized by feature (`auth/`, `admin/`, `profile/`, `api/`, plus top-level `home.blade.php` / `explore.blade.php`). Blade is formatted with `prettier-plugin-blade`.

### Seeding

The single source of truth for catalog data is `database/data/mountains_seeder.json`. `MountainSeeder` parses it and uses `updateOrCreate` to upsert Provinces → Mountains → Mountain Images → Basecamps. To add new mountains, edit the JSON and re-run `php artisan db:seed`; do not write one-off seeders for catalog data.

## Conventions

- **Commits**: `<type>(<scope>): <message>` — types are `feat`, `fix`, `refactor`, `chore`, `docs`, `build`, `test`. See [docs/commit-convention.md](docs/commit-convention.md).
- **Migrations**: never modify a shared migration; add a new file. Update `docs/architecture/*` when the schema changes.
- **CI**: [.github/workflows/laravel_ci.yml](.github/workflows/laravel_ci.yml) runs `php artisan test` against MySQL 8 on push/PR to `main` and `develop`. Branches that won't pass `php artisan test` locally will fail CI.
