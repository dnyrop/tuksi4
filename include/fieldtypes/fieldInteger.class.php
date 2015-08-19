<?php

/**
 * 
 * Integer
 * [FIELDVALUE3] = 
 *
 * @package tuksiFieldType
 */

class fieldInteger extends field {
	
	function fieldInteger($objField){
		parent::field($objField);
	}
	
	function getHTML(){
		
		$errormsg = "";
		$error = false;
		if(count($this->arrError) > 0) {
			$errormsg = join($this->arrError,'<br />');
			$error = true;	
		} 		
		
		
		$html = parent::getHtmlStart();
		
		$html .= tuksiFormElements::getInput(array(
													'name' => $this->objField->htmltagname,
													'value' => $this->objField->value,
													'disabled' => $this->objField->readonly,
													'error' => $error,
													'errormsg' => $errormsg
													
		));
		return parent::returnHtml($this->objField->name,$html);
	}

	function myIsInt ($x) {
  				  return (is_numeric($x) ? intval($x) == $x : false);
	}
	function saveData(){
		$objPage = tuksiBackend::getInstance();
		if ($this->objField->fieldvalue3) {
		// Setting char between every group of thousands.
		// Removing char
		$this->objField->value = str_replace($this->objField->fieldvalue3, "", $this->objField->value);
		}
			
		

		
		if ($this->myIsInt($this->objField->value)) { 
			$sql = $this->objField->colname . " = '" . $this->objField->value . "'";
		} else {
			$sql = "";
			$this->arrError[] = $objPage->cmstext('integer_not_valid');
		}
		
		return $sql;
	}
	
	function getListHtml() {
		$html =$this->objField->value;
		return $html;
	}
}

?>