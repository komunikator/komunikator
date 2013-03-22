<?php

class valid_actionTest extends PHPUnit_Framework_TestCase {

    public static function provider() {
        return array(
            array('invalid_action', false), //invalid action name
            array('get_status', true),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testValidAction($action, $result) {
        $params = array('session' => file_get_contents(sys_get_temp_dir().'/session'), 'action' => $action);
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        $res = json_decode($out);
        $this->assertEquals($result, $res->success);
    }

}

?>
