CREATE TABLE product_category (
    product_id INT REFERENCES product(id) ON DELETE CASCADE,
    category_id INT REFERENCES category(id) ON DELETE CASCADE
);
CREATE UNIQUE INDEX uq_product_category ON product_category (product_id, category_id);

UPDATE system.db SET version = 6;