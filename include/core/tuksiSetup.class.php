<?php

class tuksiSetup {
	
	private $step = 1;
	private $maxstep = 3;
	private $arrSteps = array();

	function __construct(){
					$this->arrSteps[] = array('no' => 1, 'name' => 'Check PHP settings');
					$this->arrSteps[] = array('no' => 2, 'name' => 'Enter database settings');
					$this->arrSteps[] = array('no' => 3, 'name' => 'Enter domains');
					$this->arrSteps[] = array('no' => 4, 'name' => 'Enter main url');
	}

	function getHTML() {

					$this->tpl = new tuksiSmarty();
					$this->tpl->assign('steps', $this->arrSteps);
					$this->tpl->assign('step', $this->step);
					$this->tpl->assign('maxstep', $this->maxstep);

					switch ($this->step) {
						case(1) :

							// Check PHP settings
							$this->doStep1();		
					}

					return $this->tpl->fetch('tuksiSetup.tpl');
	}

	function doStep1() {
		// Checking permissions
		
	}
	function doStep1() {
		// Checking permissions
		
		// convert check
		// identify check
	}
	
}
?>
