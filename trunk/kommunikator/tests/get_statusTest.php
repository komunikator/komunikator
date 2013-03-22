<?php

class get_statusTest extends PHPUnit_Framework_TestCase {

    public static function provider() {
        return array(
            array('c3h8rud74rfcap9bpae40e2pu5', false), //invalid session id
            array(file_get_contents(sys_get_temp_dir().'/session'), true),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testStatus($session, $result) {
        $params = array('action' => 'get_status', 'session' => $session);
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        $res = json_decode($out);
        $this->assertEquals($result, $res->success);
    }

}

?>
