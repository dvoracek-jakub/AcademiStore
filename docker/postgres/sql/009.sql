CREATE TYPE cart_status AS ENUM ('NEW', 'ORDERED');

CREATE TABLE cart (
    "id" SERIAL PRIMARY KEY,
    "customer_id" INT REFERENCES customer(id) ON DELETE CASCADE,
    "session_id" VARCHAR,
    "created_at" TIMESTAMP DEFAULT NOW(),
    "status" cart_status DEFAULT 'NEW',
    CHECK (customer_id IS NOT NULL OR session_id IS NOT NULL)
);

CREATE TABLE cart_item (
    "id" BIGSERIAL PRIMARY KEY,
    "cart_id" INT NOT NULL REFERENCES cart(id) ON DELETE CASCADE,
    "product_id" INT NOT NULL REFERENCES product(id) ON DELETE CASCADE,
    "quantity" INT NOT NULL CHECK (quantity > 0),
    "price" DECIMAL(10, 2) DEFAULT NULL
);

COMMENT ON COLUMN cart.session_id IS 'Session ID of logged out visitor';
COMMENT ON COLUMN cart_item.price IS 'Total price per 1 item after discounts applied. Filled in at the time of order submission.';

UPDATE system.db SET version = 9;