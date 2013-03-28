<?php

class get_music_on_holdTest extends PHPUnit_Framework_TestCase {

    public function testDial_plans() {
        $params = array('action' => 'get_dial_plans', 'session' => file_get_contents(sys_get_temp_dir().'/session'));
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        $res = json_decode($out);
        $this->assertEquals(true, $res->success);
    }

}

?>
