PROVINCE {
  uuid    id    PK
  string  name  UNIQUE
}

REGENCY {
  uuid    id          PK
  uuid    province_id FK → PROVINCE.id
  string  name
  enum    type        (regency, city)
  UNIQUE (province_id, name)
}

DISTRICT {
  uuid    id         PK
  uuid    regency_id FK → REGENCY.id
  string  name
  UNIQUE (regency_id, name)
}

VILLAGE {
  uuid    id          PK
  uuid    district_id FK → DISTRICT.id
  string  name
  UNIQUE (district_id, name)
}

MOUNTAIN
uuid        id                   PK
string      name                 UNIQUE
uuid        village_id           FK → VILLAGE.id
int         elevation_masl
string      coordinates
text        description
boolean     is_open
boolean     is_active
date        closed_since         nullable
float       length_km
float       elevation_gain_m
float       est_duration_minutes
enum        difficulty           (easy, moderate, hard, strenuous)
float       avg_rating           denormalized cache, updated on each rating

MOUNTAIN_IMAGE
uuid        id           PK
uuid        mountain_id  FK → MOUNTAIN.id
string      image_url
int         position
boolean     is_cover
timestamp   uploaded_at
UNIQUE (mountain_id, position)

BASECAMP
uuid        id           PK
uuid        mountain_id  FK → MOUNTAIN.id
string      name
uuid  	    village_id   FK → VILLAGE.id
string      address
string      coordinates
string      contact      nullable — phone/WhatsApp

MOUNTAIN_RATING
uuid        id           PK
uuid        user_id      FK → USER.id
uuid        mountain_id  FK → MOUNTAIN.id
int         score        CHECK (score BETWEEN 1 AND 5)
text        review       nullable
timestamp   created_at
UNIQUE (user_id, mountain_id)

COMMENT
uuid        id           PK
uuid        user_id      FK → USER.id
uuid        mountain_id  FK → MOUNTAIN.id
text        content
timestamp   created_at
timestamp   updated_at


USER
uuid        id            PK
string      username      UNIQUE
string      email         UNIQUE
string      password_hash
string      avatar_url    nullable
boolean     is_active     default true
timestamp   created_at


POST
uuid        id         PK
uuid        author_id  FK → USER.id
string      title
text        body       full-text searchable
timestamp   created_at
timestamp   updated_at

POST_IMAGE
uuid        id         PK
uuid        post_id    FK → POST.id
string      image_url
int         position
timestamp   uploaded_at
UNIQUE (post_id, position)

POST_REPLY
uuid        id              PK
uuid        post_id         FK → POST.id
uuid        author_id       FK → USER.id
uuid        parent_reply_id FK → POST_REPLY.id  nullable (null = top-level reply)
text        content
timestamp   created_at
timestamp   updated_at

POST_TAG
uuid        id       PK
uuid        post_id  FK → POST.id
string      keyword  lowercased on insert
UNIQUE (post_id, keyword)

Administrative hierarchy
- PROVINCE → REGENCY (one-to-many): one province has many regencies/cities
- REGENCY → DISTRICT (one-to-many): one regency/city has many districts
- DISTRICT → VILLAGE (one-to-many): one district has many villages

Mountain core
- VILLAGE → MOUNTAIN (one-to-many): one village locates many mountains
- MOUNTAIN → MOUNTAIN_IMAGE (one-to-many): one mountain has many images
- MOUNTAIN → BASECAMP (one-to-many): one mountain has many basecamps
- VILLAGE → BASECAMP (one-to-many): one village locates many basecamps

Mountain interactions
- MOUNTAIN → MOUNTAIN_RATING (one-to-many): one mountain receives many ratings
- USER → MOUNTAIN_RATING (one-to-many): one user can rate many mountains
- MOUNTAIN → COMMENT (one-to-many): one mountain receives many comments
- USER → COMMENT (one-to-many): one user can write many comments

Forum
- USER → POST (one-to-many): one user can author many posts
- POST → POST_IMAGE (one-to-many): one post has many images
- POST → POST_REPLY (one-to-many): one post has many replies
- POST → POST_TAG (one-to-many): one post has many tags
- USER → POST_REPLY (one-to-many): one user can write many replies
- POST_REPLY → POST_REPLY (one-to-many): one reply can have many nested replies (self-referencing via parent_reply_id)

---

Unique constraints:
- MOUNTAIN_RATING → UNIQUE (user_id, mountain_id): one user can only rate each mountain once
- MOUNTAIN_IMAGE → UNIQUE (mountain_id, position): no two images on the same mountain share the same position
- POST_IMAGE → UNIQUE (post_id, position): no two images on the same post share the same position
- POST_TAG → UNIQUE (post_id, keyword): no duplicate tags on the same post
- USER.username → UNIQUE: no two users share the same username
- USER.email → UNIQUE: no two users share the same email
- PROVINCE.name → UNIQUE: no duplicate province names
- REGENCY → UNIQUE (province_id, name): no duplicate regency names within the same province
- DISTRICT → UNIQUE (regency_id, name): no duplicate district names within the same regency
- VILLAGE → UNIQUE (district_id, name): no duplicate village names within the same district

Check constraints
- MOUNTAIN_RATING.score → CHECK (score BETWEEN 1 AND 5): score must be a value from 1 to 5
- MOUNTAIN.difficulty → CHECK (difficulty IN ('easy', 'moderate', 'hard', 'strenuous')): only valid difficulty levels allowed
- REGENCY.type → CHECK (type IN ('regency', 'city')): only valid regency types allowed

Nullable fields
- USER.avatar_url → nullable: user may not have a profile photo
- MOUNTAIN.closed_since → nullable: only set when the mountain is closed
- MOUNTAIN_RATING.review → nullable: user may submit a score without a written review
- BASECAMP.contact → nullable: not all basecamps have a listed contact
- POST_REPLY.parent_reply_id → nullable: null means it is a top-level reply directly under the post; a value means it is a nested reply to another reply

Default values
- USER.is_active → default true: all newly registered users are active by default
- MOUNTAIN.is_open → default true: mountains are assumed open unless explicitly closed
- MOUNTAIN.is_active → default true: mountains are assumed volcanically active unless stated otherwise

---

Indexes for performance
Foreign key indexes — these should be created on every FK column since they are frequently used in JOIN operations:
- MOUNTAIN (village_id)
- MOUNTAIN_IMAGE (mountain_id)
- BASECAMP (mountain_id), BASECAMP (village_id)
- MOUNTAIN_RATING (mountain_id), MOUNTAIN_RATING (user_id)
- COMMENT (mountain_id), COMMENT (user_id)
- POST (author_id)
- POST_IMAGE (post_id)
- POST_REPLY (post_id), POST_REPLY (author_id), POST_REPLY (parent_reply_id)
- POST_TAG (post_id)
- REGENCY (province_id)
- DISTRICT (regency_id)
- VILLAGE (district_id)

Filter indexes — columns frequently used in WHERE clauses:
- MOUNTAIN (is_open): filter open/closed mountains
- MOUNTAIN (is_active): filter active/inactive volcanoes
- MOUNTAIN (difficulty): filter by difficulty level
- MOUNTAIN (avg_rating): sort mountains by rating
- MOUNTAIN (elevation_masl): sort/filter by height
- USER (is_active): filter banned/active users
- POST (created_at): sort forum posts by newest

Full-text search indexes — for keyword search functionality:
- POST (title, body): search forum posts by keyword — use a GIN index in PostgreSQL
- POST_TAG (keyword): search posts by tag
- MOUNTAIN (name): search mountains by name
- MOUNTAIN (description): search mountains by description content

avg_rating maintenance strategy:
- MOUNTAIN.avg_rating is a denormalized cache — it does not auto-update. You need to keep it in sync using one of these approaches:
- Database trigger: automatically recalculate avg_rating on every INSERT, UPDATE, or DELETE on MOUNTAIN_RATING

POST_REPLY depth limit:
- Cap at 2 levels deep (reply to post → reply to reply)

