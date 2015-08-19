<?php


/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldTextarea extends field {
	
	function fieldTextarea ($objField){
		parent::field($objField);
		$this->objField = $objField;
	}
	
	function getHTML(){
	
		$HEIGHT = strlen($this->objField->value)/4 + 90;

		if ($HEIGHT < 100) 
			$HEIGHT = 100;

		$HtmlTag  = parent::getHtmlStart();
		
		$disabled = (isset($this->objField->readonly) && $this->objField->readonly)  ? 'disabled' : '';	
		
		$HtmlTag .= '<textarea '.$disabled.' style="height: ' . $HEIGHT . 'px" onchange="javascript:changed = 1;" class="formtextareamax" name="#HTMLTAGNAME#" cols="40" rows="8">#VALUE#</textarea>';
		$HtmlTag = str_replace("#HTMLTAGNAME#", $this->objField->htmltagname, $HtmlTag);
		$HtmlTag = str_replace("#VALUE#", $this->objField->value, $HtmlTag);
			
		return parent::returnHtml($this->objField->name,$HtmlTag);	
	
	}
	
	function saveData() {
		$objDB = tuksiDB::getInstance();
		
		$sql = $this->objField->colname . " = '" . $objDB->escapeString($this->objField->value) . "'";
		return $sql;
		
	}
	function getListHtml() {
		if ($this->objField->fieldvalue3) {
		// Ok. This is plaintext. do newlines to br
			$this->objField->value = nl2br($this->objField->value);
		}
		$html = $this->objField->value;
		
		return $html;
	}
}

?>
