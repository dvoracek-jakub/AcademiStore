ALTER TABLE product ADD COLUMN "lowest_unit_price" DECIMAL(10, 2);
COMMENT ON COLUMN product.lowest_unit_price IS 'The lowest currently valid price for 1 unit';

ALTER TABLE "order" DROP COLUMN "delivery_address"

CREATE TABLE "order_delivery_data" (
    "order_id" INT NOT NULL REFERENCES "order"(id),
    "firstname" VARCHAR,
    "lastname" VARCHAR,
    "email" VARCHAR(128),
    "phone" VARCHAR(16),
    "street" VARCHAR(64),
    "city" VARCHAR(64),
    "zip" VARCHAR(6)
);

UPDATE system.db SET version = 13;