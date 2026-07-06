# Agent Handoff Log

Running log of work done by developers and AI agents, newest first.

- **Read this at the start of a session** to learn what changed recently. A
  `SessionStart` hook also injects this file automatically.
- **Append a new entry before you end a session** so the next person or agent has
  context. Keep entries short: date, branch, key changes, and how to verify.
- Do **not** put exploit-level security detail here. Summarize sensitive fixes
  abstractly (see [CHANGELOG.md](CHANGELOG.md)).

---

## 2026-07-06 — branch: `main` (uncommitted) — Forum sidebar scoping + guest avatar

By: Claude Code

**What changed**

- Forum Leaders and Popular Tags (`CommunityController::index`, `PostController::index`) now count global forum posts only: posts with `community_id` set (My Community posts) are excluded via `whereNull('community_id')`.
- Guest visitors on the forum composer (`resources/views/community/components/feed.blade.php`) now see `images/community/profile-blank.png` instead of the dummy mountain avatar; logged-in users without an avatar keep the existing fallback.
- New `ForumPostSeeder` (registered in `DatabaseSeeder`): 3 demo users + 8 tagged global forum posts with likes and a topic-matched image each (bundled public assets), idempotent via `firstOrCreate`.
- Default avatar unified: every avatar fallback in community views (feed composer, post cards, post detail, comments, sidebar leaders) and the home community cards now uses `images/community/profile-blank.jpg` instead of the rinjani mountain photo / initials.
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
