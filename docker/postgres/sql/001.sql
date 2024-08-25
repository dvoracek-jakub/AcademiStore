CREATE TABLE "user" (
	id SERIAL PRIMARY KEY NOT NULL,
	username VARCHAR(50) NOT NULL CHECK(LENGTH(username) > 2),
	password VARCHAR(100) NOT NULL CHECK(LENGTH(password) > 7),
	firstname VARCHAR(50),
	lastname VARCHAR(50)
);

UPDATE system.db SET version = 1;