#!/usr/bin/php -q
<?
require_once('lib_queries.php');
require_once('lib_smtp.inc.php');

function debug($msg) {
    Yate::Debug('send_voicem.php: ' . $msg);
}

$filename = $argv[1];

if (!is_file($filename)) die();

$dir = dirname($filename);
$user = substr($dir, -3);

$args = basename($filename, '.mp3');
$args_arr = explode('-', $args);

if (count($args_arr) < 4) die();

$caller = $args_arr[3];
$ftime = $args_arr[1] . ' ' . $args_arr[2];
$query = "SELECT address FROM extensions WHERE extension = '$user'";
$res = query_to_array($query);
$address = $res[0]["address"];

send_voicemail($address, $filename, $caller, $ftime);

?>