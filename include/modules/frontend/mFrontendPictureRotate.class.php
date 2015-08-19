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

class mFrontendPictureRotate extends mFrontendBase {

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
		
		$this->objPage->addJavascript("/javascript/libs/prototype.js");
		$this->objPage->addJavascript("/javascript/libs/scriptaculous/scriptaculous.js");
		$this->objPage->addJavascript('/javascript/libs/tuksi.slideshow.js');
		
		return parent::getHTML();
	}
}
?>
