<?php

/**
 * PG edit page module
 *
 * @todo PHP doc missing
 * @todo cmscontextid shouldnt be used
 * @package tuksiBackendModule
 */
class mBackendPage extends mBackendBase {
	
	public $tpl;
	private $contentareaid;
	private $objHook = false;
	private $settingsMode = false;
	
	function __construct(&$objMod){
		parent::__construct($objMod);	
		
		$this->tpl = new tuksiSmarty();
		
		if($_POST->getInt('contentareaid') || $_GET->getInt('contentareaid')) {
			$this->contentareaid = $_POST->getInt('contentareaid') ? $_POST->getInt('contentareaid') : $_GET->getInt('contentareaid');
		}
		if($_POST->getInt('settings') || $_GET->getInt('settings')) {
			$this->settingsMode = true;
		}
	}
	
	public function getHTML(){
		
		$objPage = tuksiBackend::getInstance();

		$arrUser = tuksiBackendUser::getUserInfo();
		
		$objPage->addBookmark();
		
		if($this->settingsMode) {
			
			$objPage->addButton("SAVE");
			
			$objSettings = new tuksiPageGeneratorSettings($objPage->treeid,$objPage->tabid);
			
			if($objPage->action == "SAVE" && $objPage->arrPerms['SAVE']) {
				$objSettings->save();
				if($objSettings->templateChanged()){
					tuksiLog::treeAction('pagetemplatechanged',$objPage->treeid);
				}
			}
			
			$arrHtml = $objSettings->getHTML();
			
			$objStdTpl = new tuksiStandardTemplateControl();
			
			if (is_array($arrHtml)){
				//check if contextid is set and don't show anything if now
				foreach($arrHtml as $arrData) {
		   		$objStdTpl->addElement($arrData['name'],$arrData['html']);
				}
			}
			
			$this->tpl->assign("content",$objStdTpl->fetch());
			
			
		} 
		
		if(($arrAreas = $this->getContentAreas()) !== false) {
			
			if(!$this->contentareaid) {
				$this->contentareaid = $arrAreas[0]['id'];
			}
			
			foreach ($arrAreas as $arrTab) {
				if($this->settingsMode) {
					$active = false;
				} else {
					$active = $arrTab['id'] == $this->contentareaid ? true : false; 
				}
				$objPage->addTab($arrTab['name'],$objPage->getUrl($objPage->treeid) . "&contentareaid=" . $arrTab['id'],$active);
			}
		}
		
		
		if(!$this->settingsMode) {
			
			$objSettings = new tuksiPageGeneratorSettings($objPage->treeid, $objPage->tabid, true);
			
			if($objPage->action == "SAVE" && $objPage->arrPerms['SAVE']) {
				$objSettings->save();
			}
			
			$arrHtml = $objSettings->getHTML();
			
			$objStdTpl = new tuksiStandardTemplateControl();
			
			if (is_array($arrHtml)){
				//check if contextid is set and don't show anything if now
				foreach($arrHtml as $arrData) {
		   		$objStdTpl->addElement($arrData['name'],$arrData['html']);
				}
			}
			
			$this->tpl->assign("options",$objStdTpl->fetch());
			
			
			if(($arrAreas) !== false) {
				
				$objPage->addButton("SAVE");
				
				$this->tpl->assign("areas",$arrAreas);
				
				$arrOption = array(	"openSingle" => true,
									"openAll" => false);
				
				$objPageGenElements = new tuksiPageGeneratorElementsHtml($this->objMod->cmstreeid, $this->objMod->cmstreetabid, $this->contentareaid,false,$arrOption);
				
				if($objPage->action == "SAVE" && $objPage->arrPerms['SAVE']) {
					
					$doSave = true;
					if(!$this->hookBefore('save'))
						$doSave = false;
					
					if($doSave) {
						$objPageGenElements->save();
						tuksiLog::treeAction('pagesaved',$objPage->arrTree['id'],$objPage->arrTree['name']);
						$objPage->status($objPage->cmsText('pagesaved'));
						$this->hookAfter('save');
					}
				}
				
				if($_POST->getStr("savemodulearrange") && $objPage->arrPerms['SAVE']) {
					$json = $_POST->getStr('json');
					$objJson = new tuksiJSON();
					$arrModules = tuksiTools::jarray($objJson->parse($json));
					$objPageGenElements->arrangeElements($arrModules);
				}
				
				$htmlElements = $objPageGenElements->getInsertedElementsHtml();
				$this->tpl->assign("content",$htmlElements);
			}
			if(!$objSettings->hasData) {
				$this->settingsMode = true;
				$objPage->addButton("SAVE");
			}
			
		}
		
		if(!$this->settingsMode) {

			if ($objPage->arrTree['cmscontextid'] == '3') {
				$arrConf = tuksiConf::getConf();
				$previewUrl = 'http://' . $arrConf['site']['url_preview']; 

				$previewUrl.= "/newsletter/?treeid=" . $this->objMod->cmstreeid;
			} elseif ($objPage->arrTree['cmssitelangid']){
				$arrConf = tuksiConf::getPageConf($objPage->arrTree['id']);
				$previewUrl = 'http://' . $arrConf['url_preview']; 
				$previewUrl.= "/{$objPage->arrTree['url']}";

			} 
			
			$onclick= "tuksi.util.openPage('$previewUrl')";
			
			$this->addButton("PREVIEW","","SAVE", '', $onclick);
		}
		
		$this->addActionButton("BTNCOPYPAGE","","READ");
		$this->addActionButton("BTNMOVEPAGE","","DELETE");
		$this->addActionButton("BTNDELETEPAGE","","DELETE");
		$this->addActionButton("BTNADDPAGE","","ADDPAGE");
		$this->addActionButton("BTNRELEASEPAGE","","RELEASEPAGE");
		
		$this->addActionButton('BTNTPLADMIN',"","USERGROUP_SUPERADMIN");
		
		$editActive = $this->settingsMode ? false : true;
		
		$objPage->addTab($objPage->cmsText('settings'),'?treeid='.$objPage->treeid.'&settings=1',$this->settingsMode);
		
		return parent::getHTML();
	}
	
	private function getContentAreas(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$sqlArea = "SELECT c.*, t.id AS tabid,t.cmscontextid FROM pg_contentarea c, cmstree t ";
		$sqlArea.= "WHERE c.pg_page_templateid = t.pg_page_templateid AND t.id = '{$this->objMod->cmstreeid}' AND c.pg_page_templateid > 0 ";
		$sqlArea.= "ORDER BY c.seq ";
		
		$arrRsArea = $objDB->fetch($sqlArea);
		if($arrRsArea['ok'] && $arrRsArea['num_rows'] > 0) {
			return $arrRsArea['data'];
		} else {
			return false;
		}
	}
}
?>
