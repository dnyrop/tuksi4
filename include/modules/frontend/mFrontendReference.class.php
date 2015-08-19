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

class mFrontendReference extends mFrontendBase {

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
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM c_referencer{$this->objPage->tableext} ORDER BY name";
		$rs = $objDB->fetch($sql);
		
		if($rs['ok'] && $rs['num_rows'] > 0) {

			$split = ceil($rs['num_rows']/2);
			
			$arrFirst = array_slice($rs['data'],0,$split);
			$arrSecond = array_slice($rs['data'],$split);
			
			$this->tpl->assign('firstCol',$arrFirst);
			$this->tpl->assign('secondCol',$arrSecond);
					
		}
		
		return parent::getHTML();
	}
	
	function addStandardFields(){
	}
}
?>
