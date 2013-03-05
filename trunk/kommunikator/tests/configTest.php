<?php

require_once dirname(__FILE__) . '\..\src\config.php';
class configTest extends PHPUnit_Framework_TestCase {

    public function testDBConn() {
        global $conn;
        
        $this->assertEquals('DB_mysql', get_class ($conn));
    }

}

?>
