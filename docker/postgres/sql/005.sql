
CREATE TABLE "category" (
  "id" SERIAL PRIMARY KEY,
  "parent_id" INT DEFAULT 0,
  "name" VARCHAR(150) NOT NULL,
  "url_slug" VARCHAR(300) UNIQUE NOT NULL,
  "description" VARCHAR,
  "active" BOOLEAN DEFAULT false
);

UPDATE system.db SET version = 5;