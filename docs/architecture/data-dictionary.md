# Data Dictionary

Internal reference for database tables, constraints, and relationships.

## Conventions

- PK: primary key
- FK: foreign key
- `uuid` keys use app-generated UUID values
- Timestamps use UTC unless stated otherwise

## Table Index

- `administrative_regions`
- `mountains`
- `basecamps`
- `mountain_images`
- `mountain_ratings`
- `comments`
- `users`
- `posts`
- `post_images`
- `post_replies`
- `post_tags`

## users

**Purpose:** Application users and forum authors.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| username | string | No | UNIQUE | login/display handle |
| email | string | No | UNIQUE | |
| password_hash | string | No | | hashed password only |
| avatar_url | string | Yes | | |
| created_at | timestamp | No | | |

## administrative_regions

**Purpose:** Hierarchical place metadata for mountain regions.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| province | string | No | | |
| regency_city | string | No | | |
| district | string | No | | |
| village | string | No | | |
| created_at | timestamp | Yes | | |
| updated_at | timestamp | Yes | | |

## mountains

**Purpose:** Core mountain records.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| name | string | No | | |
| region_id | uuid | No | FK -> administrative_regions.id | |
| elevation_masl | int | No | | meters above sea level |
| coordinates | string | No | | coordinate text |
| description | text | Yes | | |
| image_url | string | Yes | | main image |
| is_open | boolean | No | | |
| is_active | boolean | No | | |
| closed_since | date | Yes | | |
| avg_rating | float | Yes | | derived/cache value |
| created_at | timestamp | Yes | | |
| updated_at | timestamp | Yes | | |

## basecamps

**Purpose:** Access points/basecamp records per mountain.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| mountain_id | uuid | No | FK -> mountains.id | |
| name | string | No | | |
| location | string | No | | |
| coordinates | string | Yes | | |
| created_at | timestamp | Yes | | |
| updated_at | timestamp | Yes | | |

## mountain_images

**Purpose:** Gallery images for each mountain.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| mountain_id | uuid | No | FK -> mountains.id | |
| image_url | string | No | | |
| position | int | No | UNIQUE (mountain_id, position) | display order |
| created_at | timestamp | Yes | | |
| updated_at | timestamp | Yes | | |

## mountain_ratings

**Purpose:** User score submissions per mountain.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| user_id | uuid | No | FK -> users.id | |
| mountain_id | uuid | No | FK -> mountains.id | |
| score | int | No | CHECK score between 1 and 5 | |
| created_at | timestamp | No | | |

**Additional constraints:**

- UNIQUE (`user_id`, `mountain_id`) so a user can rate one mountain only once.

## comments

**Purpose:** User comments on mountains.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| user_id | uuid | No | FK -> users.id | |
| mountain_id | uuid | No | FK -> mountains.id | |
| content | text | No | | |
| created_at | timestamp | No | | |
| updated_at | timestamp | No | | |

## posts

**Purpose:** Forum posts authored by users.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| author_id | uuid | No | FK -> users.id | |
| body | text | No | full-text index (engine-dependent) | searchable content |
| created_at | timestamp | No | | |
| updated_at | timestamp | No | | |

## post_images

**Purpose:** Multiple ordered images per post.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| post_id | uuid | No | FK -> posts.id | |
| image_url | string | No | | |
| position | int | No | UNIQUE (post_id, position) | display order |
| uploaded_at | timestamp | No | | |

## post_replies

**Purpose:** Threaded replies with optional self-reference parent.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| post_id | uuid | No | FK -> posts.id | |
| author_id | uuid | No | FK -> users.id | |
| parent_reply_id | uuid | Yes | FK -> post_replies.id | null for top-level reply |
| content | text | No | | |
| created_at | timestamp | No | | |
| updated_at | timestamp | No | | |

## post_tags

**Purpose:** Tag/keyword list for filtering and discovery.

| Column | Type | Null | Key/Constraint | Notes |
|---|---|---|---|---|
| id | uuid | No | PK | |
| post_id | uuid | No | FK -> posts.id | |
| keyword | string | No | UNIQUE (post_id, keyword) | normalized lowercase preferred |

## Relationship Summary

- `administrative_regions` 1 -> n `mountains`
- `mountains` 1 -> n `basecamps`
- `mountains` 1 -> n `mountain_images`
- `users` 1 -> n `mountain_ratings`
- `mountains` 1 -> n `mountain_ratings`
- `users` 1 -> n `comments`
- `mountains` 1 -> n `comments`
- `users` 1 -> n `posts`
- `posts` 1 -> n `post_images`
- `posts` 1 -> n `post_replies`
- `post_replies` 1 -> n `post_replies` (self-reference by `parent_reply_id`)
- `posts` 1 -> n `post_tags`

## Update Checklist

When schema changes:

1. Add a new migration (do not edit old shared migrations).
2. Update this file for new/changed columns, indexes, or constraints.
3. Update ERD source files in `docs/architecture/erd/`.
4. Mention documentation updates in the pull request description.

