<?

/**
 * Generates an urlpart from the specified field
 * 
 * objField->fieldvalue1 = Field to process (default: name)
 * objField->fieldvalue2 = Additional where clause (fx. isactive = 1 AND c_dataid <> #ROWID#)
 * objField->fieldvalue3 = Set to "1" to disable checks for duplicates
 * 
 * @package tuksiFieldType
 *
 */
class fieldUrlpartGenerator extends field {

	function __construct($objField){
		parent::field($objField);
		$this->objDB = tuksiDB::getInstance();
	}
	
	/**
	 * Return the HTML for the fieldtype
	 *
	 * @return string $strHtml
	 */
	function getHTML() {		
		return parent::returnHtml("", "");
	} // End getHTML()

	/**
	 * Generates and saves urlpart
	 *
	 * @return string $sql
	 */
	function saveData() {
		// Check fieldvalue1
		$this->objField->fieldvalue1 = preg_replace('/\s+/', '', $this->objField->fieldvalue1);
		if (strlen($this->objField->fieldvalue1)) {
			$arrInput = explode(';', $this->objField->fieldvalue1);
		} else {
			$arrInput = array('name');
		} // if
		
		// Check fieldvalue3
		$this->objField->fieldvalue3 = intval($this->objField->fieldvalue3);
		
		$sqlSel = "SELECT %s FROM %s WHERE id = '%s'";
		$sqlSel = sprintf($sqlSel, join(', ', $arrInput), $this->objField->tablename, $this->objField->rowid);
		$arrRsSel = $this->objDB->fetchItem($sqlSel);
		
		$sqlReturn = "";
		if ($arrRsSel['ok'] && $arrRsSel['num_rows']) {
			foreach ($arrInput as &$strInput) {
				$strInput = $arrRsSel['data'][$strInput];
			} // foreach
			$strName = tuksiTools::fixname(join('_', $arrInput));
			
			// Check if name is empty
			if (!empty($strName) && !$this->objField->fieldvalue3) {	
				$strUrlpart = $strName;
				$iExt = 1;
				$bExists = true;
				// Keep checking until urlpart doesn't exist
				while ($bExists) {
					// Make where clause
					$strWhere = sprintf("id <> '%d' AND %s = '%s'", $this->objField->rowid, $this->objField->colname, $this->objDB->escapeString($strUrlpart));
					if ($this->objField->fieldvalue2) {
						$strWhere.= " AND " . $this->objField->fieldvalue2;
					} // if	
					
					// Check if urlpart already exists
					$sqlExist = sprintf("SELECT id FROM %s WHERE %s", $this->objField->tablename, $strWhere);
					$arrRsExist = $this->objDB->fetchItem($sqlExist);
					
					if ($arrRsExist['ok'] && $arrRsExist['num_rows']) {
						$strUrlpart = $strName . "_" . $iExt++;
					} else {
						$bExists = false;
						$strName = $strUrlpart;
					} // if
				} // while
			} // if
			
			$sqlReturn = sprintf("%s = '%s'", $this->objField->colname, $strName);
		} // if

		return $sqlReturn;
	} // End saveData()

	/**
	 * ListA / ListB display
	 *
	 * @return string
	 */
	function getListHtml() {
		return $this->objField->value;
	}
} // End class fieldUrlpartGenerator

?>
