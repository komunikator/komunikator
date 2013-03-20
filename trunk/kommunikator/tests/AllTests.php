<?php

require_once 'configTest.php';
require_once 'authTest.php';
require_once 'get_statusTest.php';

$suite = new PHPUnit_Framework_TestSuite;
$suite->addTest(new configTest('testDBConn'));
$suite->addTest(new authTest('testAuth'));
$suite->addTest(new get_statusTest('testStatus'));
?>