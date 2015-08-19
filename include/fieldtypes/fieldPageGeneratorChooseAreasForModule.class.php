<?php

/**
 * Enter description here...
 *
 *@package tuksiFieldType 
 */

class fieldPageGeneratorChooseAreasForModule extends field {

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {

		$objDB = tuksiDB::getInstance();
		
		$HtmlTag = parent::getHtmlStart();

		$arrModule = array("htmltagname" => $this->objField->htmltagname);

		$tpl = new tuksiSmarty();

		$sqlType = "";
		
		if(is_array($this->objField->rowData) && count($this->objField->rowData) > 0) {
			if(	$this->objField->rowData['moduletype'] == "standard" || 
					$this->objField->rowData['moduletype'] == "custom") {
				$sqlType = "AND p.template_type = 1 ";
			} else if($this->objField->rowData['moduletype'] == "newsletter"){
				$sqlType = "AND p.template_type = 2 ";
			} else if($this->objField->rowData['moduletype'] == "backend"){
				$sqlType = "AND p.template_type = 3 ";
			}
		}
		
		$sqlArea = "SELECT a.id, concat(p.name,' - ', a.name) AS name, am.id as contentAreaChoosen ";
		$sqlArea.= "FROM pg_page_template p, pg_contentarea a ";
		$sqlArea.= "LEFT JOIN pg_allowedmodules am on (a.id = am.pg_contentareaid AND am.pg_moduleid = '{$this->objField->rowid}')";
		$sqlArea.= "WHERE a.pg_page_templateid = p.id ";
		$sqlArea.= $sqlType;
		$sqlArea.= "ORDER BY p.name, a.name";

		$arrArea = $objDB->fetch($sqlArea);

		$arrAreas = $arrArea['data'];
		
		$html_add = tuksiFormElements::getButton(array(
														'icon' => 'add',
														'value' => 'Tilføj'
		));
		$html_delete = "";
		
		foreach($arrAreas as $areas) {
			if($areas['contentAreaChoosen']) {
			$html_delete.=  '<tr><td>'.$areas['name'].'</td><td>' . tuksiFormElements::getButton(array(
														"icon" => "delete",
														"value" => "Slet",
														"onclick" => "setValue('".$this->objField->htmltagname."_delete', '".$areas['contentAreaChoosen'] . "');")) . '</td></tr>';
		}
		}
		
		
		$tpl->assign("tilfoj",$html_add);
		$tpl->assign("slet",$html_delete);
		$tpl->assign('area', $arrAreas);
		$tpl->assign('module', $arrModule);

		
		$HtmlTag.= $tpl->fetch('fieldtypes/fieldPageGeneratorChooseAreasForModule.tpl');
	
		return parent::returnHtml($this->objField->name, $HtmlTag);
	}

	function saveData() {
	
		$objDB = tuksiDB::getInstance();
		
		if ($_POST->getInt($this->objField->htmltagname . '_delete')) {
			$sqlDelete = "DELETE FROM pg_allowedmodules ";
			$sqlDelete.= "WHERE id = '{$_POST->getInt($this->objField->htmltagname . '_delete')}'";
			$objDB->write($sqlDelete);
		}
		
		if ($_POST->getInt($this->objField->htmltagname . '_new')) {
			$sqlAdd = "INSERT INTO pg_allowedmodules (pg_moduleid, pg_contentareaid) ";
			$sqlAdd.= "VALUES ('{$this->objField->rowid}', '{$_POST->getInt($this->objField->htmltagname . '_new')}')";
			$objDB->write($sqlAdd);
		}
		
		return;
	}
	function getListHtml() {
		return "";
	}

	function deleteData() {

		$objDB = tuksiDB::getInstance();
		
		$sqlDelete = "DELETE FROM pg_allowedmodules WHERE pg_moduleid = '{$this->objField->rowid}'";
		$objDB->write($sqlDelete);
	}

	function copyData($rowid_to) {

		$objDB = tuksiDB::getInstance();
		
		$sql = "INSERT INTO pg_allowedmodules (pg_contentareaid, pg_moduleid) ";
		$sql.= "SELECT pg_contentareaid, concat('{$rowid_to}') FROM pg_allowedmodules WHERE pg_moduleid = '{$this->objField->rowid}'";

		$rs = $objDB->write($sql);
	}

} // END Class
?>
