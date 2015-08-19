<?php

/**
 * ??
 *
 * @uses tuksiDebug
 * @uses tuksiSmarty
 * 
 * @package tuksiFrontend
 * 
 */

class mFrontendForm extends mFrontendBase {

	//return the html for the module
	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

		if (!$this->objPage->udvsite)
			$this->tpl->setCaching(10);
		
	}
	/**
	 * Henter HTML
	 */

	function getHTML() {
		
		$builder = fieldFormBuilder::getInstance($this->objMod->value1);
		$formHTML = $builder->getHtml();
		$this->tpl->assign("form",$formHTML);
		return parent::getHTML();
	}
}
?>
