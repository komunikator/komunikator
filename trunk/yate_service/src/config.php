<?php

/* File created by FreeSentral v1.2 */

date_default_timezone_set("Europe/London");

require_once("DB.php");

function handle_pear_error($e) {
Yate::Output($e->getMessage().' '.print_r($e->getUserInfo(),true));
}
require_once 'PEAR.php';
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error');

$db_type_sql="mysql";
$db_host="localhost";
$db_user="root";
$db_passwd="root";
$db_database="FREESENTRAL";

 /*
$db_type_sql="pgsql";
$db_host="localhost";
$db_user="postgres";
$db_passwd="postgres";
$db_database="freesentral";
*/
//$db_host="172.17.2.6";


$dsn="$db_type_sql://$db_user:$db_passwd@$db_host/$db_database";
$conn = DB::connect($dsn);
$conn->setFetchMode(DB_FETCHMODE_ASSOC);

$vm_base = "/var/lib/misc";
$no_groups = false;
$no_pbx = false;
$uploaded_prompts = "/var/lib/misc";
$query_on = false;
$max_resets_conn = 5;
?>
