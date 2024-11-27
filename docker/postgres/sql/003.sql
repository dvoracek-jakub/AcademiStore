
CREATE TABLE "product" (
  "id" SERIAL PRIMARY KEY ,
  "sku" VARCHAR(50) UNIQUE NOT NULL,
  "name" VARCHAR(150) NOT NULL,
  "url_slug" VARCHAR(300) UNIQUE NOT NULL,
  "description_short" VARCHAR,
  "description_long" VARCHAR,
  "price" DECIMAL(10, 2) DEFAULT 0,
  "stock" INT DEFAULT 0,
  "active" BOOLEAN DEFAULT false,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


UPDATE system.db SET version = 3;