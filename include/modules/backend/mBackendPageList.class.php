<?php

/**
 * PG list page module
 *
 * value1 : Copy page from treeid
 * 
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */
class mBackendPageList extends mBackendBase {
	
	public $tpl;
	
	function __construct(&$objMod){
		parent::__construct($objMod);	
		
		$this->tpl = new tuksiSmarty();
		
	}
	
	public function getHTML(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();

		if($_GET->getInt('rowid')) {
			
			//loead mBackendPag
			$objModule->template = 'mBackendPage.tpl';
			$objModule->cmstreeid = $_GET->getInt('rowid');
			$objPageEdit = new mBackendPage($objModule, $objPage);
			$html = $objPageEdit->getHtml();
			
		} else {
			
			if($this->userActionIsSet('RELEASE') && $objPage->arrPerms["RELEASE"]) {
				$objTuksiPageGenerator = new tuksiPageGenerator();

				$objTuksiPageGenerator->releaseSubPages($objPage->arrTree['id']);
			}

			if($this->userActionIsSet('ADD') && $objPage->arrPerms["ADD"]) {
				
				$objValues->addPageTreeid = $objPage->arrTree['id'];
				$objValues->addPageName ='New Page';
				$objValues->addPagePlacement = 1;
				$objValues->addPageCopyFromTreeid = $this->objMod->value1;
				
				$arrReturn = $objPage->addPage($objValues);
				
				if ($arrReturn['NEWTREEID']) {
					$sqlUpdate = "UPDATE cmstree SET show_inmenu = 0 WHERE id = '{$arrReturn['NEWTREEID']}'";
					$arrRs = $objDB->write($sqlUpdate);
				
					$url = $objPage->getUrl($arrReturn['NEWTREEID']);
					header("Location: $url");
					exit();
					
				} else {
					// Error??
				}
			}
			
			$arrConf = tuksiConf::getConf();
		
			$objModule->name = $this->objMod->headline;
			
			$objModule->template = 'mBackendLista.tpl';
			$objModule->value1 = 'cmstree';
			$objModule->value2 = '28';
			$objModule->value3 = "SELECT * FROM cmstree WHERE parentid = '{$objPage->arrTree['id']}' AND isdeleted = 0 ";
			$objModule->value8 = true;
			$objModule->value7 = 'name';
			$objModule->link = $objPage->getUrl() . '#ROWID#';
			
			$objList = new mBackendLista($objModule, $objPage);
			$html =  $objList->getHTML();
		}
		
		
		$this->tpl->assign('content', $html);
		
		return parent::getHTML();
	}
	
} // End mBackendPageList();
?>
