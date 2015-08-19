<?

/**
 * Enter description here...
 *
 * @uses tuksiDB
 * @package tuksiBackend
 */

class tuksiFielditemPageGenerator extends tuksiFielditemBase {
	
	function __construct($pg_moduleid) {
		parent::__construct('pg');
		
		$this->pg_moduleid	= $pg_moduleid;
		
	}
	
	function updateItem($cmsfielditemid, $colname, $name, $cmsrowfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, $cmsrowtypegroupid, $helptext) {
		
		return parent::updateItem($cmsfielditemid, 'pg', 'pg_content', $colname, $this->pg_moduleid, $name, $cmsrowfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, 0, $cmsrowtypegroupid, '', '', $helptext);
	
	}

	/**
	 * Kopier fielditems til ny relationid
	 *
	 * @param int $pg_moduleid_to
	 */
	function copyItem($pg_moduleid_to) {
	//	$this->objPage->alert('Copy fields from id = ' . $this->pg_moduleid . ' to ' . $pg_moduleid_to);
		
		$arrFields = $this->getFields();
		
		foreach ($arrFields as $arrField) {
			$cmsfielditemid = parent::copyItem($arrField['id']);
			parent::updateItemFields($cmsfielditemid, array('relationid' => $pg_moduleid_to));
		}
	}
	
	/**
	 * Getting fields PG module
	 *
	 * @return unknown
	 */
	function getFields() {

		$objDB = tuksiDB::getInstance();
		
		$sqlField = "SELECT * FROM cmsfielditem ";
		$sqlField.= "WHERE itemtype = 'pg' AND relationid = '{$this->pg_moduleid}' ";
		$sqlField.= "ORDER BY seq";
			
		$arrField = $objDB->fetch($sqlField);
		
		if ($arrField['num_rows']) {
			return $arrField['data'];
		}
		
		return array();
	}
	
	/**
	 * Delete items, by setting prepareItem (delete_me =1) and doing cleanupItem (deletes all with delele_me = 1).
	 *
	 */
	function deleteItems() {
		$this->prepareItem();
		$this->cleanupItem();
	}
	
	/**
	 * Setting delete_me = 1, soo we can see that items is not updated.
	 *
	 */
	function prepareItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmsfielditem SET delete_me = 1 WHERE itemtype = 'pg' AND relationid	= '{$this->pg_moduleid}'";
		$rs = $objDB->write($sql) or $this->objPage->alert("Fejl i prepareItem: " . $sql . ', ' . mysql_error()); 
	}
	
	/**
	 * Deletes all items that have delete_me = 1
	 *
	 */
	function cleanupItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT id FROM cmsfielditem WHERE itemtype = 'pg' AND delete_me = 1 AND relationid = '{$this->pg_moduleid}'";
		$rs = $objDB->fetch($sql);
		
		foreach ($rs['data'] as $arrItem) {
			//$this->objPage->alert('Deleting ID = ' . $arrItem['id']);
			$this->deleteItem($arrItem['id']);
		}
	}
	
	function getHTML($arrFields) {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$fields = mysql_list_fields($objDB->arrSetup['dbname'], 'pg_content');// or $this->objPage->alert("Error getting fields from pg_content: " . mysql_error());
		$columns = mysql_num_fields($fields);

		$arrColnames = array();
		$arrFieldUsed = array();
		
		$arrDontShow = array('id', 'pg_contentareaid', 'pg_moduleid', 'cmstreeid', 'not_delete', 
							'cache', 'seq','placement','cmstreetabid');
							
		foreach ($arrFields as $arrField) {
			//$arrDontShow[] = $arrField['colname'];
			$arrFieldUsed[] = $arrField['colname'];
		}
		
		// isactive must be added. So add it if it is not in array.
		if (!in_array('isactive', $arrFieldUsed)) {
			$cmsfielditemid = $this->updateItem(0, 'isactive', 'Synlig', 1, '', '', '', '', '', 1, '');
			$this->addStandardPerms($cmsfielditemid);
			
			$this->findValues($cmsfielditemid);
			
			$arrFields = $this->getFields();
		}
		
		// $this->findValues(13424);
		//$this->objPage->alert($arrFieldUsed);
		
		$arrColnames = array();
		//$arrColnames[0] = $this->objPage->cmsText('choose_field');
		
		for ($i = 0; $i < $columns; $i++) {
			$colname = mysql_field_name($fields, $i);
			
			if (!in_array($colname, $arrDontShow)) {
				//$arrColnames[$colname] = $colname;				
				if (!in_array($colname, $arrFieldUsed))
					$arrColnames[$colname] = array('name' => $colname, 'value' => $colname);
				else
					$arrColnames[$colname] = array('name' => $colname, 'value' => $colname, 'used' => 1);
			} 
			

		} // END foreach field
		
		//sort($arrColnames);
	//	$this->objPage->alert($arrColnames);
		
		$arrTplData['fields'] = $arrColnames;
		$arrTplData['btnadd'] = $objPage->cmsText('btnadd');
		$arrTplData['nyt_element'] = $objPage->cmsText('nyt_element');
		
		
		return parent::getHTML($arrFields, $arrTplData);
	}
	
	function saveData($arrFields) {
		
		$this->prepareItem();

		$arrFieldUsed = array();
				
		foreach ($arrFields as $key => $arrField) {
			$colname = $arrField['colname'];
			$arrFieldUsed[] = $colname;

			// Sletning af element vha. knap
			if ($_POST->getInt('fielditemid_delete_' . $arrField['id'])) {
				$this->deleteItem($arrField['id']);
				unset($arrFields[$key]);
			} else { 
				$this->updateItem($arrField['id'], $colname,
										$_POST->getStr('name_' . $colname), 
										$_POST->getStr('cmsfieldtypeid_' . $colname), 
										$_POST->getStr("fieldvalue1_" . $colname), 
										$_POST->getStr("fieldvalue2_" . $colname), 
										$_POST->getStr("fieldvalue3_" . $colname), 
										$_POST->getStr("fieldvalue4_" . $colname), 
										$_POST->getStr("fieldvalue5_" . $colname), 
										$_POST->getStr("cmsfieldgroupid_" . $colname), 
										$_POST->getStr("helptext_" . $colname));
			}
		
		}
		parent::saveData($arrFields);
		
		$arrFieldAdd = $_POST->getArray('field_add');
		
		if (count($arrFieldAdd) > 0) {
			
			foreach ($arrFieldAdd as $colname) {
					
				// Et colname må kun være der en gang
				if (!in_array($colname, $arrFieldUsed)) {
					$cmsfielditemid = $this->updateItem(0, $colname, $colname, 0, '', '', '', '', '', 1, '');
					$this->addStandardPerms($cmsfielditemid);
					$this->findValues($colname, $cmsfielditemid);
				}
			}
		}		
		$this->cleanupItem();
	}
}

?>
