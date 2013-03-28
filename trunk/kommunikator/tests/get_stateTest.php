<?php

class get_stateTest extends PHPUnit_Framework_TestCase {

    public function testState() {
        $params = array('action' => 'get_state', 'session' => file_get_contents(sys_get_temp_dir().'/session'));
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        print_r($out);
        $res = json_decode($out);
        $this->assertEquals(false, $res->success);
    }

}

?>
