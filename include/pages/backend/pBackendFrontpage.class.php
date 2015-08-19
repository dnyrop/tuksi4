<?php
class	pBackendFrontpage extends pBackendBase {

	private $arrTabs = array();
	
	//class for the content area returns with modules
	function __construct() {
		parent::__construct();
	}
	
	public function getHtml(){
		//$this->setTabs();
		return parent::getHtml();
	}
}
?>