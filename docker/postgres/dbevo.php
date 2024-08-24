<?php

/** Database evolution script */

$rootUser = getenv('POSTGRES_USER');
$rootPass = getenv('POSTGRES_PASSWORD');

$storeDb = 'academistore';
$storeUser = getenv('POSTGRES_STORE_USER');
$storePass = getenv('POSTGRES_STORE_PASSWORD');


$dbconn = pg_connect("host=postgres port=5432 dbname=postgres user=$rootUser password=$rootPass");
$checkDbQuery = "SELECT 1 FROM pg_database WHERE datname = '$storeDb'";
$result = pg_query($dbconn, $checkDbQuery);

// Database initialization
if (pg_num_rows($result) == 0) {

	$createDbQuery = "CREATE DATABASE $storeDb";
	$result = pg_query($dbconn, $createDbQuery);

	if ($result) {
		$dbconn = pg_connect("host=postgres port=5432 dbname=$storeDb user=$rootUser password=$rootPass");
		$result = pg_query(
			$dbconn, "
			CREATE SCHEMA system;
			CREATE TABLE system.db(version SMALLINT DEFAULT 0);
			INSERT INTO system.db(version) VALUES(0);"
		);

		$result = pg_query(
			$dbconn, "
			CREATE USER $storeUser WITH ENCRYPTED PASSWORD '$storePass';
			GRANT ALL ON ALL TABLES IN SCHEMA public TO $storeUser;"
		);
	}
} else {

	// @TODO logika pro iteraci sql scriptu
}

pg_close($dbconn);
