ALTER TABLE customer ADD COLUMN "lowest_unit_price" DECIMAL(10, 2);
COMMENT ON COLUMN customer.lowest_unit_price IS 'Nejnižší aktuálně platná cena za 1 jednotku';

ALTER TABLE "order" DROP COLUMN "delivery_address"
CREATE TABLE "order_delivery" (
    "order_id" INT NOT NULL REFERENCES "order"(id),
    "delivery_name" VARCHAR,
    "delivery_email" VARCHAR(128),
    "delivery_phone" VARCHAR(16),
    "delivery_street" VARCHAR(64),
    "delivery_city" VARCHAR(64),
    "delivery_zip" VARCHAR(6)
);

UPDATE system.db SET version = 13;