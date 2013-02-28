<?
need_user();

$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();
$id = null;

if ($data && !is_array($data)) $data = array($data);
foreach ($data as $row) {
    $values = array();
    foreach ($row as $key=>$value) {
        if ($key == 'in_use' && $value) 
	    $id = $row->id;
    $values[$key]="'$value'"; 
    $rows[] = $values;
}
}
if ($id)
    query("update playlists set in_use = 0 where playlist_id <> $id");
$id_name = 'playlist_id';
require_once("update.php");
?>