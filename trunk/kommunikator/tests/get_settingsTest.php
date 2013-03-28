<?php

class get_settingsTest extends PHPUnit_Framework_TestCase {

    public function testSettings() {
        $params = array('action' => 'get_settings', 'session' => file_get_contents(sys_get_temp_dir().'/session'));
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        $res = json_decode($out);
        $this->assertEquals(true, $res->success);
    }

}

?>
