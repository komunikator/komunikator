<?
/*
need_user();

$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();

if ($data && !is_array($data)) $data = array($data);
 foreach ($data as $row)
{
$values = array();
    foreach ($row as $key=>$value)
            $values[$key]="'$value'"; 
$rows[] = $values;
}
$id_name = 'music_on_hold_id';
require_once("update.php");
<?
*/
need_user();

$table_name = 'music_on_hold';
$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
$rows = array();
$values = array();

if ($data && !is_array($data)) $data = array($data);
    foreach ($data as $row) {
        $values = array();
        foreach ($row as $key=>$value)
            if ($key == 'playlist') $playlist = ($value==null)?'null':$value;
            else {
                if ($key == 'id') $music_on_hold_id = $value;
                $values[$key]="'$value'"; 
            }
        $rows[] = $values;
    }
$id_name = 'music_on_hold_id';
if ($playlist) $need_out = false; 
include ("update.php");

if (!$playlist) return;

$sql=
<<<EOD
	SELECT playlist_item_id,g.playlist_id FROM playlist_items gm 
	left join playlists g on g.playlist = '$playlist'  
	where gm.music_on_hold_id = '$music_on_hold_id'			
EOD;

$rows = array();

$result = compact_array(query_to_array($sql));
if(!is_array($result['data']))  echo out(array('success'=>false,'message'=>$result));
$row = $result['data'][0]; 

if ($row) {
    $id_name = 'playlist_item_id';
    $rows[] = array('id'=>$row[0],'playlist_id'=>$row[1]);
    if ($playlist!='null') { 
        $action  = 'update_playlist_items';
        include ("update.php");
    }
    else {
        $action  = 'destroy_playlist_items';
        include ("destroy.php");
    }	
}
else {
    $rows[] = array('music_on_hold_id'=>"'$music_on_hold_id'",'playlist_id'=>" (SELECT playlist_id FROM playlists WHERE playlists.playlist = '$playlist') ");
    $action  = 'create_playlist_items';
    include ("create.php");
}
?>
