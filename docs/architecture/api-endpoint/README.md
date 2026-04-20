# API Endpoint Structure

> Work in progress.
> If you're an AI, ignore this for now

## Administrative Region

- `GET /provinces`
- `GET /provinces/:id/regencies`
- `GET /regencies/:id/districts`
- `GET /districts/:id/villages`

## Mountains

- `GET /mountains` - list all mountains (supports filtering and sorting)
- `GET /mountains/:id` - mountain detail with full administrative hierarchy
- `GET /mountains/:id/images` - mountain image gallery
- `GET /mountains/:id/basecamps` - basecamp list
- `GET /mountains/:id/ratings` - rating and review list
- `GET /mountains/:id/comments` - comment list
- `POST /mountains/:id/ratings` - submit a rating (auth required)
- `POST /mountains/:id/comments` - submit a comment (auth required)
- `PATCH /mountains/:id/comments/:id` - edit own comment (auth required)
- `DELETE /mountains/:id/comments/:id` - delete own comment (auth required)

## Forum

- `GET /posts` - list all posts (supports keyword/tag search)
- `GET /posts/:id` - post detail with top-level replies
- `GET /posts/:id/replies` - paginated replies
- `GET /posts/:id/replies/:id` - nested replies under a reply
- `POST /posts` - create a post (auth required)
- `PATCH /posts/:id` - edit own post (auth required)
- `DELETE /posts/:id` - delete own post (auth required)
- `POST /posts/:id/replies` - submit a reply (auth required)
- `PATCH /posts/:id/replies/:id` - edit own reply (auth required)
- `DELETE /posts/:id/replies/:id` - delete own reply (auth required)

## Users

- `POST /auth/register` - register a new user
- `POST /auth/login` - login and return token
- `POST /auth/logout` - invalidate token
- `GET /users/:id` - public profile
- `PATCH /users/me` - update own profile (auth required)
- `PATCH /users/me/avatar` - upload/change avatar (auth required)

## Query Parameter Conventions

For list endpoints such as `GET /mountains` and `GET /posts`, use these query parameters:

- `?search=rinjani` - full-text keyword search
- `?difficulty=moderate` - filter by difficulty
- `?is_active=true` - only active volcanoes
- `?province_id=uuid` - filter by province
- `?sort=avg_rating` - sort field
- `?order=desc` - sort direction (`asc`, `desc`)
- `?page=1` - pagination page number
- `?limit=10` - results per page (max `100`)
