<?php

/**
 * Updates pg_searchtext in cmstree 
 *
 * shellscripts/backend/shell_update_searchtext.php can be used to update multiple treeIds
 *
 * @package tuksiBackend
 */

class tuksiPageGeneratorSearch {
	
	private $optimizedText = '';
	private $originalText = '';
	private $rawText = '';
	private $arrFiles = array();
	
	function __construct() {

		$objDB = tuksiDB::getInstance();	

		$this->arrFieldTypes = $this->getSearchableFieldTypes();
	}

	function getSearchableFieldTypes() {
		$objDB = tuksiDB::getInstance();

		$sql = "SELECT classname FROM cmsfieldtype WHERE issearchable = 1";

		$arrReturn = $objDB->fetch($sql);

		$arrFieldtypes = array();

		if ($arrReturn['num_rows'] > 0) {
			foreach ($arrReturn['data'] as $arrFieldtype) {
				$arrFieldtypes[] = $arrFieldtype['classname'];
			}
		}

		return $arrFieldtypes;
	}
	
	function saveTreeDataChildren($treeid) {
		$objTree = tuksiTree::getInstance();

		$arrNodes = $objTree->getAllSubNodes($treeid, array(), array('no_deleted' => true));

		if (count($arrNodes)) {
			foreach ($arrNodes as $subTreeid) {
				$this->saveTreeData($subTreeid);
			}
		}
	}
	
	function saveTreeData($treeid) {
		
		$objDB = tuksiDB::getInstance();	

		$objTree = $objDB->fetchRow("cmstree", $treeid, 'object');
		
		$this->rawText = $objTree->pg_menu_name . " " . $objTree->pg_browser_title . " " . $objTree->name;
		
		//løber igennem alle areas
		$sqlArea = "SELECT * FROM pg_contentarea WHERE pg_page_templateid = '".$objTree->pg_page_templateid ."'";
		$rsArea = $objDB->fetch($sqlArea,array('type' => 'object'));
		foreach($rsArea['data'] as $objArea){ 
			// * ---------------------------------------------------------------------------------- *
			// Henter de tilknyttede moduler for det aktuelle area
			// * ---------------------------------------------------------------------------------- *
			$sqlMod = "SELECT c.* FROM pg_content c, pg_module m ";
			$sqlMod.= "WHERE c.cmstreeid = '{$treeid}' AND c.pg_contentareaid = '{$objArea->id}' AND c.isactive = '1' ";
			$sqlMod.= "AND m.id = c.pg_moduleid AND m.search_exclude = '0' ";
			$sqlMod.= "ORDER BY seq";
			
			$rsMod = $objDB->fetch($sqlMod,array('type' => 'object'));
			
			foreach ($rsMod['data'] as $objMod){
					
				$sqlFieldInModule = "SELECT distinct(fi.id), ft.classname, fi.colname, fi.name, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, fi.helptext ";
				$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldtype ft ";
				$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '".$objMod->pg_moduleid."' AND fi.cmsfieldtypeid = ft.id ";
				$sqlFieldInModule.= "ORDER BY fi.seq";
				
				$rsField = $objDB->fetch($sqlFieldInModule,array('type' => 'object'));
				//traveser de forskellige felttyper for det pågældende modul
				foreach ($rsField['data'] as $objField) {
					
					//sætter de nødvendige variable for klassen
					$objField->htmltagname	= "module_{$objMod->id}_{$objField->id}";
					$objField->value		= $_POST->getStr($objField->htmltagname);
					$objField->vcolname		= $objField->htmltagname;
					$objField->rowid		= $objMod->id;
					
					$this->addSearchData($objField->classname,$objMod->{$objField->colname},$objField);
				}
			}
		}
		$this->saveSearchData($treeid);
	}
	
	/**
	 * Tilføjer tilladte fieldtypes data til search index. 
	 * issearchable skal sætte til 1 på de fieldtypes som skal includeres.
	 * 
	 * @param mixed $fieldType 
	 * @param mixed $value 
	 * @param mixed $objField 
	 * @access public
	 * @return void
	 */
	function addSearchData($fieldType,$value,$objField){
		
		if(method_exists($fieldType, 'getSearchvalues')) {
			//opretter og gemmer værdierne i feltet
			$objNewField = new $objField->classname($objField);
			$objNewField->getSearchvalues($this);
		} elseif(in_array($fieldType, $this->arrFieldTypes) && strlen($value) > 0) {
			$this->addSearchValue($value);
		} elseif($fieldType == 'fieldPicturecrop') {
			$this->addFileValue($value);
		}
	}	
	
	function addSearchValue($value){
		$this->originalText.= " " . $value;
		$this->rawText.= " " . $value;	
	}
	
	function saveSearchData($treeid){
		
		$objDB = tuksiDB::getInstance();
		
		// Remove html entities
		$this->originalText = html_entity_decode($this->originalText);
		$this->rawText = html_entity_decode($this->rawText);
		
		//optimizin search text
		$this->optimizedText = $this->searchOptimizeText($this->rawText);
		
		if (is_array($this->arrFiles)) {
			$files = join(";", $this->arrFiles);
		}
		
		$sqlsave = "UPDATE cmstree SET pg_fulltext = '%s', pg_searchtext = '%s', pg_files = '%s' ";
		$sqlsave.= "WHERE id = '%d'";
		$sqlsave = sprintf($sqlsave, $objDB->realEscapeString(strip_tags($this->originalText)), $objDB->realEscapeString($this->optimizedText), $objDB->realEscapeString($files), $treeid);
		$objDB->write($sqlsave);
	}
	
	function addFileValue($value){
		$this->arrFiles[] = $value;
	}
	
	function searchOptimizeText($str) {
		
		if (strlen($str) > 0) {
		
			$str = strip_tags($str);
			
			// Replace all non-word characters and remaining htmlentities with space
			$str = preg_replace('/(&[a-zA-Z]+;|\W)+/', ' ', $str);
			$str = trim($str);

			$str = strtolower($str);
			
			$arrWords = explode(' ', $str);
			$arrWords = array_unique($arrWords);
			$arrWords2 = array();
			foreach ($arrWords as $word) {
				if (strlen($word) > 1) 
					$arrWords2[] = $word;
			}
			//print_r($arrWords2);
			if (is_array($arrWords2)) {
				$str = join(' ', $arrWords2);
			}
		}
		return $str;
	}
}
?>
