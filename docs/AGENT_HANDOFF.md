# Agent Handoff Log

Running log of work done by developers and AI agents, newest first.

- **Read this at the start of a session** to learn what changed recently. A
  `SessionStart` hook also injects this file automatically.
- **Append a new entry before you end a session** so the next person or agent has
  context. Keep entries short: date, branch, key changes, and how to verify.
- Do **not** put exploit-level security detail here. Summarize sensitive fixes
  abstractly (see [CHANGELOG.md](CHANGELOG.md)).

---

## 2026-07-26 — branch: `main` — Forum Leaders: exclude 0-contribution users

By: Claude Code

**What changed**

- `PostController::index`: Forum Leaders query now has
  `->having('posts_count', '>', 0)`, so users with no global forum posts no
  longer fill the board (previously `limit(5)` returned 5 users regardless of
  count — leftover demo accounts with 0 posts were showing up).
- Test: `test_forum_leaders_excludes_users_with_zero_contributions`.

**Operational note (VM)**

- Leftover dummy accounts from the old `ForumPostSeeder`
  (`raka.dewa@ / sinta.puspita@ / bima.senja@example.com`) still exist on the
  VM. They have 0 posts now, so the code fix hides them from the board. To
  delete the accounts themselves, reassign any communities they created to an
  admin first (`communities.created_by` is `cascadeOnDelete`), then delete the
  users (their posts cascade away). See the deletion snippet handed to the
  owner; back up the DB first.

**How to verify**

- `php artisan test --compact tests/Feature/PostControllerTest.php` (passing).

## 2026-07-26 — branch: `main` — Explore: Nearby Mountains + Others sections (geolocation)

By: Claude Code

**What changed**

- New explore layout: after the search bar, a **Nearby Mountains** section
  (mountains within **100 km** of the visitor, closest first, with a distance
  badge) and an **Others** section for everything else.
- `Mountain` model: added `coordinatesToDecimal()` (parses the stored DMS
  string, same grammar as WeatherController/CoordinatesPicker) and
  `distanceKmFrom($lat, $lng)` (haversine). Returns null for unparseable
  coordinates, which drop to "Others".
- `ExploreController::index`: radius constant `NEARBY_RADIUS_KM = 100`. Reads
  `lat`/`lng` from the query, fetches all filtered mountains (≈112 rows), and
  partitions in PHP into `nearbyMountains` / `otherMountains`. The Others list
  is paginated (10/page) via a `LengthAwarePaginator` over the collection, with
  `withQueryString()` so filters + lat/lng survive page links. Nearby is a
  page-1 highlight only (hidden on page 2+ via `onFirstPage()`). Card images are
  `loading="lazy"`.
- Card markup extracted to `resources/views/explore/partials/mountain-card.blade.php`
  (used by both sections; distance badge only when `showDistance`).
- Geolocation: `resources/js/features/NearbyMountains.js` (wired in `app.js`)
  asks for location once per session, remembers coords in sessionStorage, and
  reloads with `?lat=&lng=`. Hidden lat/lng inputs keep location across filter
  submits. A "Show mountains near me" button re-prompts after a denial.
- Lang keys added (en + id): `others`, `nearby_within_radius`,
  `no_nearby_within_radius`, `enable_location`, `km_away`, `no_mountains_found`.

**How to verify**

- `php artisan test --compact tests/Feature/ExploreControllerTest.php` (14 passing).
- Load `/explore`, allow location → Nearby section lists close mountains with a
  "N km away" badge; the rest fall under Others. Deny → only Others, with a
  "Show mountains near me" button. Needs HTTPS or localhost for geolocation.

## 2026-07-26 — branch: `main` — Fix VM deploy rsync exit 23 (can't set times on bootstrap/cache)

By: Claude Code

**What changed**

- `deploy_vm.yml` rsync switches: `-avz` → `-rlvz` plus
  `--omit-dir-times --no-perms --no-owner --no-group`, and added excludes
  `/bootstrap/cache`, `/.git`, `/.github`, `/node_modules`.
- Root cause: `-a` implies `-t/-p/-o/-g`; rsync tried to set times/perms on
  `bootstrap/cache` (owned by `www-data`), got "Operation not permitted", and
  exited 23. `-rlvz` drops attribute preservation; excluding `bootstrap/cache`
  also stops `--delete` from wiping the cache that the VM's `config:cache`
  step writes. Excluding `node_modules`/`.git`/`.github` trims deploy size
  (build already happens on the runner; only `public/build` is needed).
- Trade-off: without `-t`, incremental deploys retransfer a bit more.

**How to verify**

- Push to `main` and confirm the "Rsync ke VM" step succeeds (no exit 23) and
  the app still serves compiled assets from `public/build`.

## 2026-07-26 — branch: `main` — Natural Indonesian home dummy; drop forum seeder

By: Claude Code

**What changed**

- `HomeController::index`: the dummy home community cards are now natural —
  Indonesian names + Indonesian post bodies, and each avatar is an initials
  avatar generated from the name via `ui-avatars.com` (same generator
  `User::avatarUrl` already uses). Removed the now-unused `home.sample_post`
  lang key (en + id).
- Removed the forum seeder so the community forum starts empty like a real
  forum: deleted `database/seeders/ForumPostSeeder.php` and
  `tests/Feature/ForumPostSeederTest.php`, and dropped `ForumPostSeeder::class`
  from `DatabaseSeeder`. `CommunitySeeder` (groups only, no posts) is kept.
- Updated `HomeControllerTest` dummy-name assertions (`John Doe` → `Budi Santoso`).

**How to verify**

- `php artisan test --compact tests/Feature/HomeControllerTest.php` (9 passing).
- `php artisan migrate:fresh --seed` → community forum feed is empty; only
  communities (groups) are seeded.
- Load `/home` → community cards show Indonesian names/text with initials pfps.

## 2026-07-26 — branch: `main` — Home community preview → dummy; remove Forum Leaders arrow

By: Claude Code

**What changed**

- `HomeController::index`: the home "Community" showcase no longer queries real
  `Post` rows. It now renders a fixed set of 6 dummy cards, so real user posts
  never surface on the public home page. `communityImages` (catalog photos) is
  still used for the card backgrounds. Dropped now-unused `Post`/`Str` imports.
- `community/components/sidebar.blade.php`: removed the chevron arrow next to the
  "Forum Leaders" heading (it implied a link/action that didn't exist).
- Forum Leaders is still capped at 5 users (`PostController::index` → `->limit(5)`),
  which already satisfied the "only 5 people" request — no change needed there.
- Updated `HomeControllerTest`: the section that used to assert real forum posts
  appear now asserts they do **not** (dummy only).

**How to verify**

- `php artisan test --compact tests/Feature/HomeControllerTest.php` (9 passing).
- Load `/home` → Community cards show dummy names (John Doe, Sarah Lin, …), not
  real posts. Load `/community` → Forum Leaders card has no arrow, ≤5 people.

## 2026-07-23 — branch: `main` — VM deploy: stop `rsync --delete` wiping `.env` & runtime files
## 2026-07-23 — branch: `main` — Auth emails: send synchronously (fix "verification email never arrives")

By: Claude Code

**What changed**

- `deploy_vm.yml`: added `--exclude` patterns to the rsync step
  (`--exclude=/.env --exclude=/storage --exclude=/public/storage`).
- Root cause of "`.env` di VM selalu hilang tiap deploy": the deploy rsyncs
  the runner's checkout to `/var/www/sumorrow` with `--delete`, which forces
  the VM tree to exactly match the source. `.env` is gitignored (so it is
  never in the checkout), so `--delete` treated it as extraneous and removed
  it on every deploy. Same mechanism silently wiped `storage/` (user uploads,
  sessions, logs) and the `public/storage` symlink. rsync does **not** delete
  excluded paths, so the excludes preserve them across deploys. (This is not
  a Git history / `git clean` issue — the current workflow runs no git
  commands on the VM.)

**How to verify**

- After merge, run a deploy and confirm `ls -la /var/www/sumorrow/.env` still
  exists (no manual recreate needed) and previously-uploaded images under
  `storage/app/public` survive.
- `VerifyEmailNotification` and `ResetPasswordNotification`: dropped
  `implements ShouldQueue`. Both are now sent inline during the request.
- Root cause of the reported bug: registration correctly dispatches the
  verification notification (proven by the existing `Notification::fake`
  test), and SMTP itself works. But the notifications were queued, and the
  VM deploy (`deploy_vm.yml`) starts **no** queue worker. With any non-`sync`
  `QUEUE_CONNECTION` (the `.env.example` default is `database`), the job just
  sat in the `jobs` table and the email never sent. Local worked only because
  local `.env` uses `QUEUE_CONNECTION=sync`.
- Added guard test `test_auth_emails_are_not_queued_so_they_send_without_a_worker`
  in `EmailVerificationTest` so nobody re-adds `ShouldQueue` unnoticed.

**How to verify**

- `php artisan test --filter=EmailVerificationTest` (7 pass).
- Register a new account and confirm the verification email arrives without any
  `queue:work` running. No infra/queue-worker change needed.

---

## 2026-07-22 — branch: `main` — VM deploy: restore `storage:link`, fail fast on error

By: Claude Code

**What changed**

- `deploy_vm.yml`: added `set -e` at the top of the SSH script — previously
  a failed step (e.g. `git lfs pull` hitting a network/quota error,
  `composer install` failing) did not stop the script, so it could still
  reload nginx/php-fpm into a half-deployed state.
- `deploy_vm.yml`: added `php artisan storage:link` after `migrate --force`.
  The old App Service `startup.sh` ran this on every deploy; the new VM
  workflow (`setup/vm-cicd`, 2026-07-16) dropped it. Without the
  `public/storage` symlink, every file under `storage/app/public`
  (mountain photos, avatars, post images) 404s regardless of whether Git
  LFS pulled the real bytes — this is the most likely cause of the
  reported "gambar gunung belum ke-load" symptom on the VM.
- Not a code bug, but found while investigating: three mountains in
  `database/data/mountains_seeder.json` (Amasing, Helatoba Tarutung,
  Hutapanjang) reference `mountains/*.jpg` files that have never existed
  in the repo (confirmed via `git log --all`, not an LFS pull gap). The UI
  already falls back to `default-mountain.jpg` via `onerror` in
  `explore.blade.php`, so this shows the wrong image, not a broken icon —
  needs real photos added to `storage/app/public/mountains/` and the
  seeder re-run, not a code change.
- Also flagged for the user to verify manually (not fixable from the repo):
  whether a queue worker runs on the VM. The old Azure docs note
  `QUEUE_CONNECTION=sync` was required in App Settings because queued mail
  notifications (`VerifyEmailNotification`, `ResetPasswordNotification`)
  have no worker; unclear if the VM's `.env` carries this forward.

**How to verify**

- After merge/push to `main`, watch the "Deploy to Azure VM" workflow run
  green, then check `ls -la /var/www/sumorrow/public/storage` on the VM —
  should be a symlink to `../storage/app/public`. Open a mountain page and
  confirm a previously-broken image now loads.

---

## 2026-07-16 — branch: `fix/admin-panel` — Fix Azure deploy (take 2): regenerate lockfile with CI's npm

By: Claude Code

**What changed**

- `package-lock.json`: deleted and regenerated **from scratch with npm 10**
  (`npx npm@10 install`) — the npm that ships with Node 22 used by the
  deploy workflow. The previous fix (below) was written incrementally by
  npm 11 on Windows and satisfied npm 11's `npm ci` check but not npm 10's:
  the two compute optional platform-specific subtrees
  (`@rolldown/binding-*`, `@napi-rs/wasm-runtime` → `@emnapi/*`)
  differently, so CI run #17 still failed with
  "Missing: @emnapi/core@1.11.2 from lock file".
- `main_sumorrow.yml`: added `cache: 'npm'` to setup-node (install speed,
  no behavior change).
- Lesson for future lockfile changes on this repo: after touching
  package.json, regenerate the lock from scratch with CI's npm major
  (`rm package-lock.json && npx npm@10 install`) or upgrade the workflow's
  Node so local and CI match.

**How to verify**

- Verified locally from scratch (clean worktree, no node_modules):
  `npx npm@10 ci` passes, `npm ci` (npm 11) passes, `npm run build` passes.
- After merge to `main`, the deploy workflow's "Build frontend assets"
  step must go green and the deploy job must run.

## 2026-07-16 — branch: `fix/admin-panel` — Fix Azure deploy: lockfile out of sync

By: Claude Code

**What changed**

- `package-lock.json`: regenerated from a clean checkout. The lockfile
  committed with the leaflet change was written by `npm install` against an
  existing `node_modules` and ended up missing transitive entries
  (`@emnapi/core@1.11.1`, `@emnapi/runtime@1.11.1` under
  `@rolldown/binding-wasm32-wasi`), so `npm ci` in the deploy workflow
  (`main_sumorrow.yml`, "Build frontend assets" step) failed with
  "package.json and package-lock.json are in sync" — run #16 failed,
  deploy job skipped. Run #15 (pre-leaflet) was green.

**How to verify**

- Reproduced and verified locally in a clean worktree of the failing
  commit: `npm ci` failed with the same error; after regenerating the
  lockfile, `npm ci` + `npm run build` both pass from scratch.
- After pushing, workflow run #17+ on `main` should go green through
  "Build frontend assets" and reach the deploy job.

## 2026-07-16 — branch: `main` — Admin mountain form: cover image upload

By: Claude Code

**What changed**

- `StoreMountainRequest` (+ Update via inheritance): optional `image` field —
  `nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:4096`, same rules as
  the community/event uploads.
- `Admin\MountainController`: `saveCoverImage()` stores the upload on the
  `public` disk under `mountains/` (the path shape `MountainImage`'s
  accessor already resolves) and upserts the `is_cover` row; replacing a
  cover deletes the old locally-stored file (http/absolute URLs are left
  alone). Store and update both use it.
- `admin/mountains/form.blade.php`: "Cover Image" file input in the
  Identity section; edit mode shows the current cover thumbnail. Both form
  tags gained `enctype="multipart/form-data"`. New lang keys
  `field_cover_image`, `field_cover_image_hint`,
  `field_cover_image_replace_hint` (en + id).
- Tests use a real inline 1x1 GIF instead of `UploadedFile::fake()->image()`
  because the GD extension isn't available on every machine.

**How to verify**

- `php artisan test --compact tests/Feature/AdminControllerTest.php`
  (46 pass: upload on create, replace on update deletes the old file,
  non-image rejected).
- In the browser: create a mountain with an image → its edit page shows the
  cover served from `/storage/mountains/...` (requires `php artisan
  storage:link`). Verified end-to-end with Playwright + Edge, including a
  200 response for the stored image URL.

## 2026-07-16 — branch: `main` — Mountain coordinates: DMS validation + map pin-picker

By: Claude Code

**What changed**

- `StoreMountainRequest`: `coordinates` now validated against a DMS regex
  (`COORDINATES_PATTERN`, e.g. `8 deg 16' 0" S, 115 deg 25' 0" E`) — the only
  format `WeatherController::parseCoordinates()` understands; anything else
  silently fell back to default coordinates (wrong weather, no error).
  `UpdateMountainRequest` now just extends it (rules were identical).
- `admin/mountains/form.blade.php`: fixed the misleading placeholder
  (`7.45S 110.44E`, a format the parser can't read) and added a Leaflet/OSM
  map under the field — click or drag the pin to auto-fill the input in DMS;
  on edit, the pin initializes from the stored value. New npm dependency:
  `leaflet` (lazy-loaded chunk via dynamic import, only on this form).
  New module: `resources/js/features/CoordinatesPicker.js`. New lang keys:
  `field_coordinates_placeholder`, `coordinates_format_error`,
  `coordinates_map_hint` (en + id).
- `AdminControllerTest`: payloads switched to DMS; new tests for rejected
  non-DMS formats and accepted DMS with decimal seconds.

**How to verify**

- `php artisan test --compact tests/Feature/AdminControllerTest.php` (43 pass).
- In the browser: `/admin/mountains/create` → click the map → the
  coordinates input fills with DMS; submitting stores the mountain.
  Submitting a value like `7.45S 110.44E` is rejected with a helpful error.
  Verified end-to-end with Playwright + Edge (click, drag, submit, edit-page
  pin). Requires `npm run build` (new leaflet chunk).

## 2026-07-16 — branch: `main` — Admin header dropdown no longer paints behind page cards

By: Claude Code

**What changed**

- `layouts/admin.blade.php`: added `relative z-10` to the `<header>`. Its
  `backdrop-blur` creates a stacking context, and `<main>` (made
  `position: relative` by the 2026-07-15 overflow fix) is a later sibling,
  so main's content painted **on top of** the header's dropdown — the
  language menu was visible but covered by (and unclickable through) the
  stat cards on dashboard/user-updates/mountain-data. `z-10` keeps the
  header below the mobile sidebar overlay (`z-20`/`z-30`) and the confirm
  modal (`z-200`).

**How to verify**

- Log in as admin, hover the language switcher on `/admin/dashboard`,
  `/admin/user-updates`, `/admin/mountain-data`: the menu must render on
  top of the cards and both items must be clickable (clicking ID switches
  the locale). Verified with Playwright + Edge against the live app:
  `document.elementFromPoint` at every menu-item center resolves to the
  link itself on all three pages, and clicking ID lands on a page with
  `<html lang="id">`.

## 2026-07-16 — branch: `main` — Hover dropdowns no longer close while moving into them

By: Claude Code

**What changed**

- `components/locale-switcher.blade.php`, `components/navbar.blade.php`,
  `components/navbar-light.blade.php`: the dropdown panels were offset from
  their trigger with `mt-2`/`mt-3`. Margins are not hoverable, so crossing
  that gap dropped `group-hover` and the menu vanished before you could
  click a language / menu item. The gap is now `pt-*` padding on the
  positioned wrapper (padding is part of the element, so hover survives),
  with the visual panel styles moved to an inner div. Also added
  `group-focus-within:*` so the menus open via keyboard focus.

**How to verify**

- On any admin page (or any page with a navbar), hover the language
  switcher or avatar, then move the cursor straight down into the menu —
  it must stay open until you leave it. Verified headless (Playwright +
  Edge) against the rendered component: panel stays visible across the
  button → gap → menu-item path and closes on mouse-leave.
- `npm run build` must be run (new Tailwind utilities: `top-full`,
  `group-focus-within:*`).

## 2026-07-15 — branch: `fix/admin-user-updates-overflow` — Admin pages no longer scroll past the viewport
## 2026-07-15 — branch: `fix/startup-config-cache-order` — Fix stale config cache at boot

By: Claude Code

**What changed**

- `layouts/admin.blade.php`: added `relative` to `<main>`. The `sr-only`
  labels inside the user table (Tailwind `sr-only` = `position: absolute`)
  had no positioned ancestor, so they escaped the layout's overflow clipping
  and stretched the document ~400px below the viewport — the page could
  scroll down into blank space. Anchoring them to the scroll container fixes
  every admin page at once.

**How to verify**

- Log in as admin, open `/admin/user-updates` with 12+ users: the window
  itself must not scroll (no blank strip below the app frame); only the
  content area scrolls. Verified headless via Playwright:
  `document.documentElement.scrollHeight` dropped from 1314 to 900 at a
  900px viewport.
- `startup.sh`: `config:cache` now runs **before** migrate/seed instead of
  after. `bootstrap/cache/config.php` persists across App Service restarts,
  so the seeder (and migrate) previously read env values from the *previous*
  boot — an app-setting change (e.g. `ADMIN_EMAILS`) only took effect on the
  second restart after it was set.

**How to verify**

- Set/change an app setting in Azure, let the app restart once, and confirm
  the AdminSeeder promotion applied immediately (`role` = admin on first
  restart, not the second).

---

## 2026-07-15 — branch: `main` — Env-driven admin bootstrap (AdminSeeder)

By: Claude Code

**What changed**

- New `database/seeders/AdminSeeder.php`: promotes every email in the
  `ADMIN_EMAILS` env var (comma-separated) to admin, creating the account
  first if missing (null password; owner claims it via Google login, which
  matches by email). Idempotent, never demotes. Read via
  `config('auth.admin_emails')` so it works under `config:cache`.
- Wired into `DatabaseSeeder` and `startup.sh` (runs on every Azure boot,
  right after `migrate --force`). `.env.example` documents `ADMIN_EMAILS`.
- To bootstrap an admin on Azure: set the `ADMIN_EMAILS` App Setting —
  saving it restarts the app, which runs the seeder. No DB access needed.
- Also promoted the first local admin via tinker (one-off, no code).

**How to verify**

- `php artisan test --compact tests/Feature/AdminSeederTest.php` (4 passed).
- Locally: set `ADMIN_EMAILS` in `.env`, run `php artisan db:seed --class=AdminSeeder`,
  check the user's role.

---

## 2026-07-11 — branch: `main` — Custom error pages (4xx/5xx, incl. 413 oversized upload)

By: Claude Code

**What changed**

- New `resources/views/errors/`: shared `page.blade.php` (extends `layouts.app`,
  reads `$exception->getStatusCode()`, message from `lang/*/errors.php`), plus
  one-liner `@include` views for 401/402/403/404/419/429/500/503 and `4xx`/`5xx`
  fallbacks. Explicit per-status files are required because the framework ships
  vendor views for those codes that would otherwise win over the `4xx` fallback.
- New `lang/en/errors.php` + `lang/id/errors.php` (per-status title/message,
  `default` fallback, `back_home`). 413 has an upload-specific message —
  `PostTooLargeException` (upload > `post_max_size`) maps to 413 automatically.
- `tests/Feature/ErrorPageTest.php`: 404 page, 413 page, unmapped-status
  fallback, and API 404 still returns the JSON envelope.
- Note: a 413 rejected by the web server itself (nginx `client_max_body_size`)
  never reaches PHP; the deployment's nginx config must allow at least
  `post_max_size` for Laravel to render this page.

**How to verify**

- `php artisan test --compact tests/Feature/ErrorPageTest.php` (4 passed).
- In the browser, visit any nonexistent URL, or upload an image larger than
  `post_max_size` on a post form — styled Sumorrow error page with navbar/footer
  appears instead of the server default.

---

## 2026-07-10 — branch: `feat/delete-confirmation-modal` — Basecamps to desktop sidebar; name-only search

By: Claude Code

**What changed**

- `explore/show.blade.php`: on desktop (`lg:`), Official Basecamps now renders
  as a card in the right sidebar above Nearby Mountains; the original in-flow
  section is kept for mobile/tablet (`lg:hidden` / `hidden lg:block` pair).
- `ExploreController::index()`: explore search now matches mountain **name
  only** (dropped the `description` LIKE clause).
- `ExploreControllerTest::test_explore_search_filters_results` updated so the
  non-matching mountain's description contains the search term, proving
  name-only matching.

**How to verify**

- `php artisan test --compact tests/Feature/ExploreControllerTest.php` (10 passed).
- In the browser at desktop width: open a mountain detail page — basecamps
  card appears top-right above Nearby Mountains; at mobile width it stays in
  the main column. On `/explore`, searching a word that only appears in a
  description returns no results.

---

## 2026-07-09 — branch: `feat/delete-confirmation-modal` — Styled confirm modal for all post deletes

By: Claude Code

**What changed**

- `community/components/feed.blade.php` and `profile/partials/posts.blade.php`:
  the last two post-delete forms still using the native `onsubmit="return
  confirm(...)"` now use the existing `confirm-submit-form` modal pattern
  (`<x-confirm-submit-modal />` + `ConfirmSubmit.js`, already in
  `layouts/app`). Every forum/community/profile delete now goes through the
  same styled danger modal. No new components or JS.

**How to verify**

- `php artisan test --compact --filter="test_feed_shows_delete_button_only_on_own_posts|test_profile_post_delete_uses_confirmation_modal"` (2 passed).
- In the browser: delete a post from the community feed, from a community
  page, and from the profile tabs — all should show the styled modal, not the
  browser confirm dialog.

---

## 2026-07-09 — branch: `fix/enable-ssl-verify` — Harden Google auth HTTP client

By: Claude Code

**What changed**

- `config/services.php`: removed a non-default HTTP-client option on the
  `google` service so the client uses its secure defaults. No behavior change
  for normal operation; Socialite reads the same credentials/redirect envs.

**How to verify**

- `php artisan test --compact tests/Unit/Services/GoogleAuthServiceTest.php`
  (4 passed), and Google login on production still completes once
  `GOOGLE_REDIRECT_URI` is registered on both the Azure and Google Console
  sides.

---

## 2026-07-09 — branch: `fix/https-behind-proxy` — HTTPS asset URLs on Azure

By: Claude Code

**What changed**

- `bootstrap/app.php`: `$middleware->trustProxies(at: '*')`. Azure App
  Service terminates TLS at its front end and forwards plain HTTP; without
  trusting `X-Forwarded-Proto`, Laravel rendered `http://` Vite asset URLs on
  an `https://` page and the browser blocked them (mixed content → unstyled
  site).
- `tests/Feature/TrustProxiesTest.php`: asserts a request with
  `X-Forwarded-Proto: https` is seen as secure and generates https URLs.

**How to verify**

- `php artisan test --compact tests/Feature/TrustProxiesTest.php` (passes).
- After deploy: view source of the prod `/home` page — Vite `<link>`/`<script>`
  URLs must start with `https://`, and the page renders styled.

---

## 2026-07-09 — branch: `fix/deploy` — Azure deploy pipeline fixed

By: Claude Code

**What changed**

- `.github/workflows/main_sumorrow.yml`: frontend build (`npm ci && npm run
  build`) moved into the **build** job before the artifact upload (it had been
  added after the deploy step, so built assets never reached Azure); composer
  now runs with `--no-dev --optimize-autoloader`; checkout uses `lfs: true` so
  mountain images deploy as real files, not LFS pointers; `node_modules`
  excluded from the artifact; removed the pointless composer.json existence
  check.
- `startup.sh` (new): App Service startup script — applies the `default` nginx
  config (docroot → `public/`), runs `migrate --force`, `storage:link`, and
  config/route/view cache. Portal Startup Command must be set to
  `bash /home/site/wwwroot/startup.sh`.
- Still manual (portal): App Settings for APP_KEY/DB/Google/OpenWeatherMap,
  `QUEUE_CONNECTION=sync` (queued mail notifications have no worker), real
  SMTP mailer, PHP 8.4 stack, and Google OAuth redirect URI for the prod
  domain.

**How to verify**

- Merge to `main`, watch the "Build and deploy" workflow: build job must show
  the Vite build; deploy job must pass Azure login (secrets were recreated via
  Deployment Center). Then open the site root — Blade pages should render with
  styles and mountain images.

---

## 2026-07-09 — branch: `main` — Home desktop hero made full screen

By: Claude Code

**What changed** (`home.blade.php`)

- Hero wrapper moved out of the `sm:w-[95%]` container so it spans the full
  viewport width; a new "Content Container" div (95% / max-w-350) now wraps
  everything from About down. Desktop hero height `sm:h-[700px]` →
  `sm:h-screen`; all corners square on desktop (`sm:rounded-none`) so the
  photo covers the viewport edge-to-edge (mobile keeps its rounded bottom).
  Weather chip stays `top-28` on desktop too since
  the fixed navbar now floats over the hero; search card still overlaps the
  hero's bottom edge (peeks above the fold as a scroll cue). Mobile is
  visually unchanged.

**How to verify**

- `/home` at desktop width: hero fills the viewport, navbar floats over the
  photo, chip below navbar, search card at the fold. Verified via
  headless-Edge screenshot at 1440×900. HomeControllerTest: 9 passed.

---

## 2026-07-09 — branch: `main` — Home desktop search bar restyle (mockup match)

By: Claude Code

**What changed**

- `home.blade.php`: search form restyled from the blue puzzle-cutout block
  to a white rounded card — gray pill search input, hairline divider,
  "Elevation Filter" label with the dashed value chip inline beside it,
  light-blue slider track below, solid navy pill submit button.
- `resources/css/app.css`: range-slider thumb enlarged to 20px, lighter
  blue (#3f8fd9) with a soft shadow.
- Copy per mockup: `home.search_placeholder` → "Find Mountain"/"Cari
  Gunung"; `home.explore_now` → "Explore Expeditions"/"Jelajahi Ekspedisi"
  (keys used only by this form).

**How to verify**

- `/home` at desktop width — verified against the provided mockup via
  headless-Edge screenshot. HomeControllerTest: 9 passed.

---

## 2026-07-09 — branch: `main` — Home hero: mobile search removed, unified weather chip

By: Claude Code

**What changed** (`home.blade.php`)

- Search bar + elevation filter hidden on mobile (`hidden sm:block` on the
  wrapper); still shown from `sm` up, overlapping the hero as before.
- Weather widget now uses the frosted-glass chip style on every breakpoint;
  the desktop puzzle-cutout (solid #c2dbec block with 16px notch borders)
  is gone. Positioned `top-28 right-4` on mobile (clears the floating
  navbar), `top-6 right-6` from `sm` up. JS ids unchanged.

**How to verify**

- `npm run build` (done); check `/home` at mobile and desktop widths —
  verified via headless-Edge screenshots.
- `php artisan test --compact tests/Feature/HomeControllerTest.php` — 9 passed.

---

## 2026-07-09 — branch: `main` — Mountain detail layout reorder

By: Claude Code

**What changed** (`explore/show.blade.php`)

- "Official Basecamps" moved from the right sidebar into the main column,
  directly after "Critical Information", restyled as a 2-col card grid.
- FAQ moved out of the left column to the very bottom of the page (full
  container width, last section before the footer).
- Breathing room: container `py-10 md:py-16`, column gap `gap-10 lg:gap-14`,
  left-column section spacing `space-y-14 md:space-y-16`, FAQ `mt-14 md:mt-20`.
- Sidebar now holds only Nearby Mountains + community CTA.

**How to verify**

- Browse `/explore/6` (has basecamps): section order should be About →
  Critical Info → Basecamps → Weather → Location → Reviews, FAQ last.
- `php artisan test --compact tests/Feature/ExploreControllerTest.php` —
  10 passed.

---

## 2026-07-09 — branch: `main` — Home page mobile redesign

By: Claude Code

**What changed** (all in `home.blade.php`, mobile breakpoint only — desktop
unchanged)

- Hero gets rounded bottom corners; SUMORROW wordmark scaled up on phones
  (`clamp(3rem,13vw,140px)`).
- Weather widget: full-width strip at the hero's bottom → compact frosted
  glass chip floating top-right (below the fixed navbar, `top-28`); reverts
  to the desktop puzzle-cutout from `sm` up. JS untouched (same element ids).
- Search bar now floats over the hero's bottom edge (`-mt-14`, rounded,
  shadow) — the mobile counterpart of the desktop cutout overlap.
- All sections get a consistent `px-4` gutter on mobile and cards are
  rounded again (feature, community shell, peak cards) instead of
  edge-to-edge `rounded-none`; removed the ad-hoc `mx-4 px-2` hacks.
- About heading left-aligned on phones with forced `<br>`s active only from
  `sm` up; tagline indent reduced on mobile.

**How to verify**

- `npm run build` (done); browse `/home` under 640 px — verified via
  headless-Edge screenshots at 500 px (Edge clamps window width ≥ ~500, so
  375 px shots crop misleadingly).
- `php artisan test --compact tests/Feature/HomeControllerTest.php` —
  9 passed.

---

## 2026-07-09 — branch: `main` — Mobile UI/UX audit & fixes

By: Claude Code

**What changed**

- Audited every Blade page at mobile widths; most pages were already
  responsive, so only targeted fixes were applied:
  - `components/navbar.blade.php` — mobile menu Community link pointed to
    `#`; now `/community`.
  - `components/footer.blade.php` — removed stray `""` in five class
    attributes (invalid HTML) and wired Home/Explore/Community links to
    their real routes (were `#`).
  - `explore.blade.php` — "Apply Filters" button is now visible (full-width)
    inside the mobile filter drawer instead of desktop-only; previously
    filters only applied via the non-obvious close-drawer auto-submit.
  - `profile/partials/hikings.blade.php` — tab header stacks on small
    screens so the title and action buttons no longer collide.
  - `lang/en/home.php` — hero tagline typo "Tommorow" → "Tomorrow".

**How to verify**

- `npm run build` (done), then browse `/`, `/explore`, `/community`,
  `/profile` at ~375 px width.
- `php artisan test --compact tests/Feature/ExploreControllerTest.php
  tests/Feature/HomeControllerTest.php tests/Feature/ProfileControllerTest.php
  tests/Feature/CommunityControllerTest.php` — 54 passed.

---

## 2026-07-08 — branch: `main` — README rewrite

By: Claude Code

**What changed**

- Rewrote `README.md` for GitHub: features, tech stack, setup steps
  (prereqs, `composer run setup`, `.env` API keys, seeding), common
  commands, API overview, project-structure highlights, docs index,
  and contributing notes. Docs-only, no code changes.

**How to verify**

- Read `README.md`; check the doc links resolve.

---

## 2026-07-06 — branch: `main` — Audit fixes (bugs & security)

By: Claude Code

**What changed**

- Closed an access-control gap on the public post-detail route for
  private-community content (`PostController::show` now gates by membership).
- Removed dead, uncallable controller code that duplicated the forum feed
  (`CommunityController::index`).
- Hardened the forgot-password flow against account enumeration (uniform
  response) and added throttling; also throttled the community-join endpoint.
- Regenerated the session after Google OAuth login (session-fixation guard).
- Made community slug generation collision-safe and non-empty for non-latin
  names.
- Centralised post-body markdown rendering with raw HTML stripped
  (`Post::renderedBody`), replacing three unsanitised `{!! Str::markdown !!}`
  call sites.
- Deleted a no-op console command plus its test that faked a pass via
  `debug_backtrace` (feature was already removed).
- Enforced email verification before posting: `verified` middleware now gates
  the forum-post, summit-log, and comment routes. `UserFactory` defaults to
  verified with a new `unverified()` state; unverified users are redirected to
  `verification.notice`.
- Extended the `verified` gate to the other create/interaction writes:
  community create/join, event create, gear add, rating submit, and post like.
  Leaving a community and editing/deleting one's own resources stay ungated.
- Centralised avatar URL resolution in `User::avatarUrl(?string $fallback)`,
  fixing uploaded avatars that 404'd when rendered with a bare `asset()`.
  Replaced the scattered per-view path logic (navbar, profile, community,
  admin, home cards) with calls to it, preserving each area's fallback image.
- Made rating submission recompute `avg_rating` inside a transaction to narrow
  a concurrent-write race.
- Removed dead code: unused `VerificationController` (routes use closures),
  the orphaned `PostReply` model plus `Post::replies`/`User::postReplies`
  relations (comments live in `PostComment`), and unused `Post::scopeByTag`.
  Note: `Community::getMemberCount` was kept — it is used by `community-card`.

**Verify**: `php artisan test` — full suite green (298 passing).

## 2026-07-06 — branch: `main` (uncommitted) — Locale switcher as flag dropdown
## 2026-07-06 — branch: `main` (uncommitted) — Forum sidebar scoping + guest avatar

By: Claude Code

**What changed**

- Replaced the "EN | ID" text toggle with a shared `<x-locale-switcher>` component (`resources/views/components/locale-switcher.blade.php`): flag emoji (🇺🇸/🇮🇩) + hover dropdown, themed via `button-class`/`active-class`/`panel-class` props. Used in `navbar-light.blade.php` (desktop dropdown, mobile `inline` variant — the mobile menu's `overflow-hidden` wrapper would clip an absolute dropdown) and `layouts/admin.blade.php` (desktop dropdown, summit-blue theme).
- Kept the `data-locale-switcher` attribute and existing aria-labels on the component root so `tests/Feature/LocaleMiddlewareTest.php` still passes unchanged.
- No new dependencies; flags are Unicode emoji, no image assets.

**Verify**: `php artisan test --filter=LocaleMiddlewareTest`, or visit `/home` and `/admin/dashboard` and hover the flag button.
- Forum Leaders and Popular Tags (`CommunityController::index`, `PostController::index`) now count global forum posts only: posts with `community_id` set (My Community posts) are excluded via `whereNull('community_id')`.
- Guest visitors on the forum composer (`resources/views/community/components/feed.blade.php`) now see `images/community/profile-blank.png` instead of the dummy mountain avatar; logged-in users without an avatar keep the existing fallback.
- New `ForumPostSeeder` (registered in `DatabaseSeeder`): 3 demo users + 8 tagged global forum posts with likes and a topic-matched image each (bundled public assets), idempotent via `firstOrCreate`.
- Default avatar unified: every avatar fallback in community views (feed composer, post cards, post detail, comments, sidebar leaders) and the home community cards now uses `images/community/profile-blank.jpg` instead of the rinjani mountain photo / initials.
- Fixed broken My Community images in the forum sidebar: raw `image_url` (no `asset()`) with a nonexistent `bromo.jpg` fallback replaced by the existing `Community::profileImageUrl()` helper. Each My Community entry now links to its community detail page.
- Home Community section (`home.blade.php` + `HomeController`) now shows the 8 latest global forum posts (author, body excerpt, likes/comments counts, link to post detail) instead of hardcoded "John Doe" cards; falls back to the old sample cards when the forum is empty. Posts without images borrow catalog mountain images.

**How to verify**

- `php artisan test --compact tests/Feature/PostControllerTest.php tests/Feature/CommunityControllerTest.php tests/Feature/HomeControllerTest.php tests/Feature/ForumPostSeederTest.php` (new tests: `test_forum_leaders_and_popular_tags_exclude_community_posts`, `test_guest_composer_shows_blank_profile_image`, `test_home_community_section_shows_global_forum_posts_only`, seeder idempotency test).
- Manually: `php artisan db:seed --class=ForumPostSeeder`, then open `/home` — Community section shows the seeded posts; post inside a community and check the `/community` sidebar — the post must not raise contributions or tag counts.

---

## 2026-07-05 — branch: `main` (uncommitted) — My Community detail page

By: Claude Code

**What changed**

- New community detail page `GET /community/{community}` (auth-only) at `resources/views/community/show.blade.php`: responsive header (banner + overlapping profile image with bundled defaults from `public/images/community/`), Forum / Members / About tabs, join/leave with confirm dialogs, flash feedback via the existing layout toasts.
- Community CRUD completed: `PATCH /community/{community}` (edit name/description/privacy + upload profile/banner images to `storage/community/`) and `DELETE /community/{community}` — both creator-only (`403` otherwise); membership alone grants no edit/delete rights. Join/leave unchanged and open to any member.
- Community-scoped forum posts: new nullable `posts.community_id` FK (cascade on community delete). The feed composer inside a community posts there (members only, enforced server-side); global forum feeds (`/community`, `/community/explore`) exclude community posts via `whereNull('community_id')`.
- Migrations: `2026_07_05_000001_add_banner_url_to_communities_table`, `2026_07_05_000002_add_community_id_to_posts_table` — run `php artisan migrate` after pull.
- Community cards now link to the detail page, show the community banner (default from `public/images/community/banner.jpg`) and an Owner badge; new EN + ID lang keys under `community.*`.
- Destructive community actions (delete/leave community, delete event) use the app's existing global confirm system (`form.confirm-submit-form` + `<x-confirm-submit-modal />` handled by `ConfirmSubmit` in app.js). A short-lived duplicate `components/confirm-modal.blade.php` was removed in favour of it.
- Mobile navbar (`navbar-light`): Community link now points to `/community` (was a dead `#`), and logged-in users get an avatar + Profile entry in the mobile menu. Avatar URL logic is computed once at the top of the component.
- Community page mobile polish: My Community top bar stacks on small screens; create-community modal scrolls on short viewports; feed composer's tag pills + Post button stack on mobile; emoji picker popover is viewport-centered on mobile (was clipping off-screen); GIF popover capped to viewport width.
- Member-only interaction enforcement: comments and likes on community-scoped posts now require membership server-side (403 otherwise, `PostController::ensureMemberOfPostCommunity`). UI matches: non-members get a read-only like count in the community feed and a "join to interact" notice instead of the reply form on post detail; community post detail's back link returns to the community page.
- Event deletion now also requires current membership: the event creator or community owner loses the right after leaving and regains it by rejoining (`EventController@destroy`).
- Private communities are token-gated: new `communities.join_token` column (`2026_07_05_000004`, backfills existing private rows). A token is auto-generated when a community is created as / switched to private; the creator sees it (with copy button) in the About tab. Non-members hit a token gate (`community/show-locked.blade.php`) instead of the community content, and `POST join` validates the token (`hash_equals`) for private communities — the creator can always rejoin without one. Successful joins now land on the community page.
- Removed the member-avatar stack from the community page header.
- Community Events: new `events` table (`2026_07_05_000003_create_events_table`), `Event` model, `EventController`. Members create events (`POST /community/{community}/events`, named "event" error bag, optional image to `storage/events/`); the event creator or community owner deletes them (`DELETE /community/events/{event}`). Events tab on the detail page with create modal, empty state, and the shared confirm modal; `?tab=` query deep-links a tab. Tests in `tests/Feature/EventControllerTest.php`.
- Skipped (from the `feature/mycommunity` reference branch): post saves/bookmarks and the `author_id → user_id` rename — separate features, add when requested.

**How to verify**

- `php artisan test --compact tests/Feature/CommunityControllerTest.php` (21 tests: show, creator-only update/destroy, member-only community posting, feed scoping).
- Manually: create a community from the My Community tab → lands on its detail page with default banner/profile; edit images via the Edit modal as creator; open the same community as another user → only Join/Leave visible, PATCH/DELETE return 403.

---

## 2026-07-05 — branch: `main` (uncommitted)

By: Claude Code

**What changed**

- Fixed email verification: `User::hasVerifiedEmail()` previously reported all users as verified regardless of actual verification status; it now uses the framework's standard `email_verified_at` check. Google-OAuth users remain auto-verified (`GoogleAuthService` sets `email_verified_at`). Credential users only get the verified badge (Edit Profile + profile page) after clicking the emailed link.
- `RegisterController` no longer sends the verification email manually — the `Registered` event's built-in listener handles it (avoids a double send now that the verified check is accurate).
- New Sumorrow-branded verification email: `App\Notifications\VerifyEmailNotification` (extends the framework notification, queued, localized EN + ID via new `auth.verify_notification_*` lang keys).
- Updated the stale `hasVerifiedEmail` note in CLAUDE.md.
- Published Laravel mail templates to `resources/views/vendor/mail` and restyled them with Sumorrow branding: header shows `SUMORROW-LOGO-BLACK.png`, primary buttons/links/panels use navy `#094174`, body background `#f0f5fa`, footer hardcodes the Sumorrow name. Applies to all `MailMessage` notifications (verification + password reset). `.env.example` `APP_NAME` set to `Sumorrow`.

**How to verify**

- `php artisan test --compact` — 259 passing (6 new tests in `tests/Feature/Auth/EmailVerificationTest.php`: single email on register, signed-link verification, invalid-hash rejection, Google auto-verify, badge visibility on Edit Profile, branded mail render).

## 2026-07-04 — branch: `feat/summit-log` (uncommitted)

By: Claude Code

**What changed**

- `CommunityController::index` (route `/community`) now matches `PostController::index`: the feed excludes summit logs (tag-less posts) via `whereHas('tags')`, and Forum Leaders counts only tagged forum posts.
- Summit-log delete buttons restyled small and bottom-right: on the My Activities cards (`profile/posts/index.blade.php`) and moved from the top bar into the card footer on the detail page (`profile/posts/show.blade.php`).
- Summit-log delete now confirms via the shared `ConfirmSubmit` modal (`confirm-submit-form` + data attributes) instead of the native `confirm()` dialog, on both pages above and on the profile Hikings tab (`profile/partials/hikings.blade.php`).

- CI fix: `SearchAndFilterTest::test_feed_can_be_filtered_by_search_term` created tag-less posts, which the forum feed now correctly excludes as summit logs — the test's posts are now tagged.

**How to verify**

- `php artisan test --compact` — 253 passing (2 new tests in `CommunityControllerTest` covering feed exclusion and leaders count on `/community`).

## 2026-07-03 — branch: `main` (uncommitted)

By: Claude Code

**What changed**

- Community "Explore" sub-tab renamed to "Forum" (lang key `community.tab_explore`, EN + ID).
- Removed the "Who to Follow" sidebar section and the whole follow feature with it: `users.follow` route, `UserController`, `FollowToggle.js`, its test file, and related lang keys. The `follows` table and `User` model relations remain.
- Profile page restructured: the **Posts** tab now shows the user's forum posts (posts that have category tags) with a "Last Reviews" sidebar (mountains the user most recently rated, replacing "Top Mountains"); a new **Hikings** tab shows the Summit Log (posts without tags — formerly "Hiking History", renamed in EN/ID). `ProfileController::index` now passes `forumPosts`, `hikingPosts`, `lastReviews`. `ProfilePostController::index` ("My Activities") excludes forum posts.
- New delete-post feature: `DELETE /community/posts/{post}` (`community.posts.destroy`, `PostController::destroy`, author-only via 403 guard, deletes stored image files; DB rows cascade). Delete buttons on the forum feed (own posts), profile Posts tab, and Hikings tab.

**How to verify**

- `php artisan test --compact` — 247 passing (new destroy/authorization tests in `PostControllerTest`, tab-split tests in `ProfileControllerTest` / `ProfilePostControllerTest`).
- `npm run build` — passes.

## 2026-07-03 — branch: `feat/bilingual-localization`

By: Claude Code

**What changed**

- Fixed a bad merge resolution in `HomeController` (merge `a2fe9d5`): the mountain-mapping closure still referenced the pre-merge `$firstImage`/`$fallbackImage` variables while the rest of the method used `main`'s `image_raw` + `has_real_image` cache shape, causing a 500 on `/home` and two CI test failures.

**How to verify**

- `php artisan test --compact tests/Feature/HomeControllerTest.php` — 7 passing.
- Full suite: `php artisan test --compact` — 245 passing.

## 2026-07-03 — branch: `main`

By: Codex

**What changed**

- Added an `EN | ID` locale switcher to the live user navbar (desktop and mobile) and admin header. Links preserve the current URL/query parameters, and the active locale stays highlighted while the alternate locale highlights on hover.

**How to verify**

- `php artisan test --compact tests/Feature/LocaleMiddlewareTest.php tests/Unit/LocalizationTest.php` — 12 passing, 654 assertions.
- `php artisan view:cache && php artisan view:clear` and `npm run build` — pass.

## 2026-07-03 — branch: `main`

By: Codex

**What changed**

- Audited the English/Indonesian localization pass and fixed request-locale leakage under persistent workers by resetting both Laravel and Carbon to the stable English fallback on every request without a valid stored locale.
- Completed the remaining Blade/backend localization gaps: flash messages, password-reset mail, validation messages, dates, pagination, units, gear/privacy labels, API documentation prose, and the broken `:weightkg` placeholder.
- Added database-free localization tests covering worker reset behavior, `id` selection, queued notification locales, validation output, and exact file/key/placeholder/HTML-tag parity across all 11 PHP language-file pairs plus JSON mail translations.

**How to verify**

- `php artisan test --compact tests/Unit/LocalizationTest.php` — 5 passing, 633 assertions.
- `php artisan view:cache && php artisan view:clear` — succeeds; `vendor/bin/pint --dirty --format agent` — clean.
- `php artisan test --compact` — 242 passing, 1,281 assertions against the XAMPP MySQL test database.
## 2026-07-03 (later) — branch: `main`

By: Claude Code

**What changed** (forum + profile pass)

- **Fixed the stuck-disabled Post button** (`FeedComposer.js`): `checkPostable()`
  now (1) runs once on load — after a failed-validation redirect the browser
  restores body/categories from old input without firing events, which left
  the button disabled forever; (2) listens to category-pill changes and
  requires ≥1 category, so the "no category" server error can't happen from
  the UI anymore; (3) counts a selected GIF as content (GIF-only posts were
  valid server-side but the button never enabled).
- Flash banners (`layouts/app.blade.php`) moved from `top-24` to `top-32` so
  the success toast clears the floating navbar; post-detail page top padding
  `pt-24 md:pt-32` → `pt-32` so "Back to Feed" no longer sits under the navbar
  on phones.
- Renamed the community tab label "Explore" → **"Forum"** (display text only —
  route name `community.explore`, tab ids, and JS hooks unchanged).
- **Removed the save/bookmark feature end-to-end**: feed view buttons,
  `FeedLikeSave.js` save handler (file renamed to `FeedLike.js`, class
  `FeedLike`), `PostController::toggleSave` + the `is_saved` feed pinning,
  `community.posts.save` route, `Post::saves()` relation, and the `saves`
  eager loads in Post/Community controllers. The `post_saves` table and its
  migration remain (shared migrations are never edited) — drop it in a future
  migration if desired. No tests referenced the feature; a new test asserts
  the feed shows "Forum" and contains no save buttons.
- Profile responsiveness: the "Hiking History" header row (title + two
  actions) overflowed narrow screens; it now stacks under `sm`.

**How to verify**

- `php artisan test` — 232 passing. `npm run build` — succeeds.
- In the forum: type text but no category → button disabled; pick a category →
  enabled. Submit with the server error path (disable JS) → after reload the
  button reflects restored input. Post success toast appears below the navbar.
- Open a post detail on a phone width — "Back to Feed" is fully visible.
- Feed posts show comment/like/share only (no bookmark icon).

## 2026-07-03 — branch: `main`

By: Claude Code

**What changed**

- Added backend localization (English default + Indonesian), no UI switcher yet.
  `config('app.available_locales')` = `['en', 'id']`; new `App\Http\Middleware\SetLocale`
  (registered via `$middleware->web(append: ...)` in `bootstrap/app.php`) resolves the
  active locale from a `?lang=` query param, persists it to `session('locale')`, and
  falls back to the configured default otherwise — so a future language toggle just
  needs to link to `?lang=id` / `?lang=en`, no new route required.
- Extracted every hardcoded UI string in `resources/views/**/*.blade.php` into `__()`
  calls against 10 feature-grouped `lang/en/*.php` + `lang/id/*.php` pairs (`common`,
  `auth`, `admin`, `community`, `profile`, `gear`, `home`, `explore`, `api`,
  `pagination`). The four views that were previously hardcoded Indonesian
  (`auth/forgot-password.blade.php`, `auth/reset-password.blade.php`,
  `profile/partials/achievements.blade.php`, `profile/partials/gear.blade.php`) now
  default to English in the Blade source, with the original Indonesian preserved
  verbatim as the `id` translation.
- Fixed 3 inline `onsubmit="return confirm('...')"` delete-confirmation dialogs
  (`admin/user-updates.blade.php`, `admin/mountain-data.blade.php`,
  `profile/partials/gear.blade.php`) that would have broken on an apostrophe in a
  translated string or an interpolated username/mountain name — now use
  `Illuminate\Support\Js::from(__(...))` instead of raw string interpolation.
- Deliberately left untranslated: gear category `<select>` values (round-trip to the
  `gears.category` DB column, matched by JS filters), Indonesian province/region names,
  unit abbreviations (masl/km/m/hrs/mdpl), and API-contract literals in `api/docs.blade.php`
  (HTTP header names, query param names, enum values documenting the actual API).
- Out of scope this pass (flagged as a follow-up, not implemented): controller/middleware
  flash messages (e.g. `AdminMiddleware`'s `'Unauthorized access.'`) and FormRequest
  validation messages — still hardcoded English.

**How to verify**

- `php artisan test` — 234 passing (up from 228; added `LocaleMiddlewareTest`).
- `vendor/bin/pint --dirty` — clean.
- Hit any web route with `?lang=id` (e.g. `/home?lang=id`) — UI text flips to Indonesian
  and persists across subsequent requests via session; an invalid `?lang=xx` is ignored.
  No visible toggle exists yet — this was query-param/session only, by design.

---
- **Mobile navbar fixes** (`navbar.blade.php`, `navbar-light.blade.php`,
  `HamburgerMenu.js`): the mobile menu's Community link pointed at `#` (fixed to
  `/community` in both navbars); the light navbar's mobile menu was missing the
  Profile link for logged-in users; the menu's fixed `max-h-[400px]` cap could
  clip the bottom items (logout) — the menu now animates to its measured
  `scrollHeight` instead; the menu now closes on link tap, outside click,
  Escape, and when resizing up past the `md` breakpoint; hamburger buttons got
  `aria-label`/`aria-expanded`/`aria-controls`.
- **Home page responsiveness** (`home.blade.php`, `HomeWeatherHero.js`): the
  Popular Mountains carousel used desktop-sized transforms
  (`-200% - 4rem`) anchored at `right-0`, which threw the front card entirely
  off-screen on phones — transforms are now breakpoint-aware (2 visible cards
  under 640px, 3 above) and reposition on breakpoint change; the SUMORROW hero
  title jumped to a fixed 140px at `sm`, overflowing 640–770px viewports (now
  `text-[min(10vw,140px)]`); the hero weather widget is slightly smaller on
  phones; stray `ml-2`/`mr-2` on the stacked search form caused an 8px overflow
  (now `sm:`-scoped).
- **Home mobile polish (follow-up pass)**: tightened the phone-size vertical
  rhythm (hero 500px, section gaps `mb-20`/`mt-24`/`gap-6` scale back up at
  `sm`/`lg`), the About heading's forced `<br>`s now only apply from `sm` up
  (they fought natural wrapping on phones — note the space after each `<br>` is
  load-bearing), search-bar/community paddings and borders slimmed on mobile,
  carousel container shortened to `h-[330px]` on phones, and the weather
  widget's `rounded-tr-3xl` now matches the hero's `2rem` corner radius.
- **Home images now always come from the local catalog**: the Community
  showcase cards' hardcoded Unsplash stock photo (which fell back to
  `placeholder.svg` whenever the external host was slow/blocked) is replaced
  by catalog images passed as `$communityImages`; and the popular/peaks
  showcases now filter out mountains whose cover resolves to the
  `default-mountain.jpg` fallback (3 catalog rows have no local file). The
  `mountains.home` day-long cache gained a `has_real_image` flag — run
  `php artisan cache:clear` after deploying this. New
  `HomeControllerTest::test_home_only_showcases_mountains_with_real_images`
  locks the behavior in.
- **Root cause of "home still shows fallbacks in the browser": cache-poisoned
  absolute URLs.** `mountains.home` cached `asset()` output, so whoever built
  the cache first (a CLI render → `http://localhost`, no port) baked their
  origin into every image URL for a day; browsers on `localhost:8000` then
  404'd against Apache on port 80 and every card fell back to the placeholder.
  The cache now stores raw disk paths only (`image_raw`) and
  `HomeController::resolveImageUrl()` builds URLs per request. Hero images
  stay static from `public/images/hero`. Regression test:
  `test_home_image_urls_follow_the_request_host_not_the_cache_builder`.
  Rule of thumb going forward: never cache `asset()`/`url()` output.
- **Mountain-detail weather semantics** (`MountainWeatherForecast.js`,
  `WeatherController::mockForecastData`, `explore/show.blade.php`): the hero
  card keeps showing today's *current* weather; the forecast section now shows
  the **3 days after today** (entries dated today are filtered out before
  grouping — previously today appeared as the first forecast card). Daily
  icon/description now comes from the entry nearest midday instead of the 00:00
  slot (which always carried a night icon). The backend mock and the JS fetch
  fallback both emit tomorrow..+3 to match, and the mock's test now asserts the
  exact 3 dates. The forecast "Loading" placeholder used `col-span-3` inside a
  1-column mobile grid (now `md:col-span-3`).

**How to verify**

- `php artisan test` — 229 passing. `npm run build` — succeeds.
- In a narrow viewport (<640px): open the hamburger menu (all links incl.
  Community/Profile work, menu closes on outside tap/Escape), scroll the home
  page (hero title fits, carousel front card stays on-screen, prev/next works).
- On a mountain detail page: hero card shows current conditions; the three
  forecast cards are dated tomorrow, +2, +3 — never today.

## 2026-07-02 (later) — branch: `main`

By: Claude Code

**What changed**

- Found and reverted a broken uncommitted working-tree change that had mechanically
  converted every `class="..."` attribute across all 45 `resources/views/**/*.blade.php`
  files into `@class([...])`, including cases where the naive whitespace-split conversion
  mangled Blade expressions into invalid PHP array literals. `git checkout --
  resources/views/` restored the clean state; the only legitimate `@class()` usage
  (3 conditional nav-link states in `layouts/admin.blade.php`) was preserved.
- Extracted every inline `<script>`/`<style>` block out of Blade views into
  `resources/js/features/*.js` and `resources/css/app.css` (+ new `weather-icons.css`
  partial), following the existing `ExploreSearch.js`-style class pattern and the
  existing `data-*` attribute convention (extended to JSON payloads via
  `{{ json_encode($x) }}` + `JSON.parse(el.dataset.x)` — never `@json()` in an HTML
  attribute, it breaks on embedded quotes). Only two `<script>` tags remain anywhere in
  `resources/views`: the emoji-picker and Alpine.js third-party CDN loads.
- Along the way: fixed a live double-submit bug on `explore.blade.php` (its inline
  debounce script and `ExploreSearch.js` were both submitting the same search input),
  and dropped several confirmed-dead functions (`toggleLike`, `toggleSave`,
  `filterByTag`, `window.clearImagePreview`) instead of relocating them.
- Note: the Giphy API key (`data-giphy-key` on the composer form) is exposed
  client-side — this predates the refactor (it was already interpolated into inline JS
  before), just relocated. Worth a follow-up if the Giphy integration is revisited.

**How to verify**

- `npm run build` — succeeds.
- `php artisan test` — 228 passing, unchanged.
- Manually exercise: auth pages (password toggle), navbar hamburger, explore page
  search/filter drawer, community tabs/modals/follow/like/save/comment-modal, gear
  add/edit/filter, profile tabs/avatar upload, home page weather widget + carousel,
  mountain detail page weather forecast, and the post composer (image upload, emoji,
  GIF search/select) in a browser — no automated JS test coverage exists for these.

## 2026-07-02 — branch: `main`

By: Claude Code

**What changed**

- Removed the Forum Moderation admin feature (route, controller method, view,
  sidebar link, tests) — dropped for business reasons.
- Reworked the admin dashboard: forum stats replaced with real user/mountain
  stats (total users, new this week, total mountains, active routes) and a
  recent-users table; removed the hardcoded "Pending Reports" placeholder.
- User Updates page is now a working management screen: change a user's role
  (user/admin), delete a user, and a functional Export CSV — with guards so an
  admin cannot change their own role or delete their own account. User list is
  paginated.
- Mountain Data page now supports full CRUD: add/edit/delete mountains via new
  form views (`resources/views/admin/mountains/`), with validation (difficulty
  enum, province exists, numeric bounds) and `closed_since` auto-cleared when a
  route is marked open. Mountain list is paginated.
- Admin layout now renders `success`/`error` flash messages.
- New `RedirectIfAdmin` middleware (alias `redirect.admin`) wraps all
  user-facing web routes: a logged-in admin hitting any user page (home,
  explore, community, profile, login, Google OAuth, …) is redirected to the
  admin dashboard. Logout and `/admin/*` stay outside the wrapper. This also
  fixes admins with an existing session landing on `/home` after clicking
  Google login (the `guest` middleware fallback bypassed the role check in the
  OAuth callback).
- Rewrote `tests/Feature/AdminControllerTest.php` for the new surface (41
  tests) and updated `ProfileControllerTest` (admins are now redirected away
  from `/profile`).
- Admin UI redesign ("Basecamp"): dark `deep-midnight` sidebar with topographic
  contour texture, grouped nav (Overview / Management), Google Fonts now loaded
  on admin pages (they previously fell back to system fonts), dismissible flash
  messages, stat cards with icons, quick-actions panel on the dashboard,
  server-side search on the user and mountain tables (`?q=`, kept across
  pagination), difficulty shown as a 4-step scale, status dot badges,
  sectioned mountain form with inline field errors and a Closed-since field
  that only appears when the route is marked Closed. `npm run build` re-run.

**How to verify**

- `php artisan test` — full suite: 228 passing.
- Log in as an admin → `/admin/dashboard`, `/admin/user-updates`,
  `/admin/mountain-data`; try role change, user delete, CSV export, and
  mountain create/edit/delete.

## 2026-06-23 — branch: `chore/tests-security-image-fallbacks`

By: henry + Claude Code

**What changed**

- Added feature tests for previously untested controllers: Community, Gear,
  ProfilePost, Weather, and the API User endpoints, plus a `SecurityTest`.
- Security hardening (kept abstract): login rate limiting, a mass-assignment
  guard on the user role, removal of a TLS-verification bypass, and reduced error
  leakage on social login.
- Image fallbacks: added `public/images/placeholder.svg` and `onerror` fallbacks
  to mountain, hero, avatar, and community images across the home, explore, and
  community views.
- Removed a dead routing branch that referenced a non-existent route.

**Project state**

- Full test suite: **206 passing** (`php artisan test`).
- Environment bootstrapped: `composer install`, `npm install` + `npm run build`,
  `.env` created, MySQL databases `sumorrow` and `sumorrow_test` created and
  migrated/seeded.

**How to verify**

- `php artisan test`
- `composer run dev`, then load `/home` and `/explore` — broken image sources now
  fall back to the placeholder.

**Open / next items**

- Optional: extend the `onerror` fallback to the remaining avatar spots (feed,
  sidebar, navbar, post detail) for full coverage.
- Optional: revisit the email-verification bypass in `User::hasVerifiedEmail()`.
