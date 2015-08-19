<?php
class	mFrontendRedirect extends mFrontendBase {

	//return the html for the module
	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

	}

	/**
	 * Henter HTML
	 */
	function getHTML() {
		
		$objPage = tuksiFrontend::getInstance();
		$arrUrl = fieldLink::makeUrl($this->objMod->link);
		$url = '/';
		if ($arrUrl['url'] && $arrUrl['url'] != $objPage->arrPage['pg_urlpart_full']) {
			$url = $arrUrl['url'];
		}
		http_response_code(301);
		header("Location: " . $url);
		exit();
	}
}
?>
