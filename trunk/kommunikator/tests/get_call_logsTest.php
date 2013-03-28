<?php

class get_call_logsTest extends PHPUnit_Framework_TestCase {

    public static function provider() {
        $form_data = array(
            "filter" => array(array("type" => "date", "comparison" => "eq", "value" => "2013/02/22 00:00:00", "field" => "time")),
            "page" => 1,
            "start" => 0,
            "size" => 10000,
            "sort" => "time",
            "dir" => "DESC"
        );
        return array(
            array($form_data),
            array($form_data)
        );
    }

    /**
     * @dataProvider provider
     */
    public function testCallLogs($form_data) {
        $params = array('action' => 'get_call_logs', 'session' => file_get_contents(sys_get_temp_dir() . '/session'));
        $params += $form_data;
        //print_r("php data_.php \"" . addslashes(json_encode($params))."\"");
        $out = shell_exec("php data_.php \"" . addslashes(json_encode($params))."\"");
        $res = json_decode($out);
        print_r(count($res->data));
        $this->assertEquals(true, $res->success);
    }

}

?>
