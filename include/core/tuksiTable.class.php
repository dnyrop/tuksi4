<?
class tuksiTable {

	public $tablename;
	
	function __construct($tablename) {
		$this->tablename = $tablename;
	}
	
	function copyRow($rowid_from, $tablelayoutid) {
		
		$objDB = tuksiDB::getInstance();
		
		$arrReturn = array();
		
		$sqlFields = "SHOW COLUMNS FROM {$this->tablename}";
		$rs = $objDB->fetch($sqlFields,array('type' => 'object'));
		
		// Sætter array med feltnavne. Id skal IKKE med
		$arrFields = array();
		
		foreach ($rs['data'] as $objFields) {
			if ($objFields->Field != 'id') {
				$arrFields[] = $objFields->Field;
			}
		}
		
		$sqlInsert = "INSERT INTO {$this->tablename} (" . join(", ", $arrFields) . ") ";
		$sqlInsert.= "SELECT " . join(", ", $arrFields) . " FROM {$this->tablename} WHERE id = '$rowid_from'";
		
		$rs = $objDB->write($sqlInsert);
		
		$arrReturn['rowid_new'] = $rs['insert_id'];
		$this->updateFields($arrReturn['rowid_new'], array('name' => 'Ny'));
		
		$this->copyViaFieldType($rowid_from, $arrReturn['rowid_new'], $tablelayoutid);
		
		return $arrReturn;
	}
	
	function copyViaFieldType($rowid_from, $rowid_to, $tablelayoutid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlData = "SELECT * FROM {$this->tablename} WHERE id = '{$rowid_from}'";
		$arrRsData = $objDB->fetchItem($sqlData);
		$arrData = $arrRsData['data'];

		$sqlCols = "SELECT fi.*, ft.classname ";
		$sqlCols.= "FROM cmsfielditem fi, cmsfieldtype ft ";
		$sqlCols.= "WHERE ft.id = fi.cmsfieldtypeid AND fi.tablename = '{$this->tablename}' AND fi.relationid = '{$tablelayoutid}' AND ft.special_copy = 1";
		
		$arrRs  = $objDB->fetch($sqlCols,array('type' => 'object'));
		
		foreach ($arrRs['data'] as $objRow) {
			
			$objRow->rowid 	= $rowid_from;
			$objRow->value	= $arrData[$objRow->colname];
						
			$objField = new $objRow->classname($objRow);
			
			$objField->copyData($rowid_to);
			
		}
	}
	
	function deleteRow($rowid, $tablelayoutid) {
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();

		// Sletter feltdata først
		$this->deleteViaFieldType($rowid, $tablelayoutid);

		$sql = "DELETE FROM {$this->tablename} WHERE id = '{$rowid}'";
		$result = $objDB->write($sql);
		
		if($result['ok']) {
		
			$cmd = "rm -f " . $arrConf['path']['supload']  . "/" .  $this->tablename . "/" . $rowid . "_*";
			
			system($cmd);

			tuksiDebug::log("Delete files:" . $cmd);
			
			return true;
		} else {
			return false;
		}
	}
	
	function deleteViaFieldType($rowid, $tablelayoutid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlData = "SELECT * FROM {$this->tablename} WHERE id = '{$rowid}'";

		$arrData = $objDB->fetchItem($sqlData);
		$arrData = $arrData['data']; 

		$sqlCols = "SELECT fi.*, ft.classname ";
		$sqlCols.= "FROM cmsfielditem fi, cmsfieldtype ft ";
		$sqlCols.= "WHERE ft.id = fi.cmsfieldtypeid AND fi.tablename = '{$this->tablename}' AND fi.relationid = '{$tablelayoutid}' AND ft.special_delete = 1 ";
		
		$arrReturn  = $objDB->fetch($sqlCols, array('type' => 'object'));
		
		foreach ($arrReturn['data'] as $objFieldItem) {
			
			$objFieldItem->rowid = $rowid;
			$objFieldItem->value = $arrData[$objFieldItem->colname];
			
			$objField = new $objFieldItem->classname($objFieldItem);
			
			$objField->deleteData();
			
		}
	}
	
	/**
	 * Opdatere x antal felter i en tablen
	 *
	 * @param int $cmsfielditemid
	 * @param array $arrFields Key = felt, value = værdi
	 */
	function updateFields($rowid, $arrFields) {
		
		$objDB = tuksiDB::getInstance();
		
		foreach ($arrFields as $key => $value) {
			$arrSqlFields[] = $key . " = '" . $value . "'";
		}
		
		$sql = "UPDATE {$this->tablename} SET " . join(", ", $arrSqlFields) . " WHERE id ='$rowid'";
		$objDB->write($sql);
	}
}

?>
