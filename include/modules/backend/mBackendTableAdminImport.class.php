<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendTableAdminImport extends mBackendBase  {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$this->addButton("IMPORT","","ADD");
		
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
}
?>