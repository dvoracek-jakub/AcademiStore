ALTER TABLE customer ADD COLUMN "firstname" VARCHAR(64);
ALTER TABLE customer ADD COLUMN "lastname" VARCHAR(64);
ALTER TABLE customer ADD COLUMN "phone" VARCHAR(16);

CREATE TABLE "address" (
	"id" SERIAL PRIMARY KEY,
	"customer_id" INT NOT NULL REFERENCES customer(id),
	"street" VARCHAR(64) NOT NULL,
	"city" VARCHAR(64) NOT NULL,
	"zip" VARCHAR(6)
);

UPDATE system.db SET version = 12;