<?

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldTextLabel extends field {

	function fieldTextLabel($objField) {
		parent::field($objField);
	}

	function getHTML() {
		
		$HtmlTag = parent::getHtmlStart();
		
		$HtmlTag.=$this->objField->fieldvalue1;
	
		return parent::returnHtml($this->objField->name, $HtmlTag);
	}

	function saveData() {
		
		
		return "";
				    
	}
	function deleteData() {
		
		
		return "";
	}
	function getListHtml() {
		
	
		return $this->objField->fieldvalue1;	
	}

} // END Class
?>
