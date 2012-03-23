<?php
/* File created by FreeSentral v1.2 */

/* Settings for connecting to the PostgreSQL database */

/* Host where the database server is running - use "localhost" for local */
$db_host = "localhost";
/* Name of the database (a server may have many independent databases) */
$db_database = "freesentral";
/* Database username to use when connecting */
$db_user = "postgres";
/* Password for the database access */
$db_passwd = "postgres";

date_default_timezone_set("Europe/London");

$conn  = pg_connect("host='$db_host' dbname='$db_database' user='$db_user' password='$db_passwd'")
    or die("Could not connect to the postgresql database");

$vm_base = "/var/lib/misc";
$no_groups = false;
$no_pbx = false;
$uploaded_prompts = "/var/lib/misc";
$query_on = false;
$max_resets_conn = 5;
?>
