<?
function need_user(){
if(!$_SESSION['user']) {
    echo (out(array("success"=>false,"message"=>"User is undefined"))); exit;} 
}

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

function xml_section_build($dom,$root,$data) {
    foreach ($data as $key=>$value) {
        if (is_array($value)) {
            if (is_numeric($key)) 
                $key='item';	
            $section = $dom->createElement($key);
            xml_section_build($dom,$section,$value);
        } else {
            if (is_numeric($key)) 
                $key='value'.$key;
            $section = $dom->createElement($key,$value);
        };
        $root->appendChild($section);
    }
}

function out($data) {
    $type = getparam("type"); 
    if ($type=='xml') {
        $dom = new DomDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $root = $dom->createElement("response");
        $dom->appendChild($root);
        xml_section_build($dom,$root,$data);
        header("Content-Type:text/xml;charset=UTF-8");	
        return $dom->saveXML($root);
    }
    else
        return json_encode($data);		
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