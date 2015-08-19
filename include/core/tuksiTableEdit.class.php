<?php

class tuksiTableEdit{
	
	private $tablename,$rowid,$tablelayoutid,$rowData;
	private $arrCols = array();
	private $arrFieldItems = array();
	
	
	public function __construct($tablename,$rowid,$tablelayoutid){
		$this->tablename = $tablename;
		$this->rowid = $rowid;
		$this->tablelayoutid = $tablelayoutid;
		
		// Henter række data
		$objDB = tuksiDB::getInstance();
		$this->rowData = get_object_vars($objDB->fetchRow($this->tablename, $this->rowid, 'object'));
	}
	
	public function getHTML(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$objStdTpl = new tuksiStandardTemplateControl();
		
		$objStdTpl->addHiddenField(array("NAME" => "rowid","VALUE" => $this->rowid));
		
		// * --------------------------------------------------------------------- *
		// Henter felter der skal vises. 
		// * --------------------------------------------------------------------- *
		$this->setCols();

		if (count($this->arrCols)) {
			// * --------------------------------------------------------------------------------------- *
			// Getting html fields in table ( TABLENAME ) 
			// * --------------------------------------------------------------------------------------- *
			
			foreach ($this->arrCols as &$arrGroup) {
				 
			 	if (count($arrGroup['fields'])) {
			 		
			 		$objStdTpl->addHeadline($arrGroup['name'],$arrGroup['collapsible'], $arrGroup['is_collapsed']);
			 		
			 		foreach ($arrGroup['fields'] as &$objField) {
		
						// Sætter data 
						$objField->value = $this->rowData[$objField->colname];
			 				 								
						if ($this->arrFieldItems[$objField->id]){
							$objFieldIns = &$this->arrFieldItems[$objField->id];
							$objFieldIns->setObjField($objField);
						} else {
							$objFieldIns = new $objField->classname($objField, $objPage);
						}
							
						$arrData = $objFieldIns->getHTML();
						// Hvis navn er sat bruges 2 kolonne skabelonen, eller rykkes htmldata helt til venstre
						$objStdTpl->addElement($arrData['name'],$arrData['html'],$arrData['options']);
					} // End while felttype
				} // Hvis felter i gruppen	
			} // End foreach gruppe
		} // End hvis feltlist $arrsqlFields fundet
		else {
		 	$objStdTpl->addHeadline("Fejl");
		 	$objStdTpl->addElement("Ingen felter fundet");
		}
		return $objStdTpl->fetch();
	}
	
	public function saveData(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
			
		$this->setCols();
		
		$values = array();
		
		foreach ($this->arrCols as &$arrGroup) {
		
			if (count($arrGroup['fields'])) {
			 		 	
				foreach ($arrGroup['fields'] as &$objField) {
					
					if($objField->saveperm) {
						
						$values[$objField->colname] = $objField->value 	= $_POST->getStr('TABLE_' . $objField->colname); 		
						
						// Gem object til gethtml
						$this->arrFieldItems[$objField->id] = new $objField->classname($objField, $objPage);
						$objFieldIns = &$this->arrFieldItems[$objField->id];
						$sqlPart  = $objFieldIns->saveData();
				
						if ($sqlPart) {
							$sqlSave	= "UPDATE {$this->tablename} SET $sqlPart WHERE id = '{$this->rowid}'";
							
							$rs = $objDB->write($sqlSave) or tuksiDebug::error("Error saving:" . mysql_error());
						}
					}
				} // End foreach felttype
			} // Hvis felter
		} // End foreach felttype
		$objDebug = tuksiDebug::getInstance();
		$objDebug->eventlog(11,$arrUser['id'], $this->tablename,$this->rowid);
	}
	
	public function deleteData(){
		
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$objTuksiTable = new tuksiTable($this->tablename);
	
		$status = $objTuksiTable->deleteRow($this->rowid, $this->tablelayoutid);
		
		if($status) {
			$objDebug = tuksiDebug::getInstance();
			$objDebug->eventlog(10,$arrUser['id'],$this->tablename,$this->rowid);
		}	
		
		return $status;
	}
	
	private function setCols(){
		
		$this->arrCols = array();
		
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$objFieldItem = new tuksiFielditemTable($this->tablename, $this->tablelayoutid);
		
		list($arrFields, $arrSqlFields) = $objFieldItem->getFieldsToShow($arrUser['id'], 'read');
				
		// Making fieldlist for sqlCols
		$sqlFields = join(" OR " , $arrSqlFields);
				
		$sqlCols = "SELECT fi.*,fi.id as fielditemid,fg.collapsible as cob, fg.is_collapsed as is_cob, fg.id AS groupid, fg.name AS groupname, ft.classname, SUM(fp.psave) as saveperm,  ct.value_".tuksiIni::$arrIni['setup']['admin_lang']." as langname	";
		$sqlCols.= "FROM (cmsfielditem fi, cmsfieldtype ft, cmsfieldgroup fg, cmsfieldperm fp, cmsusergroup ug) ";
		$sqlCols.= "LEFT JOIN cmstext ct ON (ct.token = fi.name) ";
		$sqlCols.= "WHERE fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fi.id = fp.cmsfielditemid AND fi.itemtype = 'table' AND ft.id = fi.cmsfieldtypeid AND fg.id = fi.cmsfieldgroupid AND fi.tablename = '{$this->tablename}' AND ($sqlFields) AND fi.relationid = '{$this->tablelayoutid}' ";
		$sqlCols.= "GROUP BY fi.id ";
		$sqlCols.= "ORDER BY fg.seq, fi.seq";
		
		$rsCols = $objDB->fetch($sqlCols,array('type' => 'object'));

		foreach ($rsCols['data'] as $objField) {
			
			// Sætter standard værdier til hver felt
			if (!empty($objField->langname))
				$objField->name = $objField->langname;
			
			$objField->htmltagname	= 'TABLE_' . $objField->colname;
			$objField->vcolname 	= $objField->htmltagname; 
			$objField->rowid 		= $this->rowid;
			$objField->value 		= $this->rowData[$objField->colname];
			$objField->rowData		= $this->rowData;
			$objField->readonly 	= $objField->saveperm ? false : true;			
			
			$this->arrCols[$objField->groupid]['id'] = $objField->groupid;			
			$this->arrCols[$objField->groupid]['name'] = $objField->groupname;
			$this->arrCols[$objField->groupid]['fields'][] = $objField;
			$this->arrCols[$objField->groupid]['collapsible'] = $objField->cob; 
			$this->arrCols[$objField->groupid]['is_collapsed'] = $objField->is_cob; 

		
		} // End While field
		
		// Henter række data
		$this->rowData = get_object_vars($objDB->fetchRow($this->tablename, $this->rowid, 'object'));
	}
}
?>
