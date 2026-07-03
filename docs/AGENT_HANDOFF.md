# Agent Handoff Log

Running log of work done by developers and AI agents, newest first.

- **Read this at the start of a session** to learn what changed recently. A
  `SessionStart` hook also injects this file automatically.
- **Append a new entry before you end a session** so the next person or agent has
  context. Keep entries short: date, branch, key changes, and how to verify.
- Do **not** put exploit-level security detail here. Summarize sensitive fixes
  abstractly (see [CHANGELOG.md](CHANGELOG.md)).

---

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
