
ALTER TABLE "product" ADD COLUMN image_name VARCHAR DEFAULT '';

UPDATE system.db SET version = 4;