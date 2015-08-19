<?php

/**
 * Template Class
 *
 * @package tuksiFieldType
 */

class fieldPageGeneratorAllowedModules extends field {

	function __construct($objField) {
		
		parent::field($objField);
	}

	function getHTML(){
		
		$tpl = new tuksiSmarty();
		$objDB = tuksiDB::getInstance();
		
		
		$sqlType = "";
		
		//try to find out wich type of page template we are showing
		if($_GET->getInt('rowid') > 0 || $_POST->getInt('rowid') > 0) {
			if($_GET->getInt('rowid') > 0) {
				$rowId = $_GET->getInt('rowid');
			} else {
				$rowId = $_POST->getInt('rowid');
			}

			$sqlTemplate = "SELECT * FROM pg_page_template WHERE id = '{$rowId}' ";
			$arrRsTemplate = $objDB->fetchItem($sqlTemplate);
			if($arrRsTemplate['num_rows'] > 0) {
				$arrTemplate = $arrRsTemplate['data'];
				if($arrTemplate['template_type'] == 3) {
					$sqlType = " WHERE m.moduletype = 'backend' ";
				} elseif($arrTemplate['template_type'] == 1){
					$sqlType = " WHERE (m.moduletype = 'standard' OR m.moduletype= 'custom') ";
				} elseif($arrTemplate['template_type'] == 2){
					$sqlType = " WHERE m.moduletype = 'newsletter' ";
				}
			}
		}

		$sqlModule = "SELECT m.id, m.name,m.isactive, d.id as checked " ;
		$sqlModule.= "FROM pg_module m " ;
		$sqlModule.= "LEFT JOIN pg_allowedmodules d ON (m.id = d.pg_moduleid AND d.pg_contentareaid = '{$this->objField->rowid}')";
		$sqlModule.= $sqlType;
		$sqlModule.= "GROUP BY m.id ORDER BY m.name";

		$arrRsModule = $objDB->fetch($sqlModule);
		if($arrRsModule['num_rows'] > 0) {
			$arrModules = array();
			foreach ($arrRsModule['data'] as $arrModule) {
				$arrModules[] = array(	"tagname" 		=> $this->objField->htmltagname,
												"id" 				=> $arrModule['id'],
												"checked" 		=> $arrModule['checked'],
												"name" 			=> $arrModule['name'],
												"isactive" 		=> $arrModule['isactive']);
			}	
		}
		$tpl->assign("modules",$arrModules);
		
		return parent::returnHtml($this->objField->name, $tpl->fetch("fieldtypes/fieldPageGeneratorAllowedModules.tpl"));
	}

	function saveData() {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlDel = "DELETE FROM pg_allowedmodules WHERE pg_contentareaid = '{$this->objField->rowid}'";
		$objDB->write($sqlDel);
		
		$sqlModule = "SELECT * FROM pg_module m " ;
		$arrRsModule = $objDB->fetch($sqlModule);
		$tag = $this->objField->htmltagname;
		foreach ($arrRsModule['data'] as $arrModule) {
			if ($_POST->getStr($tag . "_" . $arrModule['id'])) {
				$sqlIns = "INSERT INTO pg_allowedmodules (pg_contentareaid,pg_moduleid) VALUES ";
				$sqlIns.= "('{$this->objField->rowid}','{$arrModule['id']}')"; 
				$objDB->write($sqlIns);
			}
		}	
	}
	
	function getListHtml() {
		return "";
	}
	
	function copyData($to_row) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlModule = "SELECT m.id, m.name, d.id as checked " ;
		$sqlModule.= "FROM pg_module m " ;
		$sqlModule.= "LEFT JOIN pg_allowedmodules d ON (m.id = d.pg_moduleid AND d.pg_contentareaid = '{$this->objField->rowid}')";
		$sqlModule.= "GROUP BY m.id ORDER BY m.name";
		$arrRsModule = $objDB->fetch($sqlModule);
		if($arrRsModules['num_rows'] > 0) {
			foreach ($arrRsModules['data'] as $arrModule) {
			   if ($arrModule['checked']) {
					$sql = "INSERT INTO pg_allowedmodules (pg_contentareaid,pg_moduleid) VALUES ";
					$sql.= "('" . $to_row . "','{$arrModule['id']}')"; 
					$objDB->write($sql);
				}
			}	
		}
	}
} // END Class
?>
