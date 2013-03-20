<?php

class get_statusTest extends PHPUnit_Framework_TestCase {

    public static function provider() {
        return array(
            array('c3h8rud74rfcap9bpae40e2pu5', true),
            array(file_get_contents('session'), true),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testStatus($session, $result) {
        $out = shell_exec("php get_status_.php $session");
        $res = json_decode($out);
        $this->assertEquals($result, $res->success);
    }

}

?>
