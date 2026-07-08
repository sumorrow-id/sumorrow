# Sumorrow (Summit Tomorrow) 🏔️

Sumorrow is a one-stop digital platform for mountain hikers in Indonesia. The name reflects optimism and preparation for reaching the summit in the future.

Today, hiking information in Indonesia is scattered across different platforms and not integrated into a complete system. Hikers often struggle to access important details such as real-time weather conditions, mountain status, and well-structured trip documentation. Sumorrow is not only a mountain information directory, but also a personal assistant for hikers — integrating real-time weather data from OpenWeatherMap and mountain activity status from relevant authorities (such as PVMBG) to improve hiking safety.

## Features

- **Mountain directory (Explore)** — browse mountains across Indonesian provinces with elevation, difficulty, official basecamps, images, and user ratings.
- **Real-time weather** — current conditions and forecast per mountain via OpenWeatherMap.
- **Community & forum** — create/join communities (public or private), post to the forum, comment, like, and organize community events.
- **Hiking log / profile posts** — document trips on your personal profile.
- **Digital gear list** — manage hiking equipment per user.
- **Authentication** — email/password registration with email verification, plus Google OAuth (Socialite). Unverified users cannot post.
- **Admin panel** — dashboard, user role management, mountain catalog CRUD, and user-activity export.
- **REST API** — versioned read-only catalog API (`/api/v1`) secured with Laravel Sanctum, with built-in docs at `/api/docs`.

## Target users

- **Beginner hikers** — reliable information on mountain difficulty, estimated hiking time, official routes, and gear recommendations for safer, better-planned first hikes.
- **Experienced hikers** — trip documentation (Hiking Log), milestones, and technical tools to manage backpack load.
- **Community & contributors** — hikers who share trail-condition updates, write reviews, and contribute new mountain data.

## Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.4, Laravel 13 |
| Auth | Laravel Sanctum (API tokens), Laravel Socialite (Google OAuth), session auth (web) |
| Frontend | Server-rendered Blade, Vite, Tailwind CSS 4 |
| Database | MySQL 8 |
| Testing | PHPUnit 12 (`sumorrow_test` database) |
| Formatting | Laravel Pint (PHP), Prettier + `prettier-plugin-blade` (Blade/JS) |
| CI | GitHub Actions — `php artisan test` against MySQL 8 on push/PR to `main` and `develop` |

## Getting started

### Prerequisites

- PHP 8.4 with Composer
- Node.js + npm
- MySQL 8 (local dev assumes XAMPP on port 3306)

### Setup

1. Clone the repository and create the database:

   ```sql
   CREATE DATABASE sumorrow;
   CREATE DATABASE sumorrow_test; -- for running tests
   ```

2. Bootstrap everything in one shot:

   ```bash
   composer run setup
   ```

   This installs Composer/npm dependencies, copies `.env`, generates the app key, runs migrations, and builds frontend assets. For a step-by-step walkthrough see [docs/local-setup.md](docs/local-setup.md).

3. Fill in the API keys in `.env` as needed:

   ```dotenv
   GOOGLE_CLIENT_ID=        # Google OAuth login
   GOOGLE_CLIENT_SECRET=
   OPENWEATHERMAP_API_KEY=  # weather features
   GIPHY_API_KEY=           # forum GIFs
   ```

4. Seed the mountain catalog:

   ```bash
   php artisan db:seed
   ```

   Catalog data lives in `database/data/mountains_seeder.json` (single source of truth, idempotent upsert) — see [docs/seeding.md](docs/seeding.md).

5. Run the dev environment (server + queue worker + Vite HMR):

   ```bash
   composer run dev
   ```

### Common commands

| Command | Purpose |
|---|---|
| `composer run dev` | Dev server, queue listener, and Vite concurrently |
| `composer run test` | Clear config + run the full test suite |
| `php artisan test --filter=TestName` | Run a single test |
| `php artisan migrate` | Run after every pull that includes migrations |
| `php artisan migrate:fresh --seed` | Wipe and reseed from the catalog JSON |
| `./vendor/bin/pint` | Format PHP code |
| `npm run build` | Production frontend build |

## API

All endpoints live under `/api/v1`, require a Sanctum token, and are rate-limited (30/min per user, 10/min per IP for guests). Errors always return a JSON `{ message, status, ... }` envelope.

- `POST /api/v1/login` — issue a personal access token
- `GET /api/v1/provinces`, `/mountains`, `/basecamps`, images, ratings — read-only catalog

Interactive docs are served at `/api/docs`; the full contract is in [docs/architecture/api-endpoint/README.md](docs/architecture/api-endpoint/README.md).

## Project structure highlights

```
app/Http/Controllers/        # Web controllers (Community, Explore, Gear, Weather, Profile, ...)
app/Http/Controllers/Api/V1/ # Thin API controllers returning Resources
app/Http/Resources/          # All API JSON shapes
app/Services/                # e.g. GoogleAuthService (bound to SocialAuthInterface)
routes/web.php               # Blade UI routes (session auth)
routes/api.php               # /api/v1 routes (Sanctum)
resources/views/             # Blade views by feature (auth/, admin/, community/, profile/, ...)
database/data/               # mountains_seeder.json — catalog source of truth
docs/                        # setup, seeding, conventions, architecture
```

## Documentation

1. [Local setup & migration guide](docs/local-setup.md)
2. [Seeding guide](docs/seeding.md)
3. [Commit convention](docs/commit-convention.md)
4. [ERD (specification)](docs/architecture/erd/README.md) · [ERD (visual)](docs/architecture/erd/mountain_api_erd_v0.html)
5. [API endpoint structure](docs/architecture/api-endpoint/README.md)
6. [Changelog](docs/CHANGELOG.md)

## Contributing

- Commit messages follow `<type>(<scope>): <message>` — types: `feat`, `fix`, `refactor`, `chore`, `docs`, `build`, `test`. See [docs/commit-convention.md](docs/commit-convention.md).
- Never modify a shared migration; add a new one.
- Run `composer run test` and `./vendor/bin/pint` before opening a PR — CI runs the test suite against MySQL 8.