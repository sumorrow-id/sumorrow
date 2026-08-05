# Changelog

Notable changes to Sumorrow. Security-sensitive details are intentionally kept
high level so they cannot be used as a guide to attack older deployments.

## [Unreleased]

### Added

- Hikers can delete their own mountain reviews and their own forum replies.
- Mountain pages page through all reviews via a "See more reviews" button
  instead of stopping at the first five.
- Public profiles (`/users/{id}`), reachable by clicking a reviewer on a
  mountain page. They show the hiker's achievements, recent forum posts, summit
  logs, and reviews — never their email address.
- Achievement descriptions are translated when the interface language changes.
- Feature tests for the community, gear, profile-post, weather, and API user
  controllers, plus a small security regression suite.
- A shared image fallback asset (`public/images/placeholder.svg`). Mountain,
  hero, post, community, and avatar images now fall back to this placeholder at
  runtime when their source fails to load, instead of showing a broken image.

### Changed

- Hardened authentication and request handling. A number of security
  improvements were made to reduce abuse of public-facing endpoints and to
  tighten how user input is trusted. Specifics are intentionally omitted.
- The weather integration is now more resilient to upstream/network failures and
  degrades gracefully to placeholder data.

### Fixed

- Posts written inside a community — public or private — no longer appear in the
  author's profile "Posts" tab. They stay on the community page, where the
  existing membership checks apply.
- The "First Summit" achievement now requires an actual summit log (a forum post
  no longer counts) and unlocks the moment the log is posted.
- Adding, editing, or deleting gear keeps you on the Gear tab of your profile
  instead of bouncing back to Posts.
- Various minor bug fixes and cleanup: removed a dead routing branch that
  referenced a non-existent route, replaced a defunct external placeholder image
  source, and reduced information disclosure on a failed social-login attempt.

### Notes

- No database schema changes were introduced in this set of changes; existing
  migrations and seed data are unaffected.
