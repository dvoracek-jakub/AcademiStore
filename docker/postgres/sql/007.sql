CREATE TABLE product_discount
(
	"id" SERIAL PRIMARY KEY,
	"product_id" INT NOT NULL REFERENCES product(id),
	"price" DECIMAL(10, 2) DEFAULT NULL,
	"start_date" DATE,
	"end_date" DATE,
	"from_quantity" INT DEFAULT 1,
	 CONSTRAINT chk_price_discount CHECK(
		 "start_date" IS NULL OR
		 "end_date" IS NULL OR
		 "start_date" < "end_date"
	)
);

UPDATE system.db SET version = 7;