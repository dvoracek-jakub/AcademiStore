CREATE TABLE shipping (
    "id" SERIAL PRIMARY KEY,
    "name" VARCHAR,
    "price" DECIMAL(10,2) NOT NULL,
    "priority" SMALLINT DEFAULT 1
);

CREATE TABLE payment (
    "id" SERIAL PRIMARY KEY,
    "name" VARCHAR,
    "price" DECIMAL(10,2) NOT NULL,
    "priority" SMALLINT DEFAULT 1
);

CREATE TABLE payment_shipping (
    "payment_id" INT REFERENCES payment(id) ON DELETE CASCADE,
    "shipping_id" INT REFERENCES shipping(id) ON DELETE CASCADE
);

COMMENT ON TABLE payment_shipping IS 'Obsahuje všechny validní kombinace dopravy <> platby';

UPDATE system.db SET version = 10;