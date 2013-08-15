<?

function need_user() {
    if (!$_SESSION['user']) {
        echo (out(array("success" => false, "message" => "User is undefined")));
        exit;
    }
}

function getparam($param) {
    $ret = null;
    if (isset($_POST[$param]))
        $ret = $_POST[$param];
    else if (isset($_GET[$param]))
        $ret = $_GET[$param];
    else
        return null;
    return $ret;
}

function compact_array($array) {
    $header = array();
    $data = array();
    if ($array)
        foreach ($array as $array_row) {
            $data_row = array();
            if ($array_row)
                foreach ($array_row as $key => $value) {
                    if (!count($data))
                        $header[] = $key;
                    $data_row[] = $value;
                }
            $data[] = $data_row;
        }
    return array('header' => $header, 'data' => $data);
}

function xml_section_build($dom, $root, $data) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (is_numeric($key))
                $key = 'item';
            $section = $dom->createElement($key);
            xml_section_build($dom, $section, $value);
        } else {
            if (is_numeric($key))
                $key = 'value' . $key;
            $section = $dom->createElement($key, $value);
        };
        $root->appendChild($section);
    }
}

function out($data) {
    $export = getparam("export");
    if ($export) {
        $columns = $data["header"];
        array_unshift($data["data"], $columns);
        $request_id = uniqid();
        $tmp = sys_get_temp_dir() . "/" . $request_id;
        $data = array("request_id" => $request_id, "success" => !(file_put_contents($tmp, json_encode($data["data"])) === false));
    }
    $type = getparam("type");
    if ($type == 'xml') {
        $dom = new DomDocument('1.0', 'utf-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $root = $dom->createElement("response");
        $dom->appendChild($root);
        xml_section_build($dom, $root, $data);
        header("Content-Type:text/xml;charset=UTF-8");
        return $dom->saveXML($root);
    }
    else
        return json_encode($data);
}

function get_filter() {
    $filters = parseExtJSFilters();
    return $filters ? " WHERE " . $filters : '';
}

function get_sql_order_limit() {
    $sort = getparam("sort") ? get_sql_field(getparam("sort")) : 1;
    $dir = getparam("dir") ? getparam("dir") : 'DESC';
    return get_filter() . " ORDER BY " . $sort . " " . $dir . get_sql_limit(getparam("start"), getparam("size"));
}

function get_sql_limit($start, $size/* ,$page */) {
    if (!(isset($start)) || !(isset($size)))
        return '';
    //  if ($start==null || $size==null) return '';
    global $db_type_sql;
    if ($db_type_sql == 'mysql')
        return " LIMIT $start,$size";
    return " LIMIT $size OFFSET $start";
}

function get_sql_field($name) {
    global $db_type_sql;
    if ($db_type_sql == 'mysql')
        return "`$name`";
    return $name;
}

function get_SQL_concat($data) {
    global $db_type_sql;
    if (!is_array($data))
        return $data;
    if (count($data) == 0)
        return '';
    if (count($data) == 1)
        return $data[0];
    if ($db_type_sql == 'mysql') {
        $str = 'CONCAT(';
        $sep = '';
        foreach ($data as $el) {
            $str .= $sep . $el;
            $sep = ',';
        };
        return $str . ')';
    } else {
        $str = '';
        $sep = '';
        foreach ($data as $el) {
            $str .= $sep . $el;
            $sep = ' || ';
        };
        return $str;
    }
}

function parseExtJSFilters() {
    if (getparam('filter') == null) {
        // No filter passed in
        return false;
    };

    $filters = json_decode(getparam('filter')); // Decode the filter
    if ($filters == null) { // If we couldn't decode the filter
        return false;
    }
    $whereClauses = array(); // Stores whereClauses
    foreach ($filters as $filter) {
        switch ($filter->type) {
            case 'boolean':
                $filter->value = ($filter->value === true) ? '1' : '0'; // Convert value for DB
                $whereClauses[] = "$filter->field = $filter->value";
                break;
            case 'date':
                //$filter->value = "'$filter->value'"; // Enclose data in quotes
                $filter->value = strtotime($filter->value); // Enclose data in quotes
            case 'numeric':
                switch ($filter->comparison) {
                    case 'lt': // Less Than
                        $whereClauses[] = "$filter->field < $filter->value";
                        break;
                    case 'gt': // Greather Than
                        $whereClauses[] = "$filter->field > $filter->value";
                        break;
                    case 'eq': // Equal To
                        if ($filter->type == 'date') {
                            $whereClauses[] = "$filter->field < $filter->value+60*60*24";
                            $whereClauses[] = "$filter->field > $filter->value";
                        }
                        else
                            $whereClauses[] = "$filter->field = $filter->value";
                        break;
                }
                break;
            case 'list':
                $listItems = array();
                if (!count($filter->value))
                    break;
                foreach ($filter->value as $value) {
                    $listItems[] = "'$value'";
                };
                $whereClauses[] = "$filter->field IN(" . implode(',', $listItems) . ')';
                break;
            case 'string':
            default: // Assume string
                $whereClauses[] = "(
                    $filter->field LIKE '{$filter->value}%' OR
                    $filter->field LIKE '%{$filter->value}' OR 
                    $filter->field LIKE '%{$filter->value}%' OR
                    $filter->field = '{$filter->value}'
                )";
                break;
        }
    }
    if (count($whereClauses) > 0) {
        return implode(' AND ', $whereClauses);
    }
    return false;
}

$macro_sql = array(
    'caller_called1' => ' a.caller, b.called, ',
    'caller_called' =>
    <<<EOD
		case when (select firstname from extensions where extension = a.caller) is not null then 
		CONCAT((select firstname from extensions where extension = a.caller),' ',
					 (select lastname  from extensions where extension = a.caller),' (',a.caller,')') 
		else 
		a.caller end caller , 
	  case when (select firstname from extensions where extension = b.called) is not null then 
		CONCAT((select firstname from extensions where extension = b.called),' ',
					 (select lastname  from extensions where extension = b.called),' (',b.called,')') 
		else 
		b.called end called , 

EOD
        )
?>