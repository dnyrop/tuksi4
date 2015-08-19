<?

/**
 * Enter description here...
 *
 * @uses tuksiDB
 * @package tuksiBackend
 */
class tuksiFielditemBase {
	
	private $arrUserGroups = array();
	
	static $arrFieldTypes;
	static $arrFieldTypeOptions;
	static $arrFieldGroups;
	static $arrFieldAutoset;
	
	function __construct($itemtype) {
	
		$this->itemtype = $itemtype;
	}
	
	/**
	 * Henter fielditem udfra ID
	 *
	 * @param int $cmsfielditemid
	 * @return array
	 */
	function getFieldByID($cmsfielditemid) {
		$objDB = tuksiDB::getInstance();
		
		$sqlField = "SELECT * FROM cmsfielditem ";
		$sqlField.= "WHERE id = '$cmsfielditemid'";
		
		$rsField = $objDB->fetch($sqlField);
		
		if ($rsField['ok'] && $rsField['num_rows']) {
			$MyField = $rsField['data'][0];

			if ($this)
				$this->setCmsFieldItemID($MyField['id']);
			$arrExtra = tuksiFielditemBase::getExtraFieldValues($cmsfielditemid, $MyField['cmsfieldtypeid']);
			if ($arrExtra) {
				foreach ($arrExtra as $extra) {
					$MyField[$extra["name"]] = $extra["value"];
				}
			}
		}
		
		return $MyField;
	}
	
	/**
	 * Opdatere relationid feltet i et fielditem udfra ID
	 *
	 * @param int $cmsfielditemid
	 * @param int $seq
	 */
	function updateItemRelid($cmsfielditemid, $relationid) {	
		$this->updateItemFields($cmsfielditemid, array('relationid' => $relationid));	
	}
	
	/**
	 * Opdatere relationid feltet i et fielditem udfra ID
	 *
	 * @param int $cmsfielditemid
	 * @param int $seq
	 */
	function updateItemSeq($cmsfielditemid, $seq) {
		
		$this->updateItemFields($cmsfielditemid, array('seq' => $seq));
	}
	
	/**
	 * Opdatere x antal felter i cmsfieldtype
	 *
	 * @param int $cmsfielditemid
	 * @param array $arrFields Key = felt, value = værdi
	 */
	function updateItemFields($cmsfielditemid, $arrFields) {
		
		$objDB = tuksiDB::getInstance();
		
		foreach ($arrFields as $key => $value) {
			$arrSqlFields[] = $key . " = '" .$value . "'";
		}
		
		$strSQL = "UPDATE cmsfielditem SET " . join(", ", $arrSqlFields) . " WHERE id ='$cmsfielditemid'";
		$objDB->write($strSQL);
	}
	
	/**
	 * Opdatere en fielditem række
	 *
	 * @param int $cmsfielditemid
	 * @param int $itemtype
	 * @param string $tablename
	 * @param string $colname
	 * @param int $relationid
	 * @param string $name
	 * @param int $cmsfieldtypeid
	 * @param string $fieldvalue1
	 * @param string $fieldvalue2
	 * @param string $fieldvalue3
	 * @param string $fieldvalue4
	 * @param string $fieldvalue5
	 * @param int $seq Opdateres kun hvis den er sat
	 * @param int $cmsfieldgroupid
	 * @param string $listcolwidth
	 * @param string $listcolalign
	 * @param string $helptext
	 */
	function updateItem($cmsfielditemid, $itemtype, $tablename, $colname, $relationid, $name, $cmsfieldtypeid, $fieldvalue1, $fieldvalue2, $fieldvalue3, $fieldvalue4, $fieldvalue5, $seq, $cmsfieldgroupid, $listcolwidth, $listcolalign, $helptext) {
		
		$objDB = tuksiDB::getInstance();
		
		$arrFields[] = array('itemtype', $itemtype);
		$arrFields[] = array('tablename', $tablename);
		$arrFields[] = array('colname', $colname);
		$arrFields[] = array('relationid', $relationid);
		$arrFields[] = array('name', $name);
		$arrFields[] = array('cmsfieldtypeid', $cmsfieldtypeid);
		$arrFields[] = array('fieldvalue1', $fieldvalue1);
		$arrFields[] = array('fieldvalue2', $fieldvalue2);
		$arrFields[] = array('fieldvalue3', $fieldvalue3);
		$arrFields[] = array('fieldvalue4', $fieldvalue4);
		$arrFields[] = array('fieldvalue5', $fieldvalue5);
		
		if ($seq) 
			$arrFields[] = array('seq', $seq);
			
		$arrFields[] = array('cmsfieldgroupid', $cmsfieldgroupid);
		$arrFields[] = array('listcolwidth', $listcolwidth);
		$arrFields[] = array('listcolalign', $listcolalign);
		$arrFields[] = array('helptext', $helptext);
		$arrFields[] = array('delete_me', 0);
		
		$arrField = array();
		$arrData = array();
		
		foreach ($arrFields as &$arrItem) {
			$arrField[] = $arrItem[0];
			$arrData[] = "'" . $objDB->realEscapeString(stripslashes($arrItem[1])) . "'";
			$arrSet[] = $arrItem[0] . " = '" . $objDB->realEscapeString(stripslashes($arrItem[1])) . "'";
		}
		
		// hvis ID findes opdateres ellers indsættes nu række.
		if ($cmsfielditemid) {
			
			$sql = "UPDATE cmsfielditem ";
			$sql.= " SET " . join(", ", $arrSet);
			$sql.= " WHERE id = '$cmsfielditemid'";
			
			$objDB->write($sql);
			
			$this->cmsfielditemid = $cmsfielditemid;
		} else {
			$sql = "INSERT INTO cmsfielditem ";
			$sql.= '(' . join(', ', $arrField) . ')';
			$sql.= ' VALUES(' . join(', ', $arrData) . ')';
		
			$objDB->write($sql);
			
			$this->cmsfielditemid = mysql_insert_id();
		}
		
		$objField = new stdClass;
		$objField->htmltagname = 'name_' . $colname;				
		$objField->value = $name;														
		$objField->readonly = false;														
		$objField->id = $this->cmsfielditemid;																
		$objField->row = $this->cmsfielditemid;													
		$objFieldSuggest = new fieldTextSuggest($objField);
		$objFieldSuggest->saveData();
		
		return $this->cmsfielditemid;
		
	}
	/**
	 * Hent ID ved Insert
	 *
	 * @return unknown
	 */
	function getCmsFieldItemID() {
		return $this->cmsfielditemid;
	}
	
	/**
	 * Sætter fielditem ID i objektet
	 *
	 * @param unknown_type $cmsfielditemid
	 */
	function setCmsFieldItemID($cmsfielditemid) {
		$this->cmsfielditemid = $cmsfielditemid;
	}
	
	/**
	 * Slette fielditem, samt rettigheder og ekstraværdier.
	 *
	 * @param int $cmsfielditemid
	 */
	function deleteItem($cmsfielditemid) {
		
		$objDB = tuksiDB::getInstance();
		
		// Sletter Item
		$sql = "DELETE FROM cmsfielditem WHERE id = '$cmsfielditemid'";
		$objDB->write($sql);
					
		$this->deleteItemData($cmsfielditemid);
		
		$this->preparePerm($cmsfielditemid);
		$this->cleanupPerm();
			
		$this->prepareExtra($cmsfielditemid);
		$this->cleanupExtra();
		
		$this->cleanUpSpecialSetup($cmsfielditemid);
	}
	
	function deleteItemData($cmsfielditemid) {
		// Sletter Item
		$objDB = tuksiDB::getInstance();
		
		$sql = "DELETE FROM cmsfielddata WHERE cmsfielditemid = '$cmsfielditemid'";
		$objDB->write($sql);
		
	}
	
	function cleanUpSpecialSetup($id) {
		//cleanup
		$objDB = tuksiDB::getInstance();
		
		$sqlDel = "DELETE FROM cmsfielditemcustomsetup WHERE cmsfielditemid = '$id'";
		$objDB->write($sqlDel);
	}
	
	/**
	 * Henter rettigehder til et element
	 *
	 * @param string $html_id Bruges i HTML'en, til at definere hvert tagname
	 * @return array
	 */
	function getPerms($cmsfielditemid, $colname = '') {
		
		$objDB = tuksiDB::getInstance();
		
		$html_id = ($cmsfielditemid) ? $cmsfielditemid :$colname;

		$sql = "SELECT * FROM cmsfieldperm p WHERE p.cmsfielditemid = '{$cmsfielditemid}'";
		$rs = $objDB->fetch($sql);
		
		$arrPerms = array();
		foreach ($rs['data'] as $row) {
			$arrPerms[$row['cmsgroupid']] = $row;
		}
		
		$arrGroups = $this->getUserGroups();
		
		$arrReturn = array();
		foreach ($arrGroups as $id => $arrGrp) {
			$arrRights = $arrPerms[$id];
			$arrRights['name'] = $arrGrp['name'];
			$arrRights['id'] = $html_id . '_' . $id;
			$arrRights['groupid'] = $id;
			$arrReturn[] = $arrRights;
		}	
		
		return $arrReturn;
	}
	
	/**
	 * Henter rettigheder for et element
	 *
	 * @param string $html_id Bruges i HTML'en, til at definere hvert tagname
	 * @return array
	 */
	function getUserPerms($cmsfielditemid, $userid) {
							
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT c.name, c.id as c_cmsgroupid, p.* FROM cmsusergroup ug,cmsgroup c ";
		$sql.= "LEFT JOIN cmsfieldperm p ON (c.id = p.cmsgroupid AND p.cmsfielditemid = '{$cmsfielditemid}') ";
		$sql.= "WHERE ug.cmsuserid = '$userid' AND ug.cmsgroupid = c.id ORDER BY c.name";
	
		$rs = $objDB->fetch($sql);
	
		$arrRights = array();
		foreach ($rs['data'] as $row) {
			$arrRights = array(
				'padd' => $arrRights['padd'] ? 1 : $row['padd'],
            'pread' => $arrRights['pread'] ? 1 : $row['pread'],
            'psave' => $arrRights['psave'] ? 1 : $row['psave'],
            'padmin' => $arrRights['padmin'] ? 1 : $row['padmin'],
            'pdelete' => $arrRights['pdelete'] ? 1 : $row['pdelete'],
            'plist' => $arrRights['plist'] ? 1 : $row['plist']
			
			);
		}
		
		return $arrRights;
	}
	
	private function getUserGroups() {
		
		$objDB = tuksiDB::getInstance();
		
		if(!empty($this->arrUserGroups)) {	
			return $this->arrUserGroups;
		} else {
			$sqlUserGrps = "SELECT * FROM cmsgroup order by name";
			$rsUserGrps = $objDB->fetch($sqlUserGrps);
			if($rsUserGrps['num_rows'] > 0){
				foreach($rsUserGrps['data'] as $arrUserGrp) {
					$this->arrUserGroups[$arrUserGrp['id']] = $arrUserGrp;
				}
				return $this->arrUserGroups;
			}
		}
	}
	/**
	 * Sætter delete_me ligemed 1, så man kan se hvilken rækker der er gemt. Delete_me bliver 0 ved gem.
	 *
	 * @param int $cmsfielditemid
	 */
	function preparePerm($cmsfielditemid = 0) {
		
		$objDB = tuksiDB::getInstance();		
		
		if ($cmsfielditemid)
			$this->cmsfielditemid = $cmsfielditemid;
		
		$sql = "UPDATE cmsfieldperm SET delete_me = 1 WHERE cmsfielditemid = '{$this->cmsfielditemid}'";
		$objDB->write($sql);
	}
	
	/**
	 * Tilføj rettighed til fielditemID ($this->cmsfielditemid)
	 *
	 * @param int $cmsgroupid
	 * @param int $pread
	 * @param int $plist
	 * @param int $psave
	 * @param int $padmin
	 * @param int $padd
	 * @param int $pdelete
	 */
	function addPerm($cmsgroupid, $pread = 1, $plist= 1, $psave= 0, $padmin = 0, $padd = 0, $pdelete = 0) {
		if ($this->cmsfielditemid) {
			
			$objDB = tuksiDB::getInstance();		
			
			$sql = "REPLACE INTO cmsfieldperm (cmsfielditemid, cmsgroupid, pread, plist, psave, padmin, padd, pdelete)";
			$sql.= " VALUES('{$this->cmsfielditemid}', '$cmsgroupid', '$pread', '$plist', '$psave', '$padmin', '$padd', '$pdelete')";
			
			$objDB->write($sql);
		}
		
	}
	
		
	function addStandardPerms($cmsfielditemid) {
		$this->setCmsFieldItemID($cmsfielditemid);
		$this->preparePerm();
		$this->addPerm(9, 1, 0, 1);
		$this->addPerm(1, 1, 0, 1);
		$this->cleanupPerm();
	}
	
	/**
	 * Sletter alle række med delete_me =1 og cmsfielditemid = $this->cmsfielditemid
	 *
	 */
	function cleanupPerm() {
		if ($this->cmsfielditemid) {
			
			$objDB = tuksiDB::getInstance();		
			
			$sql = "DELETE FROM cmsfieldperm WHERE cmsfielditemid = '{$this->cmsfielditemid}' AND delete_me = 1";
			$objDB->write($sql);
		}
	}
	
	/**
	 * Sætter delete_me ligemed 1, så man kan se hvilken rækker der er gemt. Delete_me bliver 0 ved gem.
	 *
	 * @param int $cmsfielditemid
	 */
	function prepareExtra($cmsfielditemid = 0) {

		$objDB = tuksiDB::getInstance();
		
		if ($cmsfielditemid)
			$this->cmsfielditemid = $cmsfielditemid;
		
		$sql = "UPDATE cmsfieldvalue SET delete_me = 1 WHERE cmsfielditemid = '{$this->cmsfielditemid}'";
		$objDB->write($sql);
	}
	
	
	
	/**
	 * Tilføjer en ekstraværdi til fielditem
	 *
	 */
	function addExtra($cmsvariableid, $value) {

		if ($this->cmsfielditemid) {
			
			$objDB = tuksiDB::getInstance();
						
			$sql = "REPLACE INTO cmsfieldvalue (cmsfielditemid, cmsvariableid, value)";
			$sql.= " VALUES('{$this->cmsfielditemid}', '$cmsvariableid', '" . $objDB->realEscapeString($value) . "')";			
			$objDB->write($sql);
		}
		
	}
	
	/**
	 * Sletter alle række med delete_me =1 og cmsfielditemid = $this->cmsfielditemid
	 *
	 */
	function cleanupExtra() {
		if ($this->cmsfielditemid) {
			
			$objDB = tuksiDB::getInstance();
			
			$sql = "DELETE FROM cmsfieldvalue WHERE cmsfielditemid = '{$this->cmsfielditemid}' AND delete_me = 1";
			//print $sql . "<br>";
			$objDB->write($sql);
		}
	}

	/**
	 * Tilføjer standard værdier til nyt element.
	 *
	 * @param int $cmsfielditem
	 */
	function findValues($colname, $cmsfielditem = 0) {

		$objDB = tuksiDB::getInstance();
		
		if(count(self::$arrFieldAutoset) == 0) {
			$sql = "SELECT * FROM cmsfieldautoset ORDER BY weight";
			$rs = $objDB->fetch($sql);			
			if ($rs['ok'] && $rs['num_rows'] > 0) {
				self::$arrFieldAutoset = $rs['data'];	
			}
		}
		foreach (self::$arrFieldAutoset as $arrAutoSet) {
			if (preg_match($arrAutoSet['reg'], $colname)) {
				if ($cmsfielditem)
					$this->updateItemFields($cmsfielditem, array(	'seq' => $arrAutoSet['seq'], 
																												'cmsfieldtypeid' => $arrAutoSet['cmsfieldtypeid'], 
																												'name' => $arrAutoSet['name']));
				return $arrAutoSet;
			}
		}
		return array();
	}
	
	
	function loadFieldTypes(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		$sqlType = "SELECT * FROM cmsfieldtype ORDER BY name";
		$rsFieldtype = $objDB->fetch($sqlType);
		
		self::$arrFieldTypeOptions[0] = ' - ' . $objPage->cmsText('choose_fieldtype') . ' - ';
		
		foreach ($rsFieldtype['data'] as $arrFieldtype) {
			self::$arrFieldTypes[$arrFieldtype['id']] = $arrFieldtype;
			self::$arrFieldTypeOptions[$arrFieldtype['id']] = $arrFieldtype['name'] . " ({$arrFieldtype['id']})";
		}
	}
	
	/**
	 * Henter alle fieldtypes og sætte $cmsfieldtypeid_selected aktiv. Returnere et array til HTML
	 *
	 * @param int $cmsfieldtypeid_selected
	 * @return array
	 */
	function getFieldTypes($cmsfieldtypeid_selected) {
		
		if(count(self::$arrFieldTypes) == 0) {
			$this->loadFieldTypes();
		}
		
		$fieldtypes_selected = 0;
		
		if(isset(self::$arrFieldTypes[$cmsfieldtypeid_selected])) {
			
			$arrFieldtype = self::$arrFieldTypes[$cmsfieldtypeid_selected];
			
			// get selected fieldtype's description
			$arrFielddesc[0] = nl2br($arrFieldtype['description']);
			$arrFielddesc[1] = $arrFieldtype['desc1'];
			$arrFielddesc[2] = $arrFieldtype['desc2'];
			$arrFielddesc[3] = $arrFieldtype['desc3'];
			$arrFielddesc[4] = $arrFieldtype['desc4'];
			$arrFielddesc[5] = $arrFieldtype['desc5'];

			$fieldtype_extrafieldvalues = $arrFieldtype['extrafieldvalues'];
						
			$fieldtypes_selected = $arrFieldtype['id'];
			
			if($arrFieldtype['special_setup']) {
				$specialSetup = array('classname' => $arrFieldtype['classname']);
			}
			
		}
		return array('options' => self::$arrFieldTypeOptions, 'selected' => $fieldtypes_selected, 'fielddesc' => $arrFielddesc, 'extrafields' => $fieldtype_extrafieldvalues, 'special_setup' => $specialSetup);
	}
	
	function loadFieldGroups(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlGroup = "SELECT id, name FROM cmsfieldgroup ORDER BY name";
		$rsGroups= $objDB->fetch($sqlGroup);
		
		foreach ($rsGroups['data'] as $arrGroup) {
			self::$arrFieldGroups[$arrGroup['id']] = $arrGroup['name'];
		}
	}
	
	/**
	 * Henter alle feltgrupper
	 *
	 * @param int $cmsfieldgroupid_selected
	 * @return array
	 */
	function getFieldGroups($cmsfieldgroupid_selected) {
		
		if(count(self::$arrFieldGroups) == 0) {
			$this->loadFieldGroups();
		}
		
		return array('options' => self::$arrFieldGroups, 'selected' => $cmsfieldgroupid_selected);
	}
	
	/**
	 * Henter ekstraværdier til HTML
	 *
	 * @param int $cmsfielditemid
	 * @param string $extrafields
	 * @return array
	 */
	function getExtraFieldValues($cmsfielditemid, $cmsfieldtypeid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT v.id, v.name, fev.description, f.value ";
		$sql.= "FROM cmsfieldextravalue fev, cmsvariable v ";
		$sql.= "LEFT JOIN cmsfieldvalue f ON (f.cmsfielditemid = '$cmsfielditemid' AND f.cmsvariableid = v.id )";
		$sql.= "WHERE fev.cmsvariableid = v.id AND fev.cmsfieldtypeid = '$cmsfieldtypeid' ";
		$sql.= " ORDER BY v.name";
		
		$rsFieldvalues = $objDB->fetch($sql);
		
		foreach ($rsFieldvalues['data'] as $arrFieldValue) {
			$arrExtraFieldvalues[] = $arrFieldValue;	
		}
		return $arrExtraFieldvalues;
		
	} // End function getExtraFieldValues()
	
	
	function getCustomSetup($arrField, $arrSetup) {

		$classname = $arrSetup['classname'];
		
		$arrReturn = call_user_func(array($classname, "getSetup"),$arrField);
		return $arrReturn;
		
	}
	
	function saveCustomSetup($arrField, $arrSetup) {

		$classname = $arrSetup['classname'];
		
		$arrReturn = call_user_func(array($classname, "saveSetup"),$arrField);
		return $arrReturn;
		
	}
	
	/**
	 * Kopier fieldItem med rettigheder og ekstraværdier
	 *
	 * @param unknown_type $cmsfielditemid_from
	 */
	function copyItem($cmsfielditemid_from) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlFields = "SHOW COLUMNS FROM cmsfielditem";
		$rs = $objDB->fetch($sqlFields,array('type' => 'object'));
		
		// Sætter array med feltnavne. Id og delete_me skal IKKE med
		$arrFields = array();
		
		foreach ($rs['data'] as $objFields) {
			if ($objFields->Field != 'id' && $objFields->Field != 'delete_me') {
				$arrFields[] = $objFields->Field;
			}
		}
		
		$sqlInsert = "INSERT INTO cmsfielditem (" . join(", ", $arrFields) . ") SELECT " . join(", ", $arrFields) . " FROM cmsfielditem WHERE id = '$cmsfielditemid_from'";
		$objDB->write($sqlInsert);
		
		$cmsfielditemid_to = mysql_insert_id();
		
		
		if ($cmsfielditemid_to) {
			$this->copyPerm($cmsfielditemid_from, $cmsfielditemid_to);
			$this->copyExtra($cmsfielditemid_from, $cmsfielditemid_to);	
		}
			
		return $cmsfielditemid_to;	
	} // End function copyItem
	
	/**
	 * Kopier rettigheder til en cmsfielditem til en anden
	 *
	 * @param int $cmsfielditemid_from
	 * @param int $cmsfielditemid_to
	 */
	function copyPerm($cmsfielditemid_from, $cmsfielditemid_to) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "INSERT INTO cmsfieldperm (cmsfielditemid, cmsgroupid, padd, pread, padmin, pdelete, plist, delete_me)";
		$sql.= " SELECT concat('$cmsfielditemid_to'), cmsgroupid, padd, pread, padmin, pdelete, plist, concat('0') FROM cmsfieldperm ";
		$sql.= " WHERE cmsfielditemid = '$cmsfielditemid_from'";
		
		$objDB->write($sql);
				
	}
	
	/**
	 * Kopier ekstraværdier til nyt fielditem
	 *
	 * @param int $cmsfielditemid_from
	 * @param int $cmsfielditemid_to
	 */
	
	function copyExtra($cmsfielditemid_from, $cmsfielditemid_to) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "INSERT INTO cmsfieldvalue (cmsfielditemid, cmsvariableid, value, delete_me)";
		$sql.= " SELECT concat('$cmsfielditemid_to'), cmsvariableid, value, concat('0') FROM cmsfieldvalue ";
		$sql.= " WHERE cmsfielditemid = '$cmsfielditemid_from'";
		
		$objDB->write($sql);
				
	}
	
	/**
	 * Returnere HTML med fielditem redigering.
	 *
	 * @param array $arrFieldItems Fielditems der skal vises
	 * @param array $arrTplData Ekstra data til smarty template
	 * @return string HTML
	 */
	function getHTML($arrFieldItems, $arrTplData = array()) {
		
		$tpl = new tuksiSmarty();
		
		$arrTplData['itemtype'] = $this->itemtype;
		
		$tpl->assign('tpldata', $arrTplData);
		
		$arrSetup = array();
		
		foreach ($arrFieldItems as $key => $arrFieldItem) {
			
			
			if(empty($arrSetup)) {
				
				$arrSetup = array('relationid' => $arrFieldItem['relationid'],
													'type' => $arrFieldItem['itemtype'],
													'tablename' => $arrFieldItem['tablename']);
				
			}
			
			
					
			//need to make a fieldtype for name
			$objField = new stdClass;
			$objField->htmltagname = 'name_' . $arrFieldItem['colname'];				
			$objField->value = $arrFieldItem['name'];														
			$objField->readonly = false;														
			$objField->id = $arrFieldItem['id'];																
			$objField->row = $arrFieldItem['id'];													
			$objFieldSuggest = new fieldTextSuggest($objField);

			$arrHtml = $objFieldSuggest->getHTML();	
			
			$arrFieldItems[$key]['name_html'] = $arrHtml['html'];
			
			// Getting fieldtypes
			$arrFieldtypes = $this->getFieldTypes($arrFieldItem['cmsfieldtypeid']);
				
			$arrFieldItems[$key]['fieldtypes_options'] = $arrFieldtypes['options'];
			$arrFieldItems[$key]['fieldtypes_selected'] = $arrFieldtypes['selected'];
			$arrFieldItems[$key]['fieldtypedescription'] =  $arrFieldtypes['fielddesc'][0];
			$arrFieldItems[$key]['fieldvalue1desc'] =  $arrFieldtypes['fielddesc'][1];
			$arrFieldItems[$key]['fieldvalue2desc'] =  $arrFieldtypes['fielddesc'][2];
			$arrFieldItems[$key]['fieldvalue3desc'] =  $arrFieldtypes['fielddesc'][3];
			$arrFieldItems[$key]['fieldvalue4desc'] =  $arrFieldtypes['fielddesc'][4];
			$arrFieldItems[$key]['fieldvalue5desc'] =  $arrFieldtypes['fielddesc'][5];
			
			
			if($arrFieldtypes['special_setup']) {
				$arrFieldItems[$key]['customsetup'] = $this->getCustomSetup($arrFieldItem,$arrFieldtypes['special_setup']);
			}
			
			
			$arrFieldItems[$key]['extrafieldvalues'] = $this->getExtraFieldValues($arrFieldItem['id'], $arrFieldItem['cmsfieldtypeid']); 
			  
			// Getting fieldgroups
			$arrFieldgroups = $this->getFieldGroups($arrFieldItem['cmsfieldgroupid']);
			$arrFieldItems[$key]['fieldgroups_options'] = $arrFieldgroups['options'];
			$arrFieldItems[$key]['fieldgroups_selected'] = $arrFieldgroups['selected'];
				
			// Getting user permissions, samt gør klar til html tags med $colname
			$arrFieldItems[$key]['rights_options'] = $this->getPerms($arrFieldItem['id'], $arrFieldItem['colname']);
			
			
		}
		
		$tpl->assign("fields", $arrFieldItems);
		$tpl->assign("setup", $arrSetup);
		
		$objPage = tuksiBackend::getInstance();
		
		$objPage->addJavascript('/javascript/backend/libs/tuksi.fielditem.js');
		
		if($this->itemtype == 'media') {
			return $tpl->fetch('fielditem_media.tpl');
		} else {
			return $tpl->fetch('fielditem.tpl');
		}	
	}
	
	/**
	 * Gemmer fielditems som er sat med getHTML funktionen
	 *
	 * @param array $arrFieldItems
	 */
	function saveData($arrFieldItems) {
		
		$intSeq = 100;
		
		$updateSeq = true;
		
		$objDB = tuksiDB::getInstance();
		
		$strJSON = $_POST->getStr('json');
		if(!empty($strJSON)) {
			$json = new tuksiJSON();
			$objValues = $json->parse($strJSON);
			if($objValues->saveItemSeq) {
				$updateSeq = false;
				$arrItems = tuksiTools::jarray($objValues->items);
				$seq = 100;
				foreach($arrItems as $itemId) {
					$this->updateItemSeq($itemId, $seq);	
					$seq+= 100;
				}
			}
		}
		
		foreach ($arrFieldItems as $key => $arrFieldItem) {
			
			if($updateSeq) {
			
				$arrFieldItem['seq'] = $intSeq;
				
				if ($_POST->getInt('fieldid_' . $arrFieldItem['id'] . '_up')) {
					$arrFieldItem['seq']-=150;
				}
				if ($_POST->getInt('fieldid_' . $arrFieldItem['id'] . '_down')) {
					$arrFieldItem['seq']+=150;
				}
				
				$this->updateItemSeq($arrFieldItem['id'], $arrFieldItem['seq']);
			
			}
			// * -------------------------------------------------------------------*
			// Henter rettigheder og ekstraværdier fra POST
			// * -------------------------------------------------------------------*
		
			$arrRights = array();
			$arrExtra = array();
			
			$arrPerms = $this->getPerms($arrFieldItem['id'],$arrFieldItem['colname']);
			
			$arrPermType = array("read","add","delete","list","save","admin");
			foreach ($arrPerms as $perm) {
				foreach ($arrPermType as $permType) {
					if($_POST->getStr("right_".$permType."_".$perm['id'])) {
						$arrRights[$perm['groupid']][$permType] = 1;
					}
				}
			}
			
			// Saving group perms
			$this->preparePerm($arrFieldItem['id']);
			foreach ($arrRights as $cmsgroupid => $arrValues) {
				$this->addPerm($cmsgroupid, $arrValues['read'], $arrValues['list'], $arrValues['save'], $arrValues['admin'], $arrValues['add'], $arrValues['delete']);
			}
			
			$this->cleanupPerm();
	
			//getting extra values
			$arrExtraTypes = $this->getExtraFieldValues($arrFieldItem['id'],$arrFieldItem['cmsfieldtypeid']);
			
			// Saving extra field values
			if (count($arrExtraTypes)) {
			
				$this->prepareExtra($arrFieldItem['id']);
				
				foreach ($arrExtraTypes as $arrExtraType) {
					
					$value = $_POST->getStr('extrafieldvalues_' . $arrFieldItem['id'] . '_' . $arrExtraType['id'] . '_value');

					$this->addExtra($arrExtraType['id'], $value);
				}
				$this->cleanupExtra();
			}
			
			//check to if there is an special_setup
			if($arrFieldItem['cmsfieldtypeid'] > 0) {
				$arrSettings = $objDB->fetchRow('cmsfieldtype',$arrFieldItem['cmsfieldtypeid']);
				if($arrSettings['special_setup']) {
					$this->saveCustomSetup($arrFieldItem,$arrSettings);
				}
			}
			
			$intSeq+= 100;
		} // End foreach field
	}
}

?>
