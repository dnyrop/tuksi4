<?php

/**
 * Enter description here...
 *
 * @package tuksiFrontendPage
 */

class pFrontendTextpage extends pFrontendBase {

	//class for the content area returns with modules
	function __construct() {
		parent::__construct();
		
		$objPage = tuksiFrontend::getInstance();
		
		foreach ($objPage->arrSitemap as $arrSitemapItem) {

			if (!empty($arrSitemapItem['open_selected'])) {
				$this->tplPage->assign('sideMenu', $arrSitemapItem['nodes']);
			}
		}
	}
	
	
	
}
?>
