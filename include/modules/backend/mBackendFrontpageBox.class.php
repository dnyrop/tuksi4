<?php
/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendFrontpageBox extends mBackendBase {
	
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
			$siteid = $rsConf['data']['id'];
		}
		
		switch ($this->objMod->value1) {
			case 'created':
				$title = $objPage->cmsText('latestcreatedpages');
				$arrPages = tuksiPageGeneratorStatus::getLatestCreatedPages(5,$siteid);
				break;
			case 'published':
					$title = $objPage->cmsText('latestpublishedpages');
					$arrPages = tuksiPageGeneratorStatus::getLatestPublishedPages(5,$siteid);
					break;
			case 'changed':
					$title = $objPage->cmsText('latestchangedspages');
					$arrPages = tuksiPageGeneratorStatus::getLatestChangedPages(5,$siteid);
					break;
			case 'deleted':
					$title = $objPage->cmsText('latestdeletedpages');
					$arrPages = tuksiPageGeneratorStatus::get1LatestDeletedPages(5,$siteid);
					break;
			default:
				$title = $objPage->cmsText('latestcreatedpages');
				$arrPages = tuksiPageGeneratorStatus::getLatestChangedPages(5,$siteid);
				break;
		}
		$this->tpl->assign('title',$title);
		$this->tpl->assign('pages',$arrPages);
		return parent::getHtml();
	}
	
	function saveData(){
		
	}
	
	
}
?>
