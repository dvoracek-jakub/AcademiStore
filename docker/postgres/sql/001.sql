CREATE TABLE alfaRed (
  order_id INT NOT NULL, 
  item_no SERIAL, 
  PRIMARY KEY (order_id, item_no)   /* Můžeš vytvořit nad párem sloupců, ale už ne nad 2 sloupci zvlášť */
);

CREATE TABLE alfaBlue (
    id SERIAL PRIMARY KEY
);
