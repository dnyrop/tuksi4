<?
// Template Class
/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldFieldtypeDesc extends field {

	function fieldFieldtypeDesc ($objField) {
		parent::field($objField);
	}
	function getHTML() {
		$objDB = tuksiDB::getInstance();		
		$HtmlTag = parent::getHtmlStart();

		if ($this->objField->fieldvalue1) {
			$objRow = $objDB->FetchRow($this->objField->tablename, $this->objField->rowid);
			$objRow = $objDB->FetchRow("cmsrowfieldtype", $objRow->{$this->objField->fieldvalue1});
			if ($objRow) {
				$html = "Beskrivelse: {$objRow->description}<br>";
				if ($objRow->desc1) $html.= "Feltværdi 1: {$objRow->desc1}<br>";
				if ($objRow->desc2) $html.= "Feltværdi 2: {$objRow->desc2}<br>";
				if ($objRow->desc3) $html.= "Feltværdi 3: {$objRow->desc3}<br>";
				if ($objRow->desc4) $html.= "Feltværdi 4: {$objRow->desc4}<br>";
				if ($objRow->desc5) $html.= "Feltværdi 5: {$objRow->desc5}<br>";
				
				 $html=nl2br($html);
				return parent::returnHtml($this->objField->name, $html);
			}
		} else {
			return parent::returnHtml($this->objField->name, "Feltnavn hvori fieldtypeID er mangler!");
		}

	}

	function saveData() {

    
		return "";
	}
	function getListHtml() {
		return "";
	}

} // END Class
?>
