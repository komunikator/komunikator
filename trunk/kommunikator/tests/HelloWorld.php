<?

class HelloWorld {

    public $helloWorld;

    public function __construct() {
        $this->helloWorld = 'Hello World!';
    }

    public function sayHello() {
        return $this->helloWorld;
    }

}

?>