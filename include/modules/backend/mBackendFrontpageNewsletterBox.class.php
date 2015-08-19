<?php
/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendFrontpageNewsletterBox extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		switch ($this->objMod->value1) {
			case 'sent':
				$title = $objPage->cmstext('latetssentnewsletters');
				$arrNewsletters = tuksiNewsletterStat::getSent(10);
				break;
			case 'awaiting':
				$title = $objPage->cmstext('nextwaitingnewsletters');
				$arrNewsletters = tuksiNewsletterStat::getWaiting(10);
				break;
			default:
				break;
		}
		
		$this->tpl->assign('title',$title);
		$this->tpl->assign('newsletters',$arrNewsletters);
		return parent::getHtml();
	}
	
	function saveData(){
		
	}
	
	
}
?>
