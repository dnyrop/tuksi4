<?php

/**
 * ??
 * 
 * @package tuksiBackend
 */
class tuksiPageGeneratorSettings {
	
	private $treeid, $tabid, $edit;
	private $objTableView;
	public $hasData = true;
	
	public function __construct($treeid,$tabid,$edit = false) {
		
		$this->treeid = $treeid;
		$this->tabid = $tabid;
		$this->edit = $edit;
		
		$objDB = tuksiDB::getInstance();	
		
		$this->arrTree	= $objDB->fetchRow("cmstree", $this->treeid); //cmstree objektet for treeid
		$this->objTableView = new tuksiTableview($this->treeid, "cmstree");
	}
	
	public function getHTML() {
		
		$arrHtml = array();
		
		//no template set, need to be set before we can show any settings
		if(!$this->arrTree['pg_page_templateid']) {
			$this->noData = true;
			if (tuksiIni::$arrIni['setup']['system'] == 'backend') {
					//load cmssite id
					$this->hasData = false;
					$objPage = tuksiBackend::getInstance();
					if(($arrConf = tuksiConf::getPageConf($objPage->arrTree['id'])) === false) {

						$arrHtml[] = array('html' => $objPage->cmstext('missingsiteid'));
					
					} else {
						
						$objDB = tuksiDB::getInstance();					
						$sql = "SELECT p.id, p.name ";
						$sql.= "FROM pg_page_template p, cmscontrol c ";
						$sql.= "WHERE p.cmscontrolid = c.id AND p.isactive = 1 AND p.template_type = 1 AND c.cmssiteid = '".$arrConf['id']."'";	
						
						$rs = $objDB->fetch($sql);
						
						if($rs['num_rows'] == 0){

							$arrSiteConf = tuksiConf::getConf(); 
							$text = $objPage->cmstext('missingpagetemplate');
							$linkHtml = "<a href='" . tuksiTools::getBackendUrl($arrSiteConf['link']['pagetemplateadmin_treeid'],$arrSiteConf['link']['frontend_pagetemplateadmin_tabid']) . "'>link</a>";
							$text = str_replace("[link]",$linkHtml,$text);
							$arrHtml[] = array('html' => $text);
						
						} else {
							
							$this->objTableView->setField( "fieldSelectFromTable", 
																							$objPage->cmstext('pg_page_templateid'),
																							"pg_page_templateid",
																							"SELECT p.id, p.name FROM pg_page_template p, cmscontrol c WHERE p.cmscontrolid = c.id AND p.isactive = 1 AND p.template_type = 1 AND c.cmssiteid = '#CMSSITEID#'",
																							$arrOption['fieldvalue2'],
																							$arrOption['fieldvalue3'],
																							$arrOption['fieldvalue4'],
																							$arrOption['fieldvalue5']);
							$arrHtml = $this->objTableView->getFields();	
						}
					}
			}
			
		} else {
			$this->setData();
			$arrHtml = $this->objTableView->getFields();	
		}
		
		if(!$this->edit) {
			$field->rowid = $this->treeid;
			$objFieldLog = new fieldTuksiLog($field);
			$arr = $objFieldLog->getHtml();
			$arrHtml['suit'] = array('html' => '<div class="mHeader"><h6>Log</h6></div>','options' => array('fullwidth' => true));
			$arrHtml['log'] = array('html' => $arr['html']);
		}
		
		//get html
		
		
		return $arrHtml;
	}
	
	private function dbNameCheck($searchname, $treeid, $num=1) {
		
		// obj
		$objDB = tuksiDB::getInstance();
		
		// get existing name(s)
		$resGetMainName = $objDB->fetchItem("SELECT id, name, parentid FROM cmstree WHERE id = '{$treeid}'");
		if($resGetMainName['ok']) {
		
			$resGetNames = $objDB->fetch("SELECT id, name, parentid FROM cmstree WHERE name = '".$objDB->realEscapeString($searchname)."' AND parentid='".intval($resGetMainName['data']['parentid'])."' AND id != '{$treeid}' AND isdeleted = 0");
			if($resGetNames['ok'] && $resGetNames['num_rows'] > 0) {

				// num
				$num = intval($resGetNames['num_rows'] + $num);
				
				// change name
				$searchname = $searchname . " (".($num).")";
				
				// check again
				$searchname = $this->dbNameCheck($searchname, $treeid);
			} 
		}
		
		// return
		return($searchname);
	}
	
	public function save() {
		
		$this->setData();
		
		$arrTreePreSave = $this->arrTree;
		
		$this->objTableView->saveFields();
		
		$this->setData();
		
		if($this->arrTree['pg_page_templateid']) {
		
			$objDB = tuksiDB::getInstance();
			
			$name = $_POST->getStr("pg_menu_name_".$this->treeid);
			
			$name = $this->dbNameCheck($name, $this->treeid);
			
			$sqlUpd = "UPDATE cmstree SET name = '".$objDB->realEscapeString($name)."' WHERE id = '{$this->treeid}' ";
			$objDB->write($sqlUpd);
			
			//find out which data has been changed		
			$this->arrChanged = array();
			
			if($arrTreePreSave['pg_menu_name'] != $name) {
				tuksiTree::updateTreeUrl($this->treeid);
			}
			
			foreach($arrTreePreSave as $field => $value) {
				if($value != $this->arrTree[$field]) {
					$this->arrChanged[$field] = array("old" => $value, "new" => $this->arrTree[$field]);
				}
			}
			if(isset($this->arrChanged['pg_page_templateid'])) {
				$objPagegenerator = tuksiPageGenerator::getInstance();
				$objPagegenerator->resetModules($this->treeid,$this->tabid);
				$objPagegenerator->insertDefaultModules($this->treeid,$this->tabid,$this->arrChanged['pg_page_templateid']['new']);
			}
		}
	}
	
	public function templateChanged(){
		if(isset($this->arrChanged['pg_page_templateid'])) {
			return true;
		} else {
			return false;
		}
	}
	
	private function setData() {
		
		$objDB = tuksiDB::getInstance();	
		
		if($this->arrTree['pg_page_templateid']) {
			
			//set the options
			$this->setSettings();
		
			//overfører værdierne for det aktuelle tab til tableview
			$this->arrTree = $objDB->fetchRow("cmstree", $this->treeid); //cmstree objektet for treeid
			
		} else {
				
			if (tuksiIni::$arrIni['setup']['system'] == 'backend') {
				//load cmssite id
				
				$objPage = tuksiBackend::getInstance();
				
				if(($arrConf = tuksiConf::getPageConf($objPage->arrTree['id'])) !== false) {
					
					$objDB = tuksiDB::getInstance();					
					$sql = "SELECT p.id, p.name ";
					$sql.= "FROM pg_page_template p, cmscontrol c ";
					$sql.= "WHERE p.cmscontrolid = c.id AND p.isactive = 1 AND p.template_type = 1 AND c.cmssiteid = '".$arrConf['id']."'";	
					
					$rs = $objDB->fetch($sql);
					
					if($rs['num_rows'] > 0) {
						
						$this->objTableView->setField( "fieldSelectFromTable", 
																						$objPage->cmstext('pg_page_templateid'),
																						"pg_page_templateid",
																						"SELECT p.id, p.name FROM pg_page_template p, cmscontrol c WHERE p.cmscontrolid = c.id AND p.isactive = 1 AND p.template_type = 1 AND c.cmssiteid = '#CMSSITEID#'");
					}
				}
			}
		}
		$this->objTableView->addData($this->arrTree);
	}
	
	private function setSettings() {
		
		$objPage = tuksiBackend::getInstance();
		
		$arrOptions = $this->getOptions();
		
		if ($this->isNewsletter) {
			// nyhedsbrev side : pg_browser_title bruges som subject for nyhedsbrevet
			$this->objTableView->setField("fieldTextInput",  $objPage->cmsText("subject") . ":", "pg_browser_title");   
		}
		
		//set global options
		foreach ( $arrOptions['global'] AS $arrOption ) {
			$this->objTableView->setField( $arrOption['classname'], $arrOption['name']  . ":", $arrOption['colname'],$arrOption['fieldvalue1'],$arrOption['fieldvalue2'],$arrOption['fieldvalue3'],$arrOption['fieldvalue4'],$arrOption['fieldvalue5'],array('fielditemid' => $arrOption['fielditemid']));
		}
									  
		//set custom options
		foreach ( $arrOptions['custom'] AS $arrOption ) {
			$this->objTableView->setField( $arrOption['classname'], $arrOption['name']  . ":", $arrOption['colname'],$arrOption['fieldvalue1'],$arrOption['fieldvalue2'],$arrOption['fieldvalue3'],$arrOption['fieldvalue4'],$arrOption['fieldvalue5'],array('fielditemid' => $arrOption['fielditemid']));
		}
		
	}
	
	private function getOptions() {

		$objDB = tuksiDB::getInstance();	
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$arrGlobalOptions = array();
		
		if($this->edit) {
			$typeSql = "po.showin_edit=1";
		} else {
			$typeSql = "po.showin_settings=1";
		}
		
		$sqlGlobalOptions = "SELECT DISTINCT fi.id,fi.id as fielditemid, fi.name, fi.colname, po.showin_settings, po.showin_edit, ft.classname, fi.seq,fi.fieldvalue1,fi.fieldvalue2,fi.fieldvalue3,fi.fieldvalue4,fi.fieldvalue5, txt.value_" . tuksiIni::$arrIni['setup']['admin_lang'] . " AS langname ";
		$sqlGlobalOptions.= "FROM (cmsfielditem AS fi, cmsfieldtype AS ft, pg_option AS po, cmsfieldperm AS fp, cmsusergroup AS ug,pg_option_template ot) ";
		$sqlGlobalOptions.= "LEFT JOIN cmstext txt ON (txt.token = fi.name) ";
		$sqlGlobalOptions.= "WHERE ot.pg_page_templateid = '{$this->arrTree['pg_page_templateid']}' AND fi.id = ot.cmsfielditemid AND fi.itemtype='option' AND fi.cmsfieldtypeid=ft.id AND po.cmsfielditemid=fi.id AND $typeSql AND po.isglobal=1 ";
		$sqlGlobalOptions.= "AND fi.id=fp.cmsfielditemid AND fp.cmsgroupid=ug.cmsgroupid AND ug.cmsuserid='{$arrUser['id']}' ";
		$sqlGlobalOptions.= "ORDER BY fi.seq";
		
		$arrRsGlobalOptions = $objDB->fetch( $sqlGlobalOptions );
		
		foreach ( $arrRsGlobalOptions['data'] AS &$arrGlobalOption ) {
			if (!empty($arrGlobalOption['langname']))
				$arrGlobalOption['name'] = $arrGlobalOption['langname'];
				
			$arrGlobalOptions[$arrGlobalOption['colname']] = $arrGlobalOption;
		}
		
		$arrOptions = array();
				
		$sqlOptions = "SELECT DISTINCT fi.id,fi.id as fielditemid, fi.name, fi.colname, po.showin_settings, po.showin_edit, ft.classname, fi.seq,fi.fieldvalue1,fi.fieldvalue2,fi.fieldvalue3,fi.fieldvalue4,fi.fieldvalue5, txt.value_" . tuksiIni::$arrIni['setup']['admin_lang'] . " AS langname ";
		$sqlOptions.= "FROM (cmsfielditem AS fi, cmsfieldtype AS ft, pg_option AS po, cmsfieldperm AS fp, cmsusergroup AS ug) ";
		$sqlOptions.= "LEFT JOIN cmstext txt ON (txt.token = fi.name) ";
		$sqlOptions.= "WHERE fi.itemtype='option' AND fi.relationid='{$this->arrTree['pg_page_templateid']}' AND fi.cmsfieldtypeid=ft.id AND po.cmsfielditemid=fi.id AND $typeSql AND po.isglobal=0 ";
		$sqlOptions.= "AND fi.id=fp.cmsfielditemid AND fp.cmsgroupid=ug.cmsgroupid AND ug.cmsuserid='{$arrUser['id']}' ";
		$sqlOptions.= "ORDER BY fi.seq";
		
		$ArrRsOptions = $objDB->fetch( $sqlOptions );
		
		foreach($ArrRsOptions['data'] as &$arrOption) {
			if (!empty($arrOption['langname']))
				$arrOption['name'] = $arrOption['langname'];

			$arrOptions[$arrOption['colname']] = $arrOption;
		}
		
		$arrAllOptions = array();
		$arrAllOptions['global'] = $arrGlobalOptions;
		$arrAllOptions['custom'] = $arrOptions;
		
		return $arrAllOptions;
	}
}

?>
