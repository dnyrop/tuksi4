<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendStandard extends mBackendBase  {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
}
?>