<?php

require_once 'configTest.php';
require_once 'authTest.php';
require_once 'get_statusTest.php';
require_once 'valid_actionTest.php';

$suite = new PHPUnit_Framework_TestSuite;
$suite->addTest(new configTest());
$suite->addTest(new authTest());
$suite->addTest(new get_statusTest());
$suite->addTest(new valid_actionTest());
?>