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

$target_path = "/var/lib/misc";
$do_not_load = array();        //modules that are inserted here won't be loaded
$limit = 20;  //max number to display on page
$enable_logging = "on"; // possible values: "on"/"off", true/false, "yes"/"no" 
$upload_path = "/tmp";     // path where file for importing extensions will be uploaded
$default_ip = "ssl://127.0.0.1";	//	ip address where yate runs
$default_port = "5039";	// port used to connect to
$block = array("admin_settings"=>array("cards"));	// don't change this. This option is still being tested
?>
