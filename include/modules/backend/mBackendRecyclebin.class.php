<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendRecyclebin extends mBackendBase  {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		$objPage->addJavascript('/javascript/backend/modules/mBackendRecyclebin.js');
		
		$objRecycle = new tuksiRecycle();
		
		$moveFromThrash = $_POST->getInt('moveFromThrash');
		$deleteFromThrash = $_POST->getInt('deleteFromThrash');
		
		if($moveFromThrash > 0) {
			$objRecycle->moveFromTrash($moveFromThrash);
			$objPage->status($objPage->cmstext('nodemovedfromtrash'));
		}
		
		if($deleteFromThrash > 0) {
			$objTree = tuksiTree::getInstance();
			$status = $objTree->deleteTreeNodeForGood($deleteFromThrash);
			$objPage->status($objPage->cmstext('nodedeletedfromtrash'));
		}
		
		$arrNodes = $objRecycle->getRecycledNodes();
		$this->tpl->assign('nodes',$arrNodes);
		
		//$this->addButton("BTNEMPTYTRASH", "", "DELETE");
		
		$returnHtml = parent::getHTML();		
		return $returnHtml;
	}
}
?>