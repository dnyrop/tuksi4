<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendFrontpageLinks extends mBackendBase  {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		$sqlConf = "SELECT * FROM cmssite WHERE rootid = '".$objPage->treeid."'";
		$rsConf = $objDB->fetchItem($sqlConf);
		if($rsConf['ok'] && $rsConf['num_rows'] == 1){
			$this->tpl->assign('sitelinks',$rsConf['data']);
		}
		
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
	
	function saveData(){
		
	}
}
?>
