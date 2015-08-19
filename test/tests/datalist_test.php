<?php
if (! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', 'simpletest/');
}
require_once(SIMPLE_TEST . 'unit_tester.php');
require_once(SIMPLE_TEST . 'reporter.php');
require_once(SIMPLE_TEST . 'browser.php');

class testOfDataList extends UnitTestCase {
    
		function testOfDataList() {
			 $this->UnitTestCase('Lista class test');
    }
    
    function testLogin() {
	    $first = &new SimpleBrowser();
	    $first->post('http://4.0.backend.dev.tuksi.com/login/',array("username" => "ale",
	    																															"password" => "counter",
	    																															"process" => 1));
			
			$this->assertEqual($first->getUrl(), 'http://4.0.backend.dev.tuksi.com/');
			
			$second = &new SimpleBrowser();
	    $second->post('http://4.0.backend.dev.tuksi.com/login/',array("username" => "user",
	    																															"password" => "wrongpassword",
	    																															"process" => 1));
	    $this->assertEqual($second->getUrl(), 'http://4.0.backend.dev.tuksi.com/login/');
    }
}

$test = &new testOfDataList();
$test->run(new HtmlReporter());
?>