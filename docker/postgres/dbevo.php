<?php

/** Database evolution script */

$log = [];
$storeDb = 'academistore';
$saveLog = getenv('DBEVO_LOG');
$storeUser = getenv('POSTGRES_STORE_USER');
$storePass = getenv('POSTGRES_STORE_PASSWORD');
$sqlEvoPath = getenv('DBEVO_SQL_PATH');

$db = getDbConnection('postgres');
$result = pg_query($db, "SELECT 1 FROM pg_database WHERE datname = '$storeDb'");

// Database initialization
if (pg_num_rows($result) == 0) {
	$log[] = 'Creating database: ';
	$createDbQuery = "CREATE DATABASE $storeDb";
	$result = pg_query($db, $createDbQuery);

	if ($result) {
		$log[] = 'Success!';

		$asdb = getDbConnection($storeDb);
		$result = pg_query(
			$asdb, "
			CREATE SCHEMA system;
			CREATE TABLE system.db(version SMALLINT DEFAULT 0);
			INSERT INTO system.db(version) VALUES(0);"
		);

		$log[] = 'System schema ' . ($result ? 'created' : 'NOT created: ') . pg_last_error($db);

		$result = pg_query(
			$db, "
			CREATE USER $storeUser WITH ENCRYPTED PASSWORD '$storePass';
			ALTER DATABASE $storeDb OWNER TO $storeUser;"
		);
		$log[] = 'User and permissions ' . ($result ? 'created' : 'NOT created: ') . pg_last_error($db);
	} else {
		$log[] = 'Fail! ' . pg_last_error($db);
	}
} else {
	$log[] = 'Database exists';
}


// Database evolution
$sqlFiles = glob($sqlEvoPath . '*.sql');
if ($sqlFiles !== false && count($sqlFiles) > 0) {
	$db = getDbConnection($storeDb);
	foreach ($sqlFiles as $file) {
		$evoIter = (int) basename($file, '.sql');
		$result = pg_query($db, "SELECT version FROM system.db WHERE version < $evoIter");

		if ($result && pg_num_rows($result) > 0) {
			$log[] = "Found file to import: $file";
			$sql = file_get_contents($file);

			if ($sql !== false) {
				$result = pg_query($db, $sql);
				$log[] = 'Query was ' . ($result ? 'executed' : 'NOT executed: ') . pg_last_error($db);
			} else {
				$log[] = 'Error loading file!';
			}
		}
	}
}

pg_close($db);

if ($saveLog) {
	file_put_contents(getenv('DBEVO_LOGFILE'), print_r($log, true));
}

function getDbConnection(string $dbName)
{
	$rootUser = getenv('POSTGRES_USER');
	$rootPass = getenv('POSTGRES_PASSWORD');
	return pg_connect("host=postgres port=5432 dbname=$dbName user=$rootUser password=$rootPass");
}
