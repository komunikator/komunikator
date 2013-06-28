<?

need_user();

$values = array();
$data = json_decode($HTTP_RAW_POST_DATA);
//file_put_contents('a',print_r($data,true));
$rows = array();
$values = array();
$extensions = array();

if ($data && !is_array($data))
    $data = array($data);
foreach ($data as $row) {
    $values = array();
    $id = null;
    foreach ($row as $key => $value)
        switch ($key) {
            case 'id':
                $id = $key;
                break;
            case 'did':
                break;
            case 'default_dest':
                if (preg_match('/\d{3}/', $value)) {
                    $values['extension_id'] = " (select extension_id from extensions where extension = '$value') ";
                    $values['group_id'] = 'null';
                } else {
                    $values['group_id'] = " (select group_id from groups where groups.group = '$value') ";
                    $values['extension_id'] = 'null';
                }
                break;
            case 'destination':
                if (preg_match('/\d{3}/', $value)) {
                    $values[$key] = "'$value'";
                } else {
                    if (in_array($value, $key_source))
                        $values[$key] = "'$source[$value]'";
                    else
                        $values[$key] = " (select extension from groups where groups.group = '$value') ";
                }
                break;
            default:
                $values[$key] = "'$value'";
        }
    $rows[] = $values;
}
include("create.php");
?>