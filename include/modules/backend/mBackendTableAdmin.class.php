<?
/**
 * list the contents of a given table
 * value 1 = tablename
 * value 2 = tablelayoutid
 * 
 * 
 * @uses tuksiSmarty
 * @uses tuksiDB
 * @uses tuksiBackend
 * @uses tuksiStandardTemplateControl
 * @package tuksiBackendModule
 */

class mBackendTableAdmin extends mBackendBase {
	
	public $tpl;
	private $currentTable,$currentDB, $tablelayoutid;
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		
		$this->tpl = new tuksiSmarty();

		$fromGet = false;
		
		if($_POST->getStr('db')) {
			$this->currentDB = $_POST->getStr('db');
		} else if ($_GET->getStr('db') ){
			$this->currentDB = $_GET->getStr('db');
			$fromGet = true;
		}
		
		if($_POST->getStr('table')) {
			$this->currentTable = $_POST->getStr('table');
		} else if ($_GET->getStr('table') ){
			$this->currentTable = $_GET->getStr('table');
		}
		
		if(!$fromGet) {
			if($this->currentDB != $_POST->getStr('currentDB')) {
				$this->currentTable = "";
			}
		}
		
		if($_POST->getStr('layout')) {
			$this->tablelayoutid = $_POST->getStr('layout');
		} else if ($_GET->getStr('layout') ){
			$this->tablelayoutid = $_GET->getStr('layout');
		}
			
	}

	public function getHTML(){
		
		$objDB = tuksiDB::getInstance();
		
		$objPage = tuksiBackend::getInstance();
		$objStdTpl = new tuksiStandardTemplateControl();
		
		$this->addButton("BTNBACK","","READ");
		
		if($objPage->action == "SAVE") {
			$this->save();
		} else if ($objPage->action == "DELETE"){
			$this->delete();
		} else if($objPage->action == "ADD"){
			$this->add();
		} else if($objPage->action == "COPY"){
			$this->copy();
		} 
		
		$arrDBs = $objDB->getDatabases();
		
		if(count($arrDBs) > 1) {
				$this->tpl->assign("databases",$arrDBs);
		} 
		
		if(!$this->currentDB) {
			//get default db
			$this->currentDB = $objDB->arrSetup['dbname'];
		}
		
		$arrTables = $objDB->getTables($this->currentDB);
		
		$this->tpl->assign("tables",$arrTables);
		
		if($this->currentTable) {
			if(!$this->checkTable()) {
				$this->tablelayoutid = $this->createLayout();
			}
			$arrLayouts = $this->getTableLayouts();		
			$this->tpl->assign("layouts",$arrLayouts);
			
			if($this->tablelayoutid) {
				
				$this->tpl->assign("layoutname",$this->layoutname);
				
				$htmlFields = $this->getFields();
				$this->tpl->assign("fields",$htmlFields);
				$this->addButton("SAVE","","SAVE");				
				$this->addButton("DELETE","","DELETE");				
				$this->addButton("ADD","","ADD");
				$this->addButton("COPY","","ADD");
				
				$exportUrl = $this->getExportLayoutUrl();
				$onclick = "tuksi.util.openPage('$exportUrl')";
				
				$this->addButton("EXPORT","","ADD","",$onclick);
			}
		}
		
		$this->tpl->assign("currentTable",$this->currentTable);
		$this->tpl->assign("currentDB",$this->currentDB);
		
		$objStdTpl->addElement("",$this->tpl->fetch("modules/backend/mBackendTableAdmin.tpl", $this->objMod->id));
		return $objStdTpl->fetch();
	}
	
	private function save() {
		
		$objDB = tuksiDB::getInstance();
		
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$objFieldItem = new tuksiFielditemTable($tablename, $this->tablelayoutid);
	
		$objFieldItem->updateTableLayoutName($_POST->getStr('name'));	
		
		// --------------------------------------------------------------------------------------
		// Getting fields in table
		// --------------------------------------------------------------------------------------
		
		$objFieldItem->prepareItem();
	
		$fields = mysql_list_fields($this->currentDB, $this->currentTable);
		$columns = mysql_num_fields($fields);
		$arrFields = array();
		
		for ($i = 0; $i < $columns; $i++) { 
			$colname = mysql_field_name($fields, $i);
			$arrFields[] = $colname;
		
		}
		
		mysql_select_db($objDB->arrSetup['dbname']);
		
		$objFieldItem->saveData($arrFields);	
		
		$objFieldItem->cleanupItem();
		
	}
	
	private function delete(){
			
		$objDB = tuksiDB::getInstance();
		
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$objFieldItem = new tuksiFielditemTable($tablename, $this->tablelayoutid);
				
		$objFieldItem->deleteTableLayout();
				
		$this->tablelayoutid = 0;
	}
	
	private function add(){
  	$this->tablelayoutid = $this->createLayout("Layout name");		
	}
	
	private function copy(){
		
		$objDB = tuksiDB::getInstance();
		
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		//crate new empty layout
		$intNewTablelayoutId = $this->createLayout("Copied layout");
		
		$objFieldItem = new tuksiFielditemTable($tablename, $this->tablelayoutid);
		
		$objFieldItem->copyItems($intNewTablelayoutId);
		
		$this->tablelayoutid = $intNewTablelayoutId;
	
	}
	
	private function getExportLayoutUrl(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$url = "/services/backend/tableLayoutXml.php?tablename=" . $tablename . "&layoutid=" . $this->tablelayoutid;
		
		return $url;
		
		// Tilføj script til onload
	}
	
	// * --------------------------------------------------------------------------*
	// Check if table has an layout and if layout is set is correct
	// * --------------------------------------------------------------------------*
	
	private function checkTable() {
		
		$objDB = tuksiDB::getInstance();
		
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$sqlLayout = "SELECT id FROM cmstablelayout WHERE tablename = '{$tablename}'";
		$arrRsLayout = $objDB->fetch($sqlLayout);
		
		if ($arrRsLayout['num_rows'] > 0) { 
		  if($this->tablelayoutid) {
		  	$arrIds = array();
		  	foreach($arrRsLayout['data'] as &$arrData) {
		  		$arrIds[] = $arrData['id'];
		  	}
		  	if(!in_array($this->tablelayoutid,$arrIds)) {
		  		$this->tablelayoutid = 0;
		  	}
		  }
			return true;
		}
		return false;
	}
	
	private function createLayout($name = "") {
		
		$objDB = tuksiDB::getInstance();

		// setting default layoutname 
		$name = ($name) ? $name : "Standard";
			
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$sql = "INSERT INTO cmstablelayout(name, tablename) VALUES('{$name}', '{$tablename}')";
		$res = $objDB->write($sql) or exit("Error when inserting new layout, sql: $sql ");
		
		// getting new id
		$newtableid = $res['insert_id'];
		
		return $newtableid;
	}
	
	private function getTableLayouts(){
		
		$objDB = tuksiDB::getInstance();
		
		// * --------------------------------------------------------------------------*
		// Getting layouts
		// * --------------------------------------------------------------------------*
		
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$sqlTableLayout = "SELECT id, name FROM cmstablelayout ";
		$sqlTableLayout.= "WHERE tablename = '{$tablename}' ORDER BY id";
		$rsTableLayout = $objDB->fetch($sqlTableLayout);
	
		$arrLayouts = array();
		
		foreach ($rsTableLayout['data'] as $arrTableLayout) {
		
			// Use/show first layout if none is selected
	
			if (!$this->tablelayoutid) {
				$this->tablelayoutid = $arrTableLayout['id'];
			}
			if($this->tablelayoutid == $arrTableLayout['id']) {
				$bSelected = true;
				$this->layoutname = $arrTableLayout['name'];	
			} else {
				$bSelected = false;
			}
	
			$arrLayouts[] = array('selected' => $bSelected, 
														'id' => $arrTableLayout['id'], 
														'name' => "{$arrTableLayout['name']} ({$arrTableLayout['id']})" );
			
		} //while tablelayouts
		return $arrLayouts;
	}
	
	// * -------------------------------------------------------------------------------------- * 
	// Getting html fields in table ( TABLENAME ) 
	// * -------------------------------------------------------------------------------------- * 
	
	private function getFields(){
		
		$objDB = tuksiDB::getInstance();
			
		if($this->currentDB != $objDB->arrSetup['dbname']) {
			$tablename = $this->currentDB . "." . $this->currentTable;
		} else {
			$tablename = $this->currentTable;
		}
		
		$sqlColumns = "SHOW COLUMNS FROM {$tablename}";
		$arrColumns = $objDB->fetch($sqlColumns);
		$arrColnames = array();
		
		foreach($arrColumns['data'] as &$arrItem) {
			$arrColnames[] = $arrItem['Field'];
		}
		
		$objFieldItem = new tuksiFielditemTable($tablename, $this->tablelayoutid);
		$htmlFieldItems = $objFieldItem->getHTML($arrColnames);	
			
		return $htmlFieldItems;
	}
}	
