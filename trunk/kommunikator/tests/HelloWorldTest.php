<?php
require_once 'HelloWorld.php';

class HelloWorldTest extends PHPUnit_Framework_TestCase
{

    public function test__construct()
    {
        $hw = new HelloWorld();
        $this->assertEquals('HelloWorld', get_class($hw));
    }
 
    public function testSayHello()
    {
        $hw = new HelloWorld();
        $string = $hw->sayHello();
        $this->assertEquals('Hello World!', $string);
    }
}
?>