<?
function getparam($param) {
    $ret = NULL;
    if (isset($_POST[$param]))
        $ret = $_POST[$param];
    else if (isset($_GET[$param]))
            $ret = $_GET[$param];
        else
            return NULL;
    return $ret;
}

function compact_array($array) {
    $header = array();
    $data = array();
    if ($array)
        foreach ($array as $array_row) {
            $data_row = array();
            if ($array_row)
                foreach ($array_row as $key=>$value) {
                    if (!count ($data)) $header[] = $key;
                    $data_row[] = $value;
                }
            $data[] = $data_row;
        }
    return array('header'=>$header,'data'=>$data);
}

function get_sql_order_limit() {
    $sort =  getparam("sort")?getparam("sort"):1;
    $dir  = getparam("dir")?getparam("dir"):'';
    return $sort." ".$dir.get_sql_limit(getparam("start"),getparam("size"));
}

function get_sql_limit($start,$size,$page) {
    if ($start==null || $size==null) return '';
    global $db_type_sql;
    if ($db_type_sql == 'mysql')
        return " LIMIT $start,$size";	
    return " LIMIT $size OFFSET $start";
}

?>