<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendElements extends mBackendBase {

	private $tablename,$sqlElement;
	private $arrFields = array();
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		
		$this->tablename = $objMod->value1;
		
		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		if (!$this->tablename) 
			$this->tablename = "pageelement";
	
		$this->sqlElement = "SELECT DISTINCT fi.*, f.classname, f.special_release, f.special_delete, d.content AS value, d.id AS dataid, SUM(fp.psave) AS saveperm, fi.id AS fielditemid, txt.value_" . tuksiIni::$arrIni['setup']['admin_lang'] . " AS langname ";
		$this->sqlElement.= "FROM cmsfielditem fi ";
		$this->sqlElement.= "INNER JOIN cmsfieldtype f ON (fi.cmsfieldtypeid = f.id) ";
		$this->sqlElement.= "INNER JOIN cmsfieldperm fp ON (fi.id = fp.cmsfielditemid) ";
		$this->sqlElement.= "INNER JOIN cmsusergroup ug ON (fp.cmsgroupid = ug.cmsgroupid) ";
		$this->sqlElement.= "INNER JOIN cmsfieldgroup g ON (fi.cmsfieldgroupid = g.id) ";
		$this->sqlElement.= "INNER JOIN cmsfielddata d ON (d.cmsfielditemid = fi.id AND d.rowid = fi.id) ";
		$this->sqlElement.= "LEFT JOIN cmstext txt ON (txt.token = fi.name) ";
		$this->sqlElement.= "WHERE fi.itemtype = 'element' AND ug.cmsuserid = '{$arrUser['id']}' AND fi.relationid = '{$objPage->tabid}' AND fi.colname <> '' AND fp.pread = '1' ";
		$this->sqlElement.= "GROUP BY fi.id ";
		$this->sqlElement.= "ORDER BY g.seq DESC, fi.seq";			
	}	

	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		
		$objPage->addBookmark();
		
		$this->objStdTpl = new tuksiStandardTemplateControl();
		$this->objStdTpl->addHiddenField(array("name" => "treeid","value" => $objPage->treeid));
		$this->objStdTpl->addHiddenField(array("name" => "rowid","value" => $_GET->getInt('ROWID')));
		$this->objStdTpl->addHiddenField(array("name" => "tabid","value" => $this->tabid));
			
		if(($objPage->action == "SETUP" || $_POST->getStr('element_setup')) && !$this->userActionIsSet('BACK')) {
			$html = $this->getSetup();
		} else {
			$html = $this->getDefault();
		}
		$this->objStdTpl->addElement("",$html);
		
		return $this->objStdTpl->fetch();
	}
	
	private function getDefault(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		if($this->userActionIsSet('SAVE') && $objPage->arrPerms["SAVE"]) {
			
			$this->saveDefault();
			$objPage->status($objPage->cmsText('page_saved'));
		}
		
		$ResultElements = $objDB->fetch($this->sqlElement,array("type" => "object")); 
		$lastGroupid = 0;

		foreach ($ResultElements['data'] as $MyField) {
			
			
			//$objPage->alert(print_r($MyField, 1));
			
			if ($lastGroupid != $MyField->cmsfieldgroupid) {
				$SQL = "SELECT name FROM cmsfieldgroup WHERE id = '$MyField->cmsfieldgroupid'";
				$rs = $objDB->fetch($SQL);
				$groupname = $rs['data'][0]['name'];
				$this->objStdTpl->addheadline($groupname);
				
			}
			// Setting fieldvalues
			
			$MyField->htmltagname 	= "TABLE_" . $MyField->colname; 
			$MyField->vcolname		= $MyField->colname; 
			$MyField->value			= $MyField->value; 
			$MyField->fielditemid = $MyField->id; 
			$MyField->rowid 		= $MyField->dataid;
			$MyField->colname 		= "content";
			$MyField->tablename 	= "cmsfielddata";
			$MyField->readonly 		= $MyField->saveperm ? false : true;
	
			if ($this->arrFields[$MyField->id]){
				$objField = &$this->arrFields[$MyField->id];
				$objField->setObjField($MyField);
			} else {
				$objField = new $MyField->classname($MyField, $objPage);
			}
						
			$arrData = $objField->getHTML();
			if ($MyField->langname) {
				$arrData['name'] = $MyField->langname;
			}

			if($releaseElements && $MyField->special_release) {
				$objField->releaseData();
			}

			$this->objStdTpl->addElement($arrData['name'], $arrData['html']);	
	
			if($releaseElements) {
				tuksiRelease::releaseTableRowRaw('cmsfielddata',$MyField->dataid);
			}
				
			$lastGroupid = $MyField->cmsfieldgroupid;	
		}
		$this->addButton("SETUP","Setup","ADMIN");	
		$this->addButton("BTNSAVE","","SAVE");	
	}
	
	private function getSetup(){
		
		$tplAdmin = new tuksiSmarty();
		$objPage = tuksiBackend::getInstance();
		
		$objFieldItem = new tuksiFielditemElement($objPage->tabid);
		
		$arrFields = $objFieldItem->getFields();
		
		if($this->userActionIsSet('SAVE') && $objPage->arrPerms["SAVE"]) {
			$objFieldItem->saveData($arrFields,true);
			$arrFields = $objFieldItem->getFields();
		}
		
		$tplAdmin->assign("error_add", $arrReturn['add']['error']);
		$tplAdmin->assign("fields", $objFieldItem->getHTML($arrFields) );
		
		// indsæt element_admin skabelon i standard side.
		$this->addButton("BTNBACK","","READ");
		$this->addButton("BTNSAVE","","SAVE");
		
		return $tplAdmin->fetch('element_admin.tpl');
	}
	
	private function saveDefault(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$rsElements = $objDB->fetch($this->sqlElement,array("type" => "object")); 
		
		foreach ($rsElements['data'] as $MyField) {
			
			if($MyField->saveperm) {
				
				$MyField->value 		= $_POST->getStr("TABLE_" . $MyField->colname); 
				$MyField->htmltagname 	= "TABLE_" . $MyField->colname;
				$MyField->rowid 		= $MyField->dataid;
				$MyField->vcolname		= $MyField->colname; 
				$MyField->colname 		= "content";
				$MyField->tablename 	= "cmsfielddata";
				
				$this->arrFields[$MyField->id] = new $MyField->classname($MyField, $objPage);
				$objField = &$this->arrFields[$MyField->id];
				$sqlPart	= $objField->saveData();
				
				if ($sqlPart) {
					
					$sqlChk = "SELECT * FROM cmsfielddata WHERE cmsfielditemid = '" . $MyField->id . "' AND rowid = '" . $MyField->id . "'";
					$rsChk = $objDB->fetch($sqlChk);
					
					if($rsChk['num_rows'] == 0) {
		
						$sqlIns = "INSERT INTO cmsfielddata (cmsfielditemid,rowid,dateadded) VALUES ";
						$sqlIns.= "('{$MyField->id}','{$MyField->id}',now())";
						$objDB->write($sqlIns) or print mysql_error().$sqlIns;
						
					} 
					
					$sqlUpdataData = "UPDATE cmsfielddata SET $sqlPart WHERE cmsfielditemid = '" . $MyField->id . "' AND rowid = '" . $MyField->id . "'";
				 	$rsUpdataData = $objDB->write($sqlUpdataData);
					
				}
			}
		}
	}
	
	function saveData(){
		
	}
}
?>
