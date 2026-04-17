# Mountain API — Entity Relationship Design

## Entities

### PROVINCE
```
PROVINCE {
  int id PK
  string name UNIQUE
}
```

### MOUNTAIN
```
MOUNTAIN {
  int id PK
  int province_id FK → PROVINCE.id
  string name
  int elevation_masl       -- absolute height of the peak
  int length_km            -- distance from basecamp to the peak (route-specific)
  int elevation_gain_m     -- elevation gain for this route
  string coordinates       -- coordinates of the peak itself
  text description
  boolean is_active        -- default false
  date closed_since        -- nullable
  enum difficulty          -- (easy, moderate, hard, strenuous)
  float avg_rating         -- denormalized cache, updated on each rating
}
```

> **Note:** If a mountain has multiple access routes, each route is represented as a separate MOUNTAIN row with a different name (e.g., "Semeru via Ranu Pane", "Semeru via Tumpang"). Route-specific fields (`length_km`, `elevation_gain_m`, `elevation_masl`) reflect the attributes of that particular route.

### MOUNTAIN_IMAGE
```
MOUNTAIN_IMAGE {
  int id PK
  int mountain_id FK → MOUNTAIN.id
  string image_url
  int position
  boolean is_cover
  timestamp uploaded_at
  UNIQUE (mountain_id, position)
}
```

### BASECAMP
```
BASECAMP {
  int id PK
  int mountain_id FK → MOUNTAIN.id
  string name              -- e.g., "Candi Cetho", "Ranu Pane"
}
```

> Basecamp now stores only the entry-point name. All route metrics (length, elevation gain) are stored on the MOUNTAIN row itself.

### MOUNTAIN_RATING
```
MOUNTAIN_RATING {
  int id PK
  uuid user_id FK → USER.id
  int mountain_id FK → MOUNTAIN.id
  int score                -- CHECK (score BETWEEN 1 AND 5)
  text review              -- nullable
  timestamp created_at
  UNIQUE (user_id, mountain_id)
}
```

### COMMENT
```
COMMENT {
  int id PK
  uuid user_id FK → USER.id
  int mountain_id FK → MOUNTAIN.id
  text content
  timestamp created_at
  timestamp updated_at
}
```

### USER
```
USER {
  uuid id PK
  string username UNIQUE
  string email UNIQUE
  string password_hash
  string avatar_url        -- nullable
  timestamp created_at
}
```

### POST
```
POST {
  int id PK
  uuid author_id FK → USER.id
  string title
  text body                -- full-text searchable
  timestamp created_at
  timestamp updated_at
}
```

### POST_IMAGE
```
POST_IMAGE {
  int id PK
  int post_id FK → POST.id
  string image_url
  int position
  timestamp uploaded_at
  UNIQUE (post_id, position)
}
```

### POST_REPLY
```
POST_REPLY {
  int id PK
  int post_id FK → POST.id
  uuid author_id FK → USER.id
  int parent_reply_id FK → POST_REPLY.id  -- nullable (null = top-level reply)
  text content
  timestamp created_at
  timestamp updated_at
}
```

### POST_TAG
```
POST_TAG {
  int id PK
  int post_id FK → POST.id
  string keyword           -- lowercased on insert
  UNIQUE (post_id, keyword)
}
```

---

## Relationships

### Administrative hierarchy
- PROVINCE → MOUNTAIN (one-to-many): one province has many mountains

### Mountain core
- MOUNTAIN → MOUNTAIN_IMAGE (one-to-many): one mountain has many images
- MOUNTAIN → BASECAMP (one-to-many): one mountain has many basecamps (entry points)

### Mountain interactions
- MOUNTAIN → MOUNTAIN_RATING (one-to-many): one mountain receives many ratings
- USER → MOUNTAIN_RATING (one-to-many): one user can rate many mountains
- MOUNTAIN → COMMENT (one-to-many): one mountain receives many comments
- USER → COMMENT (one-to-many): one user can write many comments

### Forum
- USER → POST (one-to-many): one user can author many posts
- POST → POST_IMAGE (one-to-many): one post has many images
- POST → POST_REPLY (one-to-many): one post has many replies
- POST → POST_TAG (one-to-many): one post has many tags
- USER → POST_REPLY (one-to-many): one user can write many replies
- POST_REPLY → POST_REPLY (one-to-many): one reply can have many nested replies (self-referencing via parent_reply_id)

---

## Constraints

### Unique constraints
- MOUNTAIN_RATING → UNIQUE (user_id, mountain_id): one user can only rate each mountain once
- MOUNTAIN_IMAGE → UNIQUE (mountain_id, position): no two images on the same mountain share the same position
- POST_IMAGE → UNIQUE (post_id, position): no two images on the same post share the same position
- POST_TAG → UNIQUE (post_id, keyword): no duplicate tags on the same post
- USER.username → UNIQUE: no two users share the same username
- USER.email → UNIQUE: no two users share the same email
- PROVINCE.name → UNIQUE: no duplicate province names

### Check constraints
- MOUNTAIN_RATING.score → CHECK (score BETWEEN 1 AND 5): score must be a value from 1 to 5
- MOUNTAIN.difficulty → CHECK (difficulty IN ('easy', 'moderate', 'hard', 'strenuous')): only valid difficulty levels allowed

### Nullable fields
- USER.avatar_url → nullable: user may not have a profile photo
- MOUNTAIN.closed_since → nullable: only set when the mountain is closed
- MOUNTAIN_RATING.review → nullable: user may submit a score without a written review
- POST_REPLY.parent_reply_id → nullable: null means it is a top-level reply directly under the post; a value means it is a nested reply to another reply

### Default values
- MOUNTAIN.is_active → default false: mountains are assumed inactive unless stated otherwise

---

## Indexes

### Foreign key indexes
These should be created on every FK column since they are frequently used in JOIN operations:

- MOUNTAIN (province_id)
- MOUNTAIN_IMAGE (mountain_id)
- BASECAMP (mountain_id)
- MOUNTAIN_RATING (mountain_id), MOUNTAIN_RATING (user_id)
- COMMENT (mountain_id), COMMENT (user_id)
- POST (author_id)
- POST_IMAGE (post_id)
- POST_REPLY (post_id), POST_REPLY (author_id), POST_REPLY (parent_reply_id)
- POST_TAG (post_id)

### Filter indexes
Columns frequently used in WHERE clauses:

- MOUNTAIN (is_active): filter active/inactive volcanoes
- MOUNTAIN (difficulty): filter by difficulty level
- MOUNTAIN (avg_rating): sort mountains by rating
- MOUNTAIN (elevation_masl): sort/filter by height
- MOUNTAIN (province_id): filter mountains by province
- POST (created_at): sort forum posts by newest

### Full-text search indexes
For keyword search functionality:

- POST (title, body): search forum posts by keyword — use a GIN index in PostgreSQL
- POST_TAG (keyword): search posts by tag
- MOUNTAIN (name): search mountains by name
- MOUNTAIN (description): search mountains by description content

---

## Design Notes

### avg_rating maintenance strategy
- MOUNTAIN.avg_rating is a denormalized cache — it does not auto-update. You need to keep it in sync using one of these approaches:
  - **Database trigger:** automatically recalculate avg_rating on every INSERT, UPDATE, or DELETE on MOUNTAIN_RATING

### POST_REPLY depth limit
- Cap at 2 levels deep (reply to post → reply to reply)

### Multiple routes per mountain
- Each distinct hiking route is stored as its own MOUNTAIN row
- Use a consistent naming convention to group variants, e.g.: `"Semeru via Ranu Pane"`, `"Semeru via Tumpang"`
- Route-specific data (`length_km`, `elevation_gain_m`, `elevation_masl`) lives on each MOUNTAIN row
- The BASECAMP row linked to each MOUNTAIN names the entry point for that route
