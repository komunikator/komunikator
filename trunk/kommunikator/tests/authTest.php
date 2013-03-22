<?php

class authTest extends PHPUnit_Framework_TestCase {

    public static function provider() {
        return array(
            array('admin', 'admin', true),
            array('nonexistent_user', 'nonexistent_user_password', false)
        );
    }

    /**
     * @dataProvider provider
     */
    public function testAuth($user, $password, $result) {
        $params = array('action' => 'auth', 'user' => $user, 'password' => $password);
        $out = shell_exec("php data_.php " . addslashes(json_encode($params)));
        $res = json_decode($out);
        $this->assertEquals($result, $res->success);
        if ($result)
            file_put_contents(sys_get_temp_dir() . '/session', $res->session_id);
    }

}

?>
