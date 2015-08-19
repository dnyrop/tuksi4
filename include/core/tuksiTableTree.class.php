<?php

class tuksiTableTree extends tuksiTable {
	
	function __construct() {
		parent::__construct('cmstree');
			
	}
	
	function updateItem($cmstreeid, $parentid, $name, $seq, $cmstreetypeid, $value1, $value2, $value3, $cmsfileid, $tabwidth) {
		
		$objDB = tuksiDB::getInstance();
		
		$arrFields[] = array('parentid', $parentid);
		$arrFields[] = array('name', $name);
		$arrFields[] = array('seq', $seq);
		$arrFields[] = array('cmstreetypeid', $cmstreetypeid);
		$arrFields[] = array('value1', $value1);
		$arrFields[] = array('value2', $value2);
		$arrFields[] = array('value3', $value3);
		$arrFields[] = array('cmsfileid', $cmsfileid);
		$arrFields[] = array('tabwidth', $tabwidth);
	
		
		$arrField = array();
		$arrData = array();
		
		foreach ($arrFields as &$arrItem) {
			$arrField[] = $arrItem[0];
			$arrData[] = "'" . $objDB->realEscape(stripslashes($arrItem[1])) . "'";
			$arrSet[] = $arrItem[0] . " = '" . $objDB->realEscape(stripslashes($arrItem[1])) . "'";
		}
		
		// hvis ID findes opdateres ellers indsættes nu række.
		if ($cmstreeid) {			
			$sql = "UPDATE cmstree ";
			$sql.= " SET " . join(", ", $arrSet);
			$sql.= " WHERE id = '$cmstreeid'";
			
			$rs = $objDB->write($sql) or print mysql_error() . " SQL: $strSQL <br>";
			
			$this->cmstreeid = $cmstreeid;
		} else {
			$sql = "INSERT INTO cmstree ";
			$sql.= '(' . join(', ', $arrField) . ')';
			$sql.= ' VALUES(' . join(', ', $arrData) . ')';
		
			$rs = $objDB->write($sql) or print mysql_error() . " SQL: $strSQL <br>";
			
			$this->cmstreeid = $rs['insert_id'];
		}
		
		return $this->cmstreeid;
		
	}
}
?>