<?php

class authTest extends PHPUnit_Framework_TestCase {

    public static function provider() {
        return array(
            //array('user', 'user', false),
            array('admin', 'admin', true),
            array('nonexistent_user', 'nonexistent_user_password', false)
        );
    }

    /**
     * @dataProvider provider
     */
    public function testAuth($user = '', $password = '', $result = false) {
        $out = shell_exec("php auth_.php $user $password");
        $res = json_decode($out);
        $this->assertEquals($result, $res->success);
        if ($result)
            file_put_contents('session', $res->session_id);
    }

}

?>
