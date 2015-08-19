<?
/**
 * Required files for getting the fieldtype to work:
 * AJAX : services/ajax/backend/fieldRelatedContentAjaxSearch.php 
 *
 * SMARTY TPL FILES:
 * fieldRelatedContentAjaxSearch_rel_list.tpl
 * fieldRelatedContentAjaxSearch_search_list.tpl
 * fieldRelatedContentAjaxSearch.tpl
 *
 * Fieldvalue1: Search SQL.  #QUERY# is replaced with querystring: 
 * 		Sample: SELECT id, title AS name, body AS text, date_format(ts, '%y-%m-%d %h:%i') as date FROM nyheder.art WHERE id LIKE '%#QUERY#%' OR title LIKE '%#QUERY#%' ORDER BY id DESC limit 0,10
 * 
 * Fieldvalue2: SQL for getting elements #IDS# contains element IDs ["1,3,4,5"]. Use like "id IN(#IDS#)". Must return id, name
 * 		Sample: SELECT id, title AS name FROM nyheder.art WHERE id IN (#IDS#)
 * 
 * Fieldvalue3: SQL for deleting old values, when saving. #ROWID# is current row.
 * 		Sample: DELETE FROM nyheder.artrelatedlinks WHERE artid = '#ROWID#'
 * 
 * Fieldvalue4: Insert SQL for saving data. #ROWID#, #SEQ#, #EID#, #ISACTIVE# is available.
 * 		Sample: INSERT INTO nyheder.artrelatedlinks (artid,seq,link_artid, showonfrontpage) VALUES ('#ROWID#','#SEQ#','#EID#', '#ISACTIVE#')
 * 
 * Fieldvalue5: SQL for getting added elements. #ROWID# can be used.id, seq, iactive must be returned.
 * 		Sample: SELECT link_artid AS id, seq, showonfrontpage AS isactive FROM nyheder.artrelatedlinks WHERE artid = '#ROWID#'
 *
 * FIELDVALUE6: Relation table and Where clause for releasing
 * 		Sample: c_rel;parentid = #ROWID#
 *
 * @package tuksiFieldType
 */

class fieldRelatedContentAjaxSearch extends field{

	static private $arrField;
	
	function __construct($objField) {
		parent::field($objField, true);
		
		$this->validateFieldvalue("Felttype 1", $this->objField->fieldvalue1, "Search SQL.  #QUERY# is replaced with querystring");
		$this->validateFieldvalue("Felttype 2", $this->objField->fieldvalue2, "SQL for getting elements #IDS# contains element IDs [\"1,3,4,5\"]. Use like \"id IN(#IDS#)\". Must return id, name");
		$this->validateFieldvalue("Felttype 3", $this->objField->fieldvalue3, "SQL for deleting old values, when saving. #ROWID# is current row");
		$this->validateFieldvalue("Felttype 4", $this->objField->fieldvalue4, "Insert SQL for saving data. #ROWID#, #SEQ#, #EID#, #ISACTIVE# is available");
		$this->validateFieldvalue("Felttype 5", $this->objField->fieldvalue5, "SQL for getting added elements. #ROWID# can be used.id, seq, iactive must be returned.");
	}

	function getHTML() {
		if ($arrReturn = $this->checkFieldvalues()) {
			return $arrReturn;
		}
		
		$tpl = new tuksiSmarty();
		$objDB = tuksiDB::getInstance();
		
		$sqlSearch = $this->rowDataReplace($this->objField->fieldvalue1);

		if (strpos($sqlSearch, "#QUERY#") === false) {
			$mode = 'selectbox';

			$arrList = $objDB->fetch($sqlSearch);
			
			$tpl->assign('data', $arrList['data']);
		} else {
			$mode = 'search';
		}

		$tpl->assign('mode', $mode);

		//tpl data
		$tpl->assign("fieldid", $this->objField->id);
		$tpl->assign("htmltagname", $this->objField->htmltagname);
		
		$arrValues = $this->getValuesDb();
				
		$tpl->assign("datalist", $this->relList($arrValues, $this->objField->id, $this->objField->htmltagname));
		$tpl->assign("list_values", self::makeValues($arrValues));
		
		$html = $tpl->fetch('fieldtypes/fieldRelatedContentAjaxSearch.tpl');
		
		return parent::returnHtml($this->objField->name,$html);
	}

	/**
	 * Enter description here...
	 *
	 * @param string $searchquery
	 * @param int $fieldid
	 * @return string
	 */
	static function ajaxGetValue($searchquery, $fieldid, $htmltagname) {
		
		$objDB = tuksiDB::getInstance();
		$tplList = new tuksiSmarty();
		
		$tplList->assign("fieldid", $fieldid);
		$tplList->assign("htmltagname", $htmltagname);
		
		if (!isset(self::$arrField)) {
			self::$arrField = tuksiFielditemBase::getFieldByID($fieldid);
		}
		$sqlSearch = self::$arrField['fieldvalue1'];
		$sqlSearch = str_replace('#QUERY#', $searchquery, $sqlSearch);
		
		$arrRsSearch = $objDB->fetch($sqlSearch);
		
		if ($arrRsSearch['num_rows'] && $arrRsSearch['ok']) {
			$tplList->assign("arrSearch", $arrRsSearch['data']);
		} else {
			$tplList->assign("error", true);
		}

		return $tplList->fetch("fieldtypes/fieldRelatedContentAjaxSearch_search_list.tpl");
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $elementId IDs splittet by ","
	 * @param unknown_type $fieldid
	 * @param unknown_type $list_values
	 * @param unknown_type $isactive
	 * @return unknown
	 */
	function ajaxAddRelation($elementId, $fieldid, $htmltagname, $list_values, $isactive = '') {
		
		// tjek hvor mange arts der er tilføjet
		if ($list_values) {
			$arrList = explode(";", $list_values);
			$count = count($arrList) + 10;
		} else {
			$arrList = array();	
			$count = 10;
		}
		
		// Tilføje den nye
		if ($list_values) {
			$list_values .= ';';
		}
		$arrElements = explode(",", $elementId);
		
		$active = (empty($isactive) ? "0" : "1");
		
		foreach ($arrElements as &$elementId) {
			if ($elementId) {
				$arrTmp[] = $elementId . ":{$count}:{$active}";
				$count += 10;
			}
		}
		
		$list_values .= join(';', $arrTmp);
		
		$arrList = self::parseValues($list_values);
		
		$data = tuksiTools::encode(self::relList($arrList, $fieldid, $htmltagname));
			
		$list_values = self::makeValues($arrList);
		
		//print_r($list_values);
		
		return array('data' => $data, 'value' => $list_values);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $elementId
	 * @param unknown_type $fieldid
	 * @param unknown_type $list_values
	 * @return unknown
	 */
	function ajaxRemoveRelation($elementId, $fieldid, $htmltagname, $list_values) {
			
		$arrList = self::parseValues($list_values);
		
		unset($arrList[$elementId]);
		
		$data = tuksiTools::encode(self::relList($arrList, $fieldid, $htmltagname));
			
		$list_values = self::makeValues($arrList);
		
		return array('data' => $data, 'value' => $list_values);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $elementId
	 * @param unknown_type $fieldid
	 * @param unknown_type $list_values
	 * @param unknown_type $type
	 * @return unknown
	 */
	function ajaxChangeSeq($elementId, $fieldid, $htmltagname, $list_values, $type = 'up') {

		$arrList = self::parseValues($list_values);
		
		if ($type == "up") {
			$arrList[$elementId]['seq'] -= 15;
		} else if ($type == "down") {
			$arrList[$elementId]['seq'] += 15;
		}
				
		// Make sort
		uasort($arrList, array('self', 'sortElement'));
		
		$data = tuksiTools::encode(self::relList($arrList, $fieldid, $htmltagname));
			
		$list_values = self::makeValues($arrList);
		
		return array('data' => $data, 'value' => $list_values);
	}
	
	/**
	 * Convert string list to array
	 * 
	 * @param string $list_values
	 */
	static function parseValues($list_values) {
		
		$arrDataList = array();
		
		// convert to array
		if (!empty($list_values)) {
			$arrList = explode(";", $list_values);
			
			foreach ($arrList as &$artData) {
				$arrData = array();
				$arrArtData = explode(':', $artData);
				
				$arrData['id'] = $arrArtData[0];
				$arrData['seq'] = $arrArtData[1];
				$arrData['isactive'] = $arrArtData[2];
				
				$arrDataList[$arrData['id']] = $arrData;
			}
			
		} else {
			$arrDataList = array();	
		}
		
		
		return $arrDataList;
	}
	
	function getValuesDb() {
		
		$objDB = tuksiDB::getInstance();	
		
		$sql = $this->objField->fieldvalue5;
		$sql = str_replace('#ROWID#', $this->objField->rowid, $sql);
		
		$rsDataList = $objDB->fetch($sql);
		
		$arrDataList = array();
		foreach ($rsDataList['data'] as &$arrData) {
			$arrDataList[$arrData['id']] = $arrData;
		}
		
		return $arrDataList;
	}
	
	static function makeValues($arrDataValues) {
		$arrValues = array();
	
		$seq = 0;
		foreach ($arrDataValues as &$arrValue) {
			$seq += 10;
			$arrValues[] = $arrValue['id'] . ':' . $seq . ':' . $arrValue['isactive'];
		}
		
		return join(';', $arrValues);
	}
	
	function sortElement($a, $b) {
   	 	if ($a['seq'] == $b['seq']) {
   	    	 return 0;
 		}
    	return ($a['seq'] < $b['seq']) ? -1 : 1;
	}

	function relList($arrElementList, $fieldid, $htmltagname) {

		if ($this && $this->objField->fieldvalue2) {
			$sqlElements = $this->objField->fieldvalue2;
		} else {
			if (!isset(self::$arrField)) {
				self::$arrField = tuksiFielditemBase::getFieldByID($fieldid);
			}
			$sqlElements = self::$arrField['fieldvalue2'];
		}
		
		$objDB = tuksiDB::getInstance();			
		
		$tplList = new tuksiSmarty();

		if (count($arrElementList)) {
			$sqlElements = str_replace('#IDS#', "'" . join("','", array_keys($arrElementList)) . "'", $sqlElements);
			$arrRsElements = $objDB->fetch($sqlElements);

			foreach ($arrRsElements['data'] as &$arrElement) {
				$arrElementList[$arrElement['id']]['name'] = $arrElement['name'];
			}
			//print_r($arrElementList);
		}

		$tplList->assign("fieldid", $fieldid);
		$tplList->assign("htmltagname", $htmltagname);
		$tplList->assign("elementlist", $arrElementList);

		if ($this && $this->arrFieldvalues['VALUE1']) {
			$tplList->assign("show_checkbox", true);
		}
		
		return $tplList->fetch("fieldtypes/fieldRelatedContentAjaxSearch_rel_list.tpl");
	}
	
	function saveData() {
		
		$objDB = tuksiDB::getInstance();
		
		$fieldid = $this->objField->id;
		$htmltagname = $this->objField->htmltagname;
		$rowId = $this->objField->rowid;
		
		//hent hidden values og lav dem til et array
		$strValues = $_POST->getStr($htmltagname . "_relatedLinks");
		$arrValues = self::parseValues($strValues);
		
		//slet alt fra db'en omkring den pågældende artikel
		
		$sqlDeleteOldLinks = $this->objField->fieldvalue3;
		$sqlDeleteOldLinks = str_replace('#ROWID#', $rowId, $sqlDeleteOldLinks);
		
		$arrRsDelete = $objDB->write($sqlDeleteOldLinks);
		
		//Lav indsætningen af nye felter
		$sqlInsertLinksBase = $this->objField->fieldvalue4;
				
		$seq = 0;
		foreach ($arrValues as &$value) {
			$seq++;
			
			$sqlInsertLinks = str_replace('#ROWID#', $rowId, $sqlInsertLinksBase);
			$sqlInsertLinks = str_replace('#SEQ#', $seq, $sqlInsertLinks);
			$sqlInsertLinks = str_replace('#EID#', $value['id'], $sqlInsertLinks);
			$sqlInsertLinks = str_replace('#ISACTIVE#', $value['isactive'], $sqlInsertLinks);
			
			$rsWriteLinks = $objDB->write($sqlInsertLinks);
		}	
	}

	function getListHtml() {
		return '';
	}
	
	function releaseData() {
		$objPage = tuksiBackend::getInstance();
		
		$arrRelease = explode(';', $this->arrFieldvalues['FIELDVALUE6']);
		$relationtable = trim($arrRelease[0]);
		
		$sqlAppend = '';
		if (isset($arrRelease[1])) {
			$sqlAppend = str_replace("#ROWID#", $this->objField->rowid, " WHERE " . trim($arrRelease[1]));
		}
		
		if (!tuksiRelease::releaseTable($relationtable, null, $sqlAppend)) {
			$objPage->alert($objPage->cmstext("pagereleasefailed"));
		}
	}

}
?>
