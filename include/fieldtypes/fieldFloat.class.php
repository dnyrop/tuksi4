<?php

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldFloat extends field {
	
	function fieldFloat($objField){
		parent::field($objField);
		
	}
	
	function getHTML(){
		
		if ($this->objField->fieldvalue3) {
			// Setting char between every group of thousands. 
		$this->objField->value = str_replace('.',$this->objField->fieldvalue3, $this->objField->value);
		}

		$html = parent::getHtmlStart();
		
		$html.= tuksiFormElements::getInput(array(	
											'type' => 'text',
											'name' => $this->objField->htmltagname,
											'value' => $this->objField->value,
											'disabled' => $this->objField->readonly));
		
		return parent::returnHtml($this->objField->name,$html);
	}

	function saveData(){		
		if ($this->objField->fieldvalue3) {
		// Setting char between every group of thousands.
		// Removing char
		$this->objField->value = str_replace(",",".", $this->objField->value);
		}
		if (is_numeric($this->objField->value) || $this->objField->value == '') { 
			$sql = $this->objField->colname . " = '" . $this->objField->value . "'";
		} else {
			$sql = "";
			$GLOBALS['error'][$this->objField->vcolname] = "Værdi skal være et tal {..., -2.32, -1.23, 0, 1, 2.6543, ...}. ";
		}
		
		return $sql;
	}
	
	function getListHtml() {
		$html = str_replace(".", ",", $this->objField->value) ;
		return $html;
	}
}
?>