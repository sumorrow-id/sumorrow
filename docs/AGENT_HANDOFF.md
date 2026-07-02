# Agent Handoff Log

Running log of work done by developers and AI agents, newest first.

- **Read this at the start of a session** to learn what changed recently. A
  `SessionStart` hook also injects this file automatically.
- **Append a new entry before you end a session** so the next person or agent has
  context. Keep entries short: date, branch, key changes, and how to verify.
- Do **not** put exploit-level security detail here. Summarize sensitive fixes
  abstractly (see [CHANGELOG.md](CHANGELOG.md)).

---

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
