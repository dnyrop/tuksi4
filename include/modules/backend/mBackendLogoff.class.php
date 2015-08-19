<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */
class mBackendLogoff extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		
		$objTuksiUser = new tuksibackendUser();
		$objTuksiUser->logout();
		
		$arrConf = tuksiConf::getConf();
		
		$loginUrl = $objPage->getUrl($arrConf['link']['login_treeid']);
		
		header("Location: ".$loginUrl);
		exit();
	}
	
	function saveData(){
		
	}
}
?>
