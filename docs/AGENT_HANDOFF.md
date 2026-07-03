# Agent Handoff Log

Running log of work done by developers and AI agents, newest first.

- **Read this at the start of a session** to learn what changed recently. A
  `SessionStart` hook also injects this file automatically.
- **Append a new entry before you end a session** so the next person or agent has
  context. Keep entries short: date, branch, key changes, and how to verify.
- Do **not** put exploit-level security detail here. Summarize sensitive fixes
  abstractly (see [CHANGELOG.md](CHANGELOG.md)).

---

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
