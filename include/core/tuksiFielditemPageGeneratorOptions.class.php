<?


/**
 * Enter description here...
 *
 * @uses tuksiDB
 * @package tuksiBackend
 */

class tuksiFielditemPageGeneratorOptions extends tuksiFielditemBase {
	
	function __construct($pg_moduleid) {
		parent::__construct('option');
		
		$this->pg_moduleid	= $pg_moduleid;
		
	}
	
	function updateItem($cmsfielditemid, $colname, $name, $cmsrowfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, $cmsrowtypegroupid, $helptext, $showin_edit, $showin_settings) {
		$id = parent::updateItem($cmsfielditemid, 'option', 'cmstree', $colname, $this->pg_moduleid, $name, $cmsrowfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, 0, $cmsrowtypegroupid, '', '', $helptext);		
		$this->updateOptions( $id, $showin_edit, $showin_settings );
		return $id; 
	}

	function updateOptions( $cmsfielditemid, $showedit, $showsettings ) {
		$objDB = tuksiDB::getInstance();
		
		$sqlOption = "SELECT * FROM pg_option WHERE cmsfielditemid='$cmsfielditemid'";
		$arrOption = $objDB->fetch( $sqlOption );
		
		if( $arrOption['ok'] && $arrOption['num_rows'] > 0 ) {
			$sqlUpdOption = "UPDATE pg_option SET showin_edit='$showedit', showin_settings='$showsettings' WHERE cmsfielditemid='$cmsfielditemid'";
		} else {
			$sqlUpdOption = "INSERT INTO pg_option (showin_edit, showin_settings, cmsfielditemid) VALUES ('$showedit', '$showsettings', '$cmsfielditemid')";
		}
		$rs = $objDB->write($sqlUpdOption) or $this->objPage->alert("Fejl i updateItem: " . $sqlUpdOption . ', ' . mysql_error()); 
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
	
	function getFields() {

		$objDB = tuksiDB::getInstance();
		
		unset($arrField);
		
		$sqlField = "SELECT fi.*, op.showin_edit, op.showin_settings, op.cmsfielditemid FROM cmsfielditem AS fi, pg_option AS op ";
		$sqlField.= "WHERE fi.itemtype = 'option' AND fi.relationid = '{$this->pg_moduleid}' AND fi.id=op.cmsfielditemid ";
		$sqlField.= "ORDER BY fi.seq";
					
		$rsField = $objDB->fetch($sqlField);
		
		$arrFields = array();
		if ($rsField['num_rows']) {

			foreach ($rsField['data'] as $arrField) {
				$arrFields[] = $arrField;

			}
		}
	//	$this->objPage->alert($arrFields);
		
		return $arrFields;
	}
	
	function deleteItems() {
		$this->prepareItem();
		$this->cleanupItem();
	}
	
	function prepareItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmsfielditem SET delete_me = 1 WHERE itemtype = 'option' AND relationid	= '{$this->pg_moduleid}'";
		$rs = $objDB->write($sql) or $this->objPage->alert("Fejl i prepareItem: " . $sql . ', ' . mysql_error()); 
	}
	
	function cleanupItem() {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT id FROM cmsfielditem WHERE itemtype = 'option' AND delete_me = 1 AND relationid = '{$this->pg_moduleid}'";
		$rs = $objDB->fetch($sql);
		
		foreach ($rs['data'] as $arrItem) {
			//$this->objPage->alert('Deleting ID = ' . $arrItem['id']);
			$this->deleteItem($arrItem['id']);
			$this->cleanupOptions( $arrItem['id'] );
		}
			
		$rs = $objDB->fetch($sql);
	}
	
	function cleanupOptions( $cmsfielditemid ) {
		$objDB = tuksiDB::getInstance();
		
		$sql = "DELETE FROM pg_option WHERE cmsfielditemid='$cmsfielditemid'";
		$rs = $objDB->write( $sql );
	}
	
	function getHTML($arrFields) {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$fields = mysql_list_fields($objDB->arrSetup['dbname'], 'cmstree');// or $this->objPage->alert("Error getting fields from pg_content: " . mysql_error());
		$columns = mysql_num_fields($fields);

		$arrColnames = array();
		$arrFieldUsed = array();
		
		$arrDontShow = array('id', 'parentid', 'cmstreetypeid', 'value1', 'value2', 'value3', 'cmsfileid', 'backendscriptparam', 'name', 'tabwidth', 'pg_page_templateid', 'pg_browser_title', 'pg_show_settings', 'pg_isactive', 'pg_menu_name', 'pg_show_inmenu', 'pg_urlpart', 'pg_urlpart_full', 'pg_isfrontpage', 'datecreated', 'datechanged', 'datepublished', 'pg_metakeywords', 'pg_metadescription', 'cmscontextid', 'cms_page_templateid', 'isdeleted', 'datedeleted');
		foreach ($arrFields as $arrField) {
			//$arrDontShow[] = $arrField['colname'];
			$arrFieldUsed[] = $arrField['colname'];
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
				$this->cleanupOptions( $arrItem['id'] );
				unset($arrFields[$key]);
			} else {
				if ($_POST->getStr("showin_edit_" . $colname))
					$showin_edit = 1;
				else
					$showin_edit = 0;
				
				if ($_POST->getStr("showin_settings_" . $colname))
					$showin_settings = 1;
				else
					$showin_settings = 0;
				
				$this->updateItem($arrField['id'], $colname,
									$_POST->getStr('name_' . $colname), 
									$_POST->getStr('cmsfieldtypeid_' . $colname), 
									$_POST->getStr("fieldvalue1_" . $colname), 
									$_POST->getStr("fieldvalue2_" . $colname), 
									$_POST->getStr("fieldvalue3_" . $colname), 
									$_POST->getStr("fieldvalue4_" . $colname), 
									$_POST->getStr("fieldvalue5_" . $colname), 
									$_POST->getStr("cmsfieldgroupid_" . $colname), 
									$_POST->getStr("helptext_" . $colname),
									$showin_edit,
									$showin_settings
									);
			}
		
		}
		parent::saveData($arrFields);
		
		$arrFieldAdd = $_POST->getArray('field_add');
		
		if (count($arrFieldAdd) > 0) {
			
			foreach ($arrFieldAdd as $colname) {
					
				// Et colname må kun være der en gang
				if (!in_array($colname, $arrFieldUsed)) {
					$cmsfielditemid = $this->updateItem(0, $colname, $colname, 0, '', '', '', '', '', 1, '', 0, 0);
					$this->addStandardPerms($cmsfielditemid);
					$this->findValues($colname, $cmsfielditemid);
				}
			}
		}		
		$this->cleanupItem();
	}
}

?>
