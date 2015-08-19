<?


/**
 * Enter description here...
 *
 * @uses tuksiDB
 * @package tuksiBackend
 */
class tuksiFielditemTable extends tuksiFielditemBase {
	
	function __construct($tablename, $tablelayoutid) {
		parent::__construct('table');
		
		$this->tablename = $tablename;
		$this->tablelayoutid = $tablelayoutid;	
		
	}
	
	/**
	 * Kopier fielditems fra aktiv tablelayout til andet tablelayoutid ($tableid_new)
	 *
	 * @param int $tableid_new
	 */
	function copyItems($tableid_new) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlField = "SELECT id FROM cmsfielditem ";
		$sqlField.= "WHERE itemtype = 'table' AND tablename = '{$this->tablename}' AND relationid = '{$this->tablelayoutid}' ORDER BY seq";
		$rs = $objDB->fetch($sqlField);
		
		foreach ($rs['data'] as $arrField ) {
			
			$cmsfielditemid_to = parent::copyItem($arrField['id']);	
			parent::updateItemFields($arrField['id'], array('relationid' => $tableid_new));
		}
				
	}
	
	function updateItem($cmsfielditemid, $colname, $name, $cmsrowfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, $seq, $cmsrowtypegroupid, $listcolwidth, $listcolalign, $helptext) {
		
		return parent::updateItem($cmsfielditemid, 'table', $this->tablename, $colname, $this->tablelayoutid, $name, $cmsrowfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, $seq, $cmsrowtypegroupid, $listcolwidth, $listcolalign, $helptext);
	
	}
	
	/**
	 * Henter fielditem udfra colname, og opretter fielditem hvis det ikke findes hvis makenew = 1.
	 * Returner assoc array
	 *
	 * @param string $colname
	 * @param int $makenew
	 * @return object
	 */
	function getField($colname, $makenew = 0) {
		unset($MyField);
		
		$objDB = tuksiDB::getInstance();
		
		$sqlField = "SELECT * FROM cmsfielditem ";
		$sqlField.= "WHERE itemtype = 'table' AND tablename = '{$this->tablename}' AND colname = '{$colname}' AND relationid = '{$this->tablelayoutid}'";
		
		//$this->objPage->alert($sqlField);
		
		$rsField = $objDB->fetch($sqlField);
		$arrAutoSet = $this->findValues($colname);
		
		//$this->objPage->alert($colname);	
		//$this->objPage->alert($arrAutoSet);
		
		if ($rsField['num_rows']) {
			
			$MyField = $rsField['data'][0];
			
			$this->setCmsFieldItemID($MyField['id']);
		} elseif ($makenew) {
						
			$arrAutoSet = $this->findValues($colname);
					
			$cmsfielditemid = $this->updateItem(0, $colname, $arrAutoSet['name'],  $arrAutoSet['cmsfieldtypeid'], '', '', '', '', '', $arrAutoSet['seq'], 1,'', '', '' );
			
			$this->addStandardPerms($cmsfielditemid);
			
			return $this->getField($colname);
		}
		
		return $MyField;
	}
	
	/**
	 * Sletter alle items til nuværent tablelayout
	 *
	 */
	function deleteItems() {
		$this->prepareItem();
		$this->cleanupItem();
	}
	
	function prepareItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmsfielditem SET delete_me = 1 WHERE itemtype = 'table' AND relationid = '{$this->tablelayoutid}'";
		$rs = $objDB->write($sql);
	}
	
	function cleanupItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT id FROM cmsfielditem WHERE itemtype = 'table' AND delete_me = 1 AND relationid = '{$this->tablelayoutid}'";		
		$rs = $objDB->fetch($sql);
		
		foreach ($rs['data'] as $arrItem) {
			$this->deleteItem($arrItem['id']);
		}
	
	}
	
	/**
	 * Opdatere tablelayout
	 *
	 * @param string $name
	 * @return resultset mysql resultset
	 */
	function updateTableLayoutName($name) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlSaveLayout = "UPDATE cmstablelayout SET name = '" . $objDB->realEscapeString(stripslashes($name)). "' WHERE id = '{$this->tablelayoutid}'";
		$objDB->write($sqlSaveLayout);
	}
	
	/**
	 * Slettter aktiv tablelayout og fielditems til den
	 *
	 */
	function deleteTableLayout() {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlDeleteLayout = "DELETE FROM cmstablelayout WHERE id = '{$this->tablelayoutid}'";
		$objDB->write($sqlDeleteLayout);
		
		$this->deleteItems();
	}
	
	function getFieldsToShow($cmsuserid, $perm = 'list') {

		$objDB = tuksiDB::getInstance();
		
		// Getting fields with correct permission
		$sqlPerm = "SELECT fi.colname FROM cmsfielditem fi, cmsfieldperm fp, cmsusergroup cg ";
		$sqlPerm.= "WHERE fi.itemtype = 'table' ";
		$sqlPerm.= "AND fi.relationid = '{$this->tablelayoutid}' AND fi.id = fp.cmsfielditemid ";
		$sqlPerm.= "AND cg.cmsgroupid = fp.cmsgroupid AND cg.cmsuserid = '{$cmsuserid}' AND fp.p{$perm} = 1";
		
		$arrRsPerm = $objDB->fetch($sqlPerm);	
		
		//print_r($arrRsPerm);print "<br>";
		$arrPermOk = array();
		
		if ($arrRsPerm['num_rows']) {
			foreach ($arrRsPerm['data'] as $arrField) {
				$arrPermOk[] = $arrField['colname'];
			}
		}

		$arrFields = array();
		$arrSqlFields = array();
		
		// Getting fields in table ( TABLENAME ) 	
		$sqlFields = "SHOW FIELDS FROM ".$this->tablename;
		$rs = $objDB->fetch($sqlFields);// or $this->objPage->addAlert("Fejl ved hentning af felter:" . mysql_error());
		
		foreach ($rs['data'] as $row) {
			
			$fieldname = $row['Field'];
				
			// If permission ok then return field
			if (in_array($fieldname, $arrPermOk)) {
				$arrSqlFields[] = " fi.colname = '{$fieldname}' ";
				$arrFields[] = $fieldname; 
			}
		}	

		return array($arrFields, $arrSqlFields);
	}
	
	/**
	 * Finder fielditems udaf colname i en table. ID oprettes hvis den ikke findes.
	 *
	 * @param array $colnames
	 * @return array
	 */
	function getHTML($colnames) {
		$arrFields = array();
		if (is_array($colnames)) {
			foreach ($colnames as $colname) {
				$arrField = $this->getField($colname, 1);
				
				$arrFields[] = $arrField;						
			}
			
		} else {
			$arrField = $this->getField($colname);	
			$arrFields[] = $arrField;	
		}
		
		usort($arrFields, array($this, 'sort_field'));
		
		return parent::getHTML($arrFields, array('itemtype' => 'table'));
	
	}
	
	function sort_field($a, $b) {
   		if ($a['seq'] == $b['seq']) {
       		return 0;
   		}
   		return ($a['seq'] < $b['seq']) ? -1 : 1;
	}
	
	/**
	 * Gemmer felter
	 *
	 * @param array $colname
	 * @return unknown
	 */
	function saveData($colname) {
		//$this->objPage->alert($colname);
		
		$arrFields = array();
		if (is_array($colname)) {
			foreach ($colname as $col) {
				
				$arrField = $this->getField($col);	
				
				//need to make a fieldtype for name
				$objField = new stdClass;
				$objField->htmltagname = 'name_'.$arrField['colname'];				
				$objField->value = $_POST->getStr('name_' . $arrField['colname']);														
				$objField->readonly = false;														
				$objField->id = $arrField['id'];																
				$objField->row = $arrField['id'];													
				$objFieldSuggest = new fieldTextSuggest($objField);

				$objFieldSuggest->saveData();	
					
				$arrField['colname'] = $col;
				$arrFields[] = $arrField;					
			}
		} else {
			$arrField = $this->getField($colname);
			$arrField['colname'] = $colname;
			$arrFields[] = $arrField;	
		}
		
		usort($arrFields, array($this, 'sort_field'));
		
		foreach ($arrFields as $arrField) {
			$colname = $arrField['colname'];
			$this->updateItem($arrField['id'], $colname, $_POST->getStr('name_' . $colname), $_POST->getStr('cmsfieldtypeid_' . $colname), $_POST->getStr("fieldvalue1_" . $colname), $_POST->getStr("fieldvalue2_" . $colname), $_POST->getStr("fieldvalue3_" . $colname), $_POST->getStr("fieldvalue4_" . $colname), $_POST->getStr("fieldvalue5_" . $colname), 0, $_POST->getStr("cmsfieldgroupid_" . $colname), $_POST->getStr("listcolwidth_".$colname), $_POST->getStr("listcolalign_" . $colname), $_POST->getStr("helptext_" . $colname));
		}
		return parent::saveData($arrFields);
	}
	
	
	public function getCompleteLayout(){
		
		$arrFields = array();
		
		$objDB = tuksiDB::getInstance();
		
		$sqlField = "SELECT * FROM cmsfielditem ";
		$sqlField.= "WHERE itemtype = 'table' AND tablename = '{$this->tablename}' AND relationid = '{$this->tablelayoutid}'";
		$rsField = $objDB->fetch($sqlField);
		
		if($rsField['ok'] && $rsField['num_rows'] > 0) {
			foreach ($rsField['data'] as $arrField) {
				//get perms
				$arrPerms = array();
				
				$sqlPerm = "SELECT * FROM cmsfieldperm WHERE cmsfielditemid = '{$arrField['id']}' ";
				$rsPerm = $objDB->fetch($sqlPerm);
				
				if($rsPerm['ok'] && $rsPerm['num_rows'] > 0){
					$arrPerms = $rsPerm['data'];
				}
				
				$arrFields[] = array(	'colname' => $arrField['colname'],
															'fieldtype' => $arrField,
															'perms' => $arrPerms);
			}
		}
		return $arrFields;
	}
	
	public function getCompleteLayoutXML(){
		
		$xml = "<layout>";
		
		$arrData = $this->getCompleteLayout();
		
		foreach($arrData as $arrField) {
			$xml.= "<field>";
			$xml.= "<colname>".utf8_encode($arrField['colname'])."</colname>";
			$xml.= "<fieldtype>".tuksiTools::makeXMLFromArray($arrField['fieldtype'])."</fieldtype>";
			if(count($arrField['perms'])) {
				$xml.= "<perms>";
				foreach ($arrField['perms'] as $arrPerm) {
					$xml.= "<permgroup>".tuksiTools::makeXMLFromArray($arrPerm)."</permgroup>";	
				}
				$xml.= "</perms>";
			}
			$xml.= "</field>";
		}
		$xml.= "</layout>";
		return $xml;
	}
}
?>
