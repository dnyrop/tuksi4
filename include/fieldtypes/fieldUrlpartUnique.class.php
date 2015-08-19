<?
// * ------------------------------------------------------------------------------------------------------------- *
// Denne klasse bruges til at validere indtastede url sti, så en tekst er unique i tabelfelt 
// F.eks: /katalog/side1 . Her tjekkes at 'side1' ikke er indtastet flere gange i tabellen
// Dette gør at man ikke behøver ID'ere i url'en.

// hjo@dwarf.dk
// * ------------------------------------------------------------------------------------------------------------- *
class fieldUrlpartUnique extends field {

	function fieldUrlpartUnique($objField){
		parent::field($objField);
		$this->objDB = tuksiDB::getInstance();
	}
	
	// * ----------------------------------------------------------------------------------------------- *
	// Laver HTML til side
	// * ----------------------------------------------------------------------------------------------- *
	function getHTML() {
		if (!$this->objField->fieldvalue1) {
			$this->objField->fieldvalue1 = "200";
		}
		
		if (!$this->objField->fieldvalue2) {
			$this->objField->fieldvalue2 = false;
		}
		
		$Html  = parent::getHtmlStart();
		$Html .= "<input onchange=\"javascript:changed = 1;\" class=\"text\" type=\"text\" name=\"{$this->objField->htmltagname}\" value=\"{$this->objField->value}\">";
		
		return parent::returnHtml($this->objField->name,$Html);
	} // End getHTML()

	// * ----------------------------------------------------------------------------------------------- *
	// Gemmer data ved tryk på Gem knap 
	// * ----------------------------------------------------------------------------------------------- *
	function saveData() {
		$bookOk = 1;
		$sql = "";
  		if ($this->objField->fieldvalue2 && empty($this->objField->value)) {
  			$sql = $this->objField->colname . " = ''";
  		} else {
			if ($this->objField->value) {
				// Tjekker om værdi findes i samme table
				// Urlpart må KUN være et gang i samme table
	  			$sql = "SELECT * FROM {$this->objField->tablename} WHERE {$this->objField->colname} = '{$this->objDB->escapeString($this->objField->value)}' AND id <> '{$this->objField->rowid}'";
	  		
		  		$arrRs = $this->objDB->fetchItem($sql);
		  		
		  		if ($arrRs['ok']) {
		  			if ($arrRs['num_rows']) {
		  				$bookOk = 0; 
		  				$error .= "Urlpart already exists.";
			  		}
		  		} else {
		  			$bookOk = 0;
		  			$error .= "Database check failed.";
		  		}
	  		
				// Tjek at urlpart ikke indholder tegn som ikke kan vises i browser url
	  			if (!preg_match("/^[a-zA-Z0-9\-\_]+$/", $this->objField->value, $m)){
	  				$bookOk = 0; 
	  				$error.= "Invalid chars in string. Only a-z, A-Z, 0-9, - and _ is valid.";
	  			}
	  		
				// Hvis ingen fejl gemmes til DB
	  			if ($bookOk) {
		  			$sql = $this->objField->colname . " = '" . $this->objField->value . "'";
	  			} else {
					// Lav fejlbesked til bruger
	  				$GLOBALS['error'][$this->objField->vcolname] = $error;
	  			}
	  		} else {
				// Lav fejlbesked til bruger
	  			$GLOBALS['error'][$this->objField->vcolname] = "Urlpart must be entered.";
	  		}
  		}
  	
		return $sql;
	} // End saveData()

	function unitTest() {

		$this->objField->tablename = 'cmstree';
		$this->objField->colname= 'name';
		$this->objField->value = 'name';

		$this->saveData();
		$this->getHTML();

		return $this->unitTestResult(1, 'ok');
	}

	// * ----------------------------------------------------------------------------------------------- *
	// Vis værdi i lista / listab
	// * ----------------------------------------------------------------------------------------------- *
	function getListHtml() {
		return $this->objField->value;
	}
} // End klasse fieldUrlpartUnique

?>
