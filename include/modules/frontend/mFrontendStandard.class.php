<?php

/**
 * Standard module, if you write simpel tpl.
 *
 * @uses mFrontendBase
 * @uses tuksiSmarty
 * @package tuksiFrontendModule
 */
class mFrontendStandard extends mFrontendBase {

	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

	}
	
	/**
	 * Get HTML content
	 *
	 * @return string
	 */
	function getHTML() {
		if ($html = $this->checkCache()) {
			return $html;
		}
		return parent::getHTML();
	}
}
?>