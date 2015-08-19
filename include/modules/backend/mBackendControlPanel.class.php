<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */
class mBackendControlPanel extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		
		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$arrConf = tuksiConf::getConf();
		
		$arrUser = tuksiBackendUser::getUserInfo();
		
		//get all panels to show

		$sqlPanels = "SELECT DISTINCT cp.*,p.*,cp.id as cpid FROM cmscontrolpanel cp,cmsperm p, cmsusergroup ug ";
		$sqlPanels.= "WHERE cp.parentid = 0 AND p.pread = 1 AND p.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = {$arrUser['id']} ";
		$sqlPanels.= "AND p.cmstreetabid = cp.cmstreetabid AND p.itemtype = 'tree' GROUP BY cp.id ";
		$sqlPanels.= "ORDER BY cp.seq";

		$arrRsPanels = $objDB->fetch($sqlPanels);
		foreach ($arrRsPanels['data'] as &$arrPanel) {
			$arrPanel['links'] = $this->getSubLinks($arrPanel['cpid']);
			$arrPanel['url'] = $objPage->getUrl($arrPanel['cmstreeid'],$arrPanel['cmstreetabid']);
		}
		
		$this->tpl->assign("panels",$arrRsPanels['data']);
		
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
	
	function getSubLinks($id){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$arrUser = tuksiBackendUser::getUserInfo();
		
		//get all panels to show
		$sqlPanels = "SELECT DISTINCT cp.* FROM cmscontrolpanel cp,cmsperm p, cmsusergroup ug ";
		$sqlPanels.= "WHERE cp.parentid = '$id' AND p.pread = 1 AND p.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = {$arrUser['id']} ";
		$sqlPanels.= "AND p.cmstreetabid = cp.cmstreetabid AND p.itemtype = 'tree' ";
		$sqlPanels.= "ORDER BY cp.seq";
		
		
		$arrRsPanels = $objDB->fetch($sqlPanels);
		
		if($arrRsPanels['num_rows'] > 0) {
			foreach ($arrRsPanels['data'] as &$arrLink) {
				$arrLink['url'] = $objPage->getUrl($arrLink['cmstreeid'],$arrLink['cmstreetabid']);
			}
			return $arrRsPanels['data'];
		} else {
			return array();
		}
	}
	
	function saveData(){
		
	}
}
?>
