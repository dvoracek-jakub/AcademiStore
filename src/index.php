<?php

$dbconn = pg_connect("host=postgres port=5432 dbname=postgres user=root password=secret456");
echo $dbconn ? "postgre connected" : "postgre connection failed";
pg_close($dbconn);

phpinfo();