# ERD Documentation

This folder stores Entity Relationship Diagram (ERD) artifacts for internal architecture work.

## Purpose

- Keep a visual map of table relationships
- Align application models with database migrations
- Help code reviews validate schema changes

## Suggested file structure

- `current.mmd` or `current.drawio`: editable source of truth
- `current.png`: exported image for quick preview
- `changelog.md`: optional notes for major relationship changes

## Team rules

- Update ERD when adding or changing tables/foreign keys/index constraints.
- In pull requests, include both migration files and ERD updates.
- Do not put production secrets or real data values in this folder.

## Current domain scope

- Administrative region and mountain entities
- User-generated comments and ratings
- Forum posts, images, tags, and threaded replies

