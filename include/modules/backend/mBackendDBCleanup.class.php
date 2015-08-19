<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendDBCleanup extends mBackendBase  {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		
		$nbModules = tuksiCleanup::getNBModules();
		$nbTree = tuksiCleanup::getNBTree();
		$nbTab = tuksiCleanup::getNBTab();
		$nbElement = tuksiCleanup::getNBElement();
		
		$returnHtml = "cmstree : " . $nbTree . "<br>";
		$returnHtml.= "cmstreetab : " . $nbTab . "<br>";
		$returnHtml.= "element : " . $nbElement . "<br>";
		$returnHtml.= "pg_content : " . $nbModules . "<br>";
		
		
		
		/*$returnHtml = parent::getHTML();*/
		return $returnHtml;
	}
}
?>