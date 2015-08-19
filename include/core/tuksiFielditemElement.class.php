<?


/**
 * Enter description here...
 *
 * @uses tuksiDB
 * @package tuksiBackend
 */

class tuksiFielditemElement extends tuksiFielditemBase {
	
	function __construct($tabid) {
		parent::__construct('element');
		
		$this->tabid = $tabid;
	}

	function addItem() {
		parent::updateItem(0, 'element', '', 'new_element', $this->tabid, 'New element', 0, '', '', '', '', '', 0, 1, '', '', 0, '');
	
		return $this->getCmsFieldItemID();
	}
	
	function updateItem($cmsfielditemid, $colname, $name, $cmsfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, $cmsfieldgroupid, $helptext) {
		
		return parent::updateItem($cmsfielditemid, 'element', 'cmsfielditem', $colname, $this->tabid, $name, $cmsfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, 0, $cmsfieldgroupid, '', '', $helptext);
	
	}
	
	function addDataRow($cmsfielditemid){
		
		if(!$this->CheckDataExists($cmsfielditemid)) {
			
			$objDB = tuksiDB::getInstance();
			
			$sqlIns = "INSERT INTO cmsfielddata (cmsfielditemid,rowid,dateadded) VALUES ";
			$sqlIns.= "('{$cmsfielditemid}','{$cmsfielditemid}',now())";
		
			$objDB->write($sqlIns);	
		}
		
	}
	
	function CheckDataExists($cmsfielditemid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlChk = "SELECT * FROM cmsfielddata WHERE cmsfielditemid = '{$cmsfielditemid}' AND rowid = '{$cmsfielditemid}'";
		$rs = $objDB->fetch($sqlChk);
		if($rs['num_rows'] > 0)
			return true;
		else
			return false;
	
	}
	
	
	function checkExists($colname) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT id FROM cmsfielditem ";
		$sql.= "WHERE itemtype = 'element' AND colname = '" . $objDB->realEscapeString($colname) . "' AND relationid = '{$this->tabid}' ";
		
		$rs = $objDB->fetch($sql);
		
		return $rs['num_rows'];
		
	}
	
	/**
	 * Kopier element til anden tabid
	 *
	 * @param int $cmsfielditemid_from
	 */
	function copyItem($cmsfielditemid_from) {
		
		$cmsfielditemid_to = parent::copyItem($cmsfielditemid_from);
		
		parent::updateItemRelid($cmsfielditemid_to, $this->tabid);
	}
	
	/**
	 * Henter alle felter i nuværende tabid
	 *
	 * @return array
	 */
	function getFields() {
		
		$objDB = tuksiDB::getInstance();
			
		$sqlField = "SELECT * FROM cmsfielditem ";
		$sqlField.= "WHERE itemtype = 'element' AND relationid = '{$this->tabid}' ";
		$sqlField.= "ORDER BY seq";
		
		$rsField = $objDB->fetch($sqlField);
		
		$arrFields = array();
		if ($rsField['num_rows']) {
			foreach ($rsField['data'] as $arrField) {
				$arrFields[] = $arrField;
			}
		}
	
		return $arrFields;
	}
	
	
	function prepareItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmsfielditem SET delete_me = 1 WHERE itemtype = 'element' AND relationid = '{$this->tabid}'";
		$objDB->write($sql);
	}
	
	function cleanupItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "DELETE FROM cmsfielditem WHERE itemtype = 'element' AND delete_me = 1 AND relationid = '{$this->tabid}'";
		$objDB->write($sql);
			
	}
	
	/**
	 * Henter felter som en bruger har rettigheder til.
	 *
	 * @param int $cmsuserid
	 * @param string $perm
	 * @return array
	 */
	function getFieldsToShow($cmsuserid, $perm = 'list') {

		$objDB = tuksiDB::getInstance();
		
		// Getting allowed fields to save in table ( TABLENAME ) 	
		$rs = $objDB->fetch("SHOW FIELDS FROM {$this->tablename}");
		
		$arrFields = array();
	
		foreach ($rs['data'] as $row) {
			$fieldname = $row['Field'];
				
			$sqlPerm = "SELECT * FROM cmsfielditem fi, cmsfieldperm fp, cmsusergroup cg ";
			$sqlPerm.= "WHERE fi.type = 'table' ";
			$sqlPerm.= "AND fi.cmstablelayoutid = '{$this->tablelayoutid}' AND fi.id = fp.cmsfielditemid ";
			$sqlPerm.= "AND cg.cmsgroupid = fp.cmsgroupid AND cg.cmsuserid = '{$cmsuserid}' AND fi.colname = '{$fieldname}' AND fp.p{$perm} = 1";

			$rsPerm = $objDB->fetch($sqlPerm);
			if ($rsPerm['num_rows']) {
				$arrSqlFields[] = " fi.colname = '{$fieldname}' ";
				$arrFields[] = $fieldname; 
			}
		
		}	

		return array($arrFields, $arrSqlFields);
	}
	
	function saveData($arrFields,$addDataRow = false) {
		
		$arrReturn = array();
		
		$this->prepareItem();

		if ($_POST->getStr('field_add_name')) {
			
			$colname = stripslashes($_POST->getStr('field_add_name'));
		
			if (!$this->checkExists($colname)) {
				
				$cmsfielditemid = $this->updateItem(0, $colname, $colname, 0, '', '', '', '', '', 1, '', '');
				$this->addStandardPerms($cmsfielditemid);
				
				// Tjek felt, og opdater ID
				$this->findValues($colname, $cmsfielditemid);
				
				if($addDataRow)
					$this->addDataRow($cmsfielditemid);
				
			} else {
				
				$arrReturn['add']['error'] = 'Element navn findes i forvejen';
			
			}
		}

		foreach ($arrFields as $key => $arrField) {
			$colname = $arrField['colname'];

			// Sletning af element vha. knap
			if ($_POST->getStr('fielditemid_delete_' . $arrField['id'])) {
				$this->deleteItem($arrField['id']);
				unset($arrFields[$key]);
			} else { 
				$this->updateItem(	$arrField['id'], 
									$colname, 
									$_POST->getStr('name_' . $colname), 
									$_POST->getStr('cmsfieldtypeid_' . $colname), 
									$_POST->getStr("fieldvalue1_" . $colname), 
									$_POST->getStr("fieldvalue2_" . $colname), 
									$_POST->getStr("fieldvalue3_" . $colname), 
									$_POST->getStr("fieldvalue4_" . $colname), 
									$_POST->getStr("fieldvalue5_" . $colname), 
									$_POST->getStr("cmsfieldgroupid_" . $colname), 
									$_POST->getStr("helptext_" . $colname));
				
				//make sure we have data fields
				if($addDataRow)
					$this->addDataRow($arrField['id']);
			}
		
		}
		
		parent::saveData($arrFields);
		$this->cleanupItem();
		return $arrReturn;
	}
}
?>