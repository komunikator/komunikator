<?php

class get_didsTest extends PHPUnit_Framework_TestCase {

    public function testDids() {
        $params = array('action' => 'get_dids', 'session' => file_get_contents(sys_get_temp_dir().'/session'));
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        $res = json_decode($out);
        $this->assertEquals(true, $res->success);
    }

}

?>
