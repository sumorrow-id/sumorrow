# Sumorrow Public API — v1

Read-only HTTP API for the Sumorrow mountain catalog (provinces, mountains, mountain images, basecamps, mountain ratings).

- **Base URL:** `/api/v1`
- **Format:** JSON only (`Accept: application/json`)
- **Auth:** Required on every endpoint — Laravel Sanctum (session cookie for same-origin SPA, or `Authorization: Bearer <token>` for external clients). You must log in to the web app first to obtain a token.
- **Rate limit:** 30 requests/minute per authenticated user. Unauthenticated requests are stricter (10/min by IP) and will still hit 401 since auth is required.

> Write endpoints (POST/PATCH/DELETE) are intentionally not exposed yet. They are planned as admin-only in a future revision (see [Roadmap](#roadmap)).

---

## Authentication

All requests must carry one of:

- A Sanctum session cookie (same-origin SPA, after web login).
- A personal access token sent as a Bearer header:

```http
GET /api/v1/mountains HTTP/1.1
Host: sumorrow.test
Accept: application/json
Authorization: Bearer 1|abc123...
```

A 401 is returned for missing/invalid credentials:

```json
{ "message": "Unauthenticated.", "status": 401 }
```

> A user-facing token issuance flow (`POST /api/v1/auth/tokens`) is planned. For now, generate a test token via tinker:
>
> ```bash
> php artisan tinker
> > User::first()->createToken('local-test')->plainTextToken
> ```

## Rate Limiting

| Audience | Limit |
|---|---|
| Authenticated user | **30 requests / minute** (per user ID) |
| Unauthenticated | 10 requests / minute (per IP) — for the 401 response |

Every response includes:

- `X-RateLimit-Limit` — your bucket size
- `X-RateLimit-Remaining` — requests left in the current window
- `Retry-After` (only on 429) — seconds to wait before retrying

429 response shape:

```json
{ "message": "Too many requests. Slow down.", "status": 429, "retry_after": 38 }
```

## Response Envelopes

### List (paginated)

```json
{
  "data":  [ { "...": "..." } ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta":  { "current_page": 1, "per_page": 15, "total": 200, "last_page": 14, "from": 1, "to": 15, "path": "..." }
}
```

### Single resource

```json
{ "data": { "id": 1, "name": "Rinjani", "...": "..." } }
```

### Error envelope

```json
{ "message": "Resource not found.", "status": 404 }
```

Validation errors (422) include a per-field `errors` map:

```json
{
  "message": "The selected difficulty is invalid.",
  "status": 422,
  "errors": { "difficulty": ["The selected difficulty is invalid."] }
}
```

## Query Parameter Conventions

`GET /mountains` accepts:

| Param | Type | Notes |
|---|---|---|
| `search` | string ≤120 | LIKE match on `name` and `description` |
| `province_id` | int | Must exist in `provinces` |
| `difficulty` | enum | `easy` \| `moderate` \| `hard` \| `strenuous` |
| `is_active` | bool | `true` / `false` / `1` / `0` |
| `sort` | enum | `name` (default) \| `avg_rating` \| `elevation_masl` |
| `order` | enum | `asc` (default) \| `desc` |
| `page` | int ≥1 | Pagination page |
| `limit` | int 1..50 | Page size (default 15) |

`GET /provinces` accepts `search`, `page`, `limit` (default 50, max 50).

---

## Endpoints

### Provinces

#### `GET /provinces`
List provinces with `mountains_count`.

Example: `GET /api/v1/provinces?search=jawa`

```json
{
  "data": [
    { "id": 5, "name": "Jawa Barat", "mountains_count": 12 },
    { "id": 6, "name": "Jawa Tengah", "mountains_count": 21 }
  ],
  "links": { "...": "..." },
  "meta": { "current_page": 1, "per_page": 50, "total": 2, "...": "..." }
}
```

#### `GET /provinces/{id}`
Single province detail.

```json
{ "data": { "id": 5, "name": "Jawa Barat", "mountains_count": 12 } }
```

#### `GET /provinces/{id}/mountains`
Paginated list of mountains belonging to a province. Response uses the `MountainResource` schema described under `/mountains`.

---

### Mountains

#### `GET /mountains`
List mountains, filtered/sorted/paginated.

Example: `GET /api/v1/mountains?search=rinjani&sort=avg_rating&order=desc&limit=10`

```json
{
  "data": [
    {
      "id": 42,
      "name": "Rinjani",
      "province": { "id": 19, "name": "Nusa Tenggara Barat" },
      "difficulty": "hard",
      "elevation_masl": 3726,
      "avg_rating": 4.6,
      "is_active": true,
      "cover_image": "https://.../rinjani.jpg"
    }
  ],
  "links": { "...": "..." },
  "meta":  { "current_page": 1, "per_page": 10, "total": 1, "...": "..." }
}
```

#### `GET /mountains/{id}`
Full mountain detail (with counts).

```json
{
  "data": {
    "id": 42,
    "name": "Rinjani",
    "province": { "id": 19, "name": "Nusa Tenggara Barat" },
    "difficulty": "hard",
    "elevation_masl": 3726,
    "length_km": 28,
    "elevation_gain_m": 2500,
    "coordinates": "8 deg 25' 0\" S, 116 deg 28' 0\" E",
    "description": "...",
    "is_active": true,
    "closed_since": null,
    "avg_rating": 4.6,
    "cover_image": "https://.../rinjani.jpg",
    "counts": { "images": 6, "basecamps": 3, "ratings": 128 }
  }
}
```

#### `GET /mountains/{id}/images`
Image gallery, ordered by `position`. `image_url` falls back to `source_url` when no upload exists.

```json
{
  "data": [
    {
      "id": 101,
      "mountain_id": 42,
      "image_url": "https://.../rinjani-1.jpg",
      "source_url": "https://example.com/rinjani-original.jpg",
      "position": 0,
      "is_cover": true,
      "uploaded_at": "2026-04-12T08:30:00+00:00"
    }
  ]
}
```

#### `GET /mountains/{id}/basecamps`

```json
{
  "data": [
    { "id": 11, "mountain_id": 42, "name": "Sembalun" },
    { "id": 12, "mountain_id": 42, "name": "Senaru"   }
  ]
}
```

#### `GET /mountains/{id}/ratings`
Paginated ratings with the author's public username + avatar.

```json
{
  "data": [
    {
      "id": 901,
      "score": 5,
      "review": "Worth every step.",
      "user": { "username": "hikergirl", "avatar_url": "https://.../u.jpg" },
      "created_at": "2026-05-01T11:23:00+00:00"
    }
  ],
  "links": { "...": "..." },
  "meta":  { "...": "..." }
}
```

---

### Basecamps

#### `GET /basecamps/{id}`
Basecamp detail with parent mountain reference (intended for deep-link lookups).

```json
{
  "data": {
    "id": 11,
    "mountain_id": 42,
    "name": "Sembalun",
    "mountain": { "id": 42, "name": "Rinjani" }
  }
}
```

---

## Fallback

Any unmatched `/api/*` path returns JSON 404 instead of an HTML error page:

```json
{
  "message": "Endpoint not found.",
  "status": 404,
  "available_root": "https://sumorrow.test/api/v1",
  "docs": "https://sumorrow.test/docs/architecture/api-endpoint/README.md"
}
```

---

## Error Codes Reference

| Code | When |
|---|---|
| 401 | Missing / invalid Sanctum credentials |
| 404 | Resource not found, or unmatched API path (fallback) |
| 422 | Validation failure on query params |
| 429 | Rate limit exceeded — see `Retry-After` |

---

## Roadmap

These were sketched in the original draft and remain planned, scoped to admin-only writes in a future revision:

- `POST /mountains/{id}/ratings` — submit a rating
- `POST /mountains/{id}/comments`, `PATCH`, `DELETE` — comment lifecycle
- Forum endpoints — `/posts`, replies
- User endpoints — `/auth/register`, `/auth/login`, `/auth/logout`, `/users/:id`, `/users/me`
- Administrative region drilldown — `/provinces/{id}/regencies`, `/regencies/{id}/districts`, `/districts/{id}/villages`
- Personal access token issuance flow — `POST /api/v1/auth/tokens`
