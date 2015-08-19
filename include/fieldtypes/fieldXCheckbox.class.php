<?php

/**
 * 
 * X Checkboxs from other table 
 * objField->fieldvalue1 = Data table for list
 * objField->fieldvalue2 = Relation table
 * objField->fieldvalue3 = Condition fieldname
 * objField->fieldvalue4 = Special field as name (Default: name)
 * objField->fieldvalue5 = Special field as relation (Default: fieldvalue1 + "id")
 * arrFieldvalues['FIELDVALUE6'] = Additional where clause
 * arrFieldvalues['FIELDVALUE7'] = Special order by (Default: name asc)
 * 
 * Extra fields has to be setup in Tuksi
 *
 * @package tuksiFieldType
 */

class fieldXCheckbox extends field {
	
	function __construct($objField) {
		parent::field($objField, true);
		$this->objField = $objField;
	}
	
	function getHTML() {

		$tpl = new tuksiSmarty();	
		$objDB = tuksiDB::getInstance();
			
		if ($this->objField->fieldvalue3) {	
			$sql = "SELECT ".$this->objField->fieldvalue3." AS id FROM ".$this->objField->tablename." WHERE id = '".$this->objField->rowid."'";
			$rs = $objDB->fetchItem($sql);
			if ($rs['ok'] && $rs['num_rows'] == 1) {
				$sqlWhere = "WHERE t1.".$this->objField->fieldvalue3." = ".$rs['data']['id'];	
			}

			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere.= "AND ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		} else {
			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere = "WHERE ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		}
		if ($this->arrFieldvalues['FIELDVALUE8']) {
			$arrParam = explode('=', $this->arrFieldvalues['FIELDVALUE8']);

			$sqlONWhere .= "AND {$arrParam[0]} = '{$arrParam[1]}'";


		}

		// Special field as name
		$name = "name";
		if ($this->objField->fieldvalue4) {
			$name = $this->objField->fieldvalue4;
		}

		// Special field as relation
		$relname = $this->objField->fieldvalue1."id";
		if ($this->objField->fieldvalue5) {
			$relname = $this->objField->fieldvalue5;
		}

		// Special order by
		$orderBy = "t1.name, t1.id";
		if ($this->arrFieldvalues['FIELDVALUE7']) {
			$orderBy = $this->arrFieldvalues['FIELDVALUE7'];
		}
		
		$sql = "SELECT t1.id, t1.$name AS name, t2.id AS checked " ;
		$sql.= "FROM " . $this->objField->fieldvalue1 . " t1 " ;
		$sql.= "LEFT JOIN " . $this->objField->fieldvalue2 . " t2 ON (t1.id = t2." . $relname." AND t2." . $this->objField->tablename. "id = '{$this->objField->rowid}' $sqlONWhere) ";
		$sql.= $sqlWhere;
		$sql.= " GROUP BY t1.id ORDER BY $orderBy";
		$rs = $objDB->fetch($sql);

		$arrCheckboxes = array();
		if ($rs['ok']) {
			foreach ($rs['data'] as $arrData) {
				$arrTags = array();
				
				$arrTags['name'] = $this->objField->htmltagname . "_" . $arrData['id'];

				if ($arrData['checked']) {
					$arrTags['checked'] = "CHECKED";
				} else {
					$arrTags['checked'] = "";
				}
				$arrTags['value'] = $arrData['name'];
				
				$arrCheckboxes[] = $arrTags;
			}
		}

		$arrInputs = array();
		if (count($arrCheckboxes)) {
			foreach ($arrCheckboxes as $id => $check) {
				$arrInputs[] = array(
						'input' => tuksiFormElements::getCheckBox(array(
										'checked' => $check['checked'],
										'name' => $check['name'],
										'id' => $check['name'],
										'disabled' => $this->objField->readonly)),
						'name' => $check['value'],
						'boxid' => $check['name']);
			}
		}
		$tpl->assign("arrInputs", $arrInputs);
		
		$html = $tpl->fetch("fieldtypes/fieldXCheckbox.tpl");
			
		return parent::returnHtml($this->objField->name, $html);
	}
	
	function saveData() {
	
		$objDB = tuksiDB::getInstance();
		
		if ($this->objField->fieldvalue3) {	
			$sql = "SELECT ".$this->objField->fieldvalue3." AS id FROM ".$this->objField->tablename." WHERE id = '".$this->objField->rowid."'";
			$rs = $objDB->fetchItem($sql);
			if ($rs['ok'] && $rs['num_rows'] == 1) {
				$sqlWhere = "WHERE t1.".$this->objField->fieldvalue3." = ".$rs['data']['id'];	
			}

			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere.= "AND ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		} else {
			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere = "WHERE ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		}
		if ($this->arrFieldvalues['FIELDVALUE8']) {
			$arrParam = explode('=', $this->arrFieldvalues['FIELDVALUE8']);

			$sqlONWhere .= "AND {$arrParam[0]} = '{$arrParam[1]}'";
		}

		// Special field as name
		$name = "name";
		if ($this->objField->fieldvalue4) {
			$name = $this->objField->fieldvalue4;
		}

		// Special field as relation
		$relname = $this->objField->fieldvalue1."id";
		if ($this->objField->fieldvalue5) {
			$relname = $this->objField->fieldvalue5;
		}

		// Special order by
		$orderBy = "t1.name, t1.id";
		if ($this->arrFieldvalues['FIELDVALUE7']) {
			$orderBy = $this->arrFieldvalues['FIELDVALUE7'];
		}
		
		$sql = "SELECT t1.id, t1.$name AS name, t2.id AS checked " ;
		$sql.= "FROM " . $this->objField->fieldvalue1 . " t1 " ;
		$sql.= "LEFT JOIN " . $this->objField->fieldvalue2 . " t2 ON (t1.id = t2." . $relname." AND t2." . $this->objField->tablename. "id = '{$this->objField->rowid}' $sqlONWhere) ";
		$sql.= $sqlWhere;
		$sql.= " GROUP BY t1.id ORDER BY $orderBy";
		$rs = $objDB->fetch($sql);

		$sqlDel = "DELETE FROM " . $this->objField->fieldvalue2 . " ";
		$sqlDel.= "WHERE " . $this->objField->tablename . "id = '". $this->objField->rowid ."'";

		if (count($arrParam) == 2) {
			$sqlDel .= " AND {$arrParam[0]} = '{$arrParam[1]}'";
		}

		$objDB->write($sqlDel) or print mysql_error();
		
		if ($rs['ok']) {
			foreach ($rs['data'] as $arrData) {
				if ($_POST->getStr($this->objField->htmltagname . "_" . $arrData['id'])) {
					$sqlIns = "INSERT INTO " . $this->objField->fieldvalue2 . " ";
					$sqlIns.= " SET " . $this->objField->tablename . "id = '" . $this->objField->rowid . "',";
					$sqlIns.= $relname . " = '".$arrData['id']."' ";
					if (count($arrParam) == 2) {
						$sqlIns.= ", {$arrParam[0]} = '{$arrParam[1]}'"; 
					}
					$objDB->write($sqlIns) or print mysql_error();
				}	
			}
		}
	}
	
	function getListHtml() {
		
		$objDB = tuksiDB::getInstance();
		
		if ($this->objField->fieldvalue3) {	
			$sql = "SELECT ".$this->objField->fieldvalue3." AS id FROM ".$this->objField->tablename." WHERE id = '".$this->objField->rowid."'";
			$rs = $objDB->fetchItem($sql);
			if ($rs['ok'] && $rs['num_rows'] == 1) {
				$sqlWhere = "WHERE t1.".$this->objField->fieldvalue3." = ".$rs['data']['id'];	
			}

			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere.= "AND ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		} else {
			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere = "WHERE ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		}

		// Special field as name
		$name = "name";
		if ($this->objField->fieldvalue4) {
			$name = $this->objField->fieldvalue4;
		}

		// Special field as relation
		$relname = $this->objField->fieldvalue1."id";
		if ($this->objField->fieldvalue5) {
			$relname = $this->objField->fieldvalue5;
		}

		// Special order by
		$orderBy = "t1.name, t1.id";
		if ($this->arrFieldvalues['FIELDVALUE7']) {
			$orderBy = $this->arrFieldvalues['FIELDVALUE7'];
		}
		
		$sql = "SELECT t1.id, t1.$name AS name, t2.id AS checked " ;
		$sql.= "FROM " . $this->objField->fieldvalue1 . " t1 " ;
		$sql.= "LEFT JOIN " . $this->objField->fieldvalue2 . " t2 ON (t1.id = t2." . $relname." AND t2." . $this->objField->tablename. "id = '{$this->objField->rowid}') ";
		$sql.= $sqlWhere;
		$sql.= " GROUP BY t1.id ORDER BY $orderBy";
		$rs = $objDB->fetch($sql);
		
		$arrList = array();
		if ($rs['ok']) {
			foreach ($rs['data'] as $arrData) {
				if ($arrData['checked']) {
					$arrList[] = $arrData['name'];
				}	
			}
		}

		if (count($arrList)) {
			return join(', ', $arrList);
		} else {
			return "";
		}
	}
	
	function copyData($rowid_to) {
		
		$objDB = tuksiDB::getInstance();
		
		if ($this->objField->fieldvalue3) {	
			$sql = "SELECT ".$this->objField->fieldvalue3." AS id FROM ".$this->objField->tablename." WHERE id = '".$this->objField->rowid."'";
			$rs = $objDB->fetchItem($sql);
			if ($rs['ok'] && $rs['num_rows'] == 1) {
				$sqlWhere = "WHERE t1.".$this->objField->fieldvalue3." = ".$rs['data']['id'];	
			}

			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere.= "AND ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		} else {
			// AND
			if ($this->arrFieldvalues['FIELDVALUE6']) {
				$sqlWhere = "WHERE ".$this->arrFieldvalues['FIELDVALUE6']." ";
			}
		}

		// Special field as name
		$name = "name";
		if ($this->objField->fieldvalue4) {
			$name = $this->objField->fieldvalue4;
		}

		// Special field as relation
		$relname = $this->objField->fieldvalue1."id";
		if ($this->objField->fieldvalue5) {
			$relname = $this->objField->fieldvalue5;
		}

		// Special order by
		$orderBy = "t1.name, t1.id";
		if ($this->arrFieldvalues['FIELDVALUE7']) {
			$orderBy = $this->arrFieldvalues['FIELDVALUE7'];
		}
		
		$sql = "SELECT t1.id, t1.$name AS name, t2.id AS checked " ;
		$sql.= "FROM " . $this->objField->fieldvalue1 . " t1 " ;
		$sql.= "LEFT JOIN " . $this->objField->fieldvalue2 . " t2 ON (t1.id = t2." . $relname." AND t2." . $this->objField->tablename. "id = '{$this->objField->rowid}') ";
		$sql.= $sqlWhere;
		$sql.= " GROUP BY t1.id ORDER BY $orderBy";
		$rs = $objDB->fetch($sql);
		
		if ($rs['ok']) {
			foreach ($rs['data'] as $arrData) {
				if ($arrData['checked']) {
					$sqlIns = "INSERT INTO " . $this->objField->fieldvalue2 . "(" . $this->objField->tablename . "id, " . $relname . ") ";
					$sqlIns.= "VALUES('" . $rowid_to . "','".$arrData['id']."')";
					$objDB->write($sqlIns) or print mysql_error();
				}	
			}
		}
	}
	
	function releaseData() {
		
		$objPage = tuksiBackend::getInstance();
		
		$maintable = $this->objField->tablename;
		$relationtable = $this->objField->fieldvalue2;
		
		$sqlAppend = sprintf("WHERE %sid = '%d'", $maintable, $this->objField->rowid);

		if (!tuksiRelease::releaseTable($relationtable, null, $sqlAppend)) {
			$objPage->alert($objPage->cmstext("pagereleasefailed"));
		}	
	}
}	
?>
