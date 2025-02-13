CREATE TYPE order_status AS ENUM ('NEW', 'IN_PROCESS', 'COMPLETED', 'RETURNED', 'CANCELLED');
CREATE TYPE payment_status AS ENUM ('UNPAID', 'CREATED', 'PAID', 'CANCELLED');

CREATE TABLE "order" (
    "id" SERIAL PRIMARY KEY,
    "customer_id" INT NOT NULL REFERENCES customer(id),
    "cart_id" INT NOT NULL REFERENCES cart(id) UNIQUE,
    "shipping_id" INT NOT NULL REFERENCES shippingType(id),
    "payment_id" INT NOT NULL REFERENCES payment(id),
    "delivery_address" VARCHAR,
    "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    "status" order_status DEFAULT 'NEW'
);

CREATE TABLE order_payment (
    "id" SERIAL PRIMARY KEY,
    "order_id" INT NOT NULL REFERENCES "order"(id),
    "status" payment_status DEFAULT 'UNPAID',
    "remote_identifier" VARCHAR,
    "remote_state" VARCHAR,
    "modified" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


COMMENT ON COLUMN order_payment.remote_identifier IS 'Identifikátor platby v platební službě';

UPDATE system.db SET version = 11;