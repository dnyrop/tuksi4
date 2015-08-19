<?php

/**
 * Text Input field
 * Fieldvalues:
 * [FIELDVALUE1] = Width of input box (default = 200px),
 * [FIELDVALUE2] = Max lenght of text [chars] 
 * [FIELDVALUE3] = 1 -> password field 
 * [FIELDVALUE3] = 2 -> Read only InputField
 * [FIELDVALUE3] = 3 -> Email field
 *
 * @package tuksiFieldType
 */
class fieldTextInput extends field {

	function __construct($objField){
		parent::field($objField);
	}
	
	function getHTML() {
		
		$arrOptions = array('id' => $this->objField->htmltagname);
		
		$arrOptions['width'] = $this->objField->fieldvalue1;
		
		if ($this->objField->fieldvalue2) {
			$arrOptions['maxlength'] = $this->objField->fieldvalue2;
		}
		
		$errormsg = "";
		$error = false;
		if(count($this->arrError) > 0) {
			$errormsg = join($this->arrError,'<br />');
			$error = true;	
		} 		
		switch ($this->objField->fieldvalue3) {
			case (1) : // This is a password input field
				$arrOptions['type'] = 'password';
				break;
			case (2) : // This is a READ-ONLY input field
				$arrOptions['type'] = "text";
				$arrOptions['disabled'] = true;
				break;
			case (3) : //This is a Email Field
				$arrOptions['type'] = "text";
				$arrOptions['error'] = $error;
				$arrOptions['errormsg'] = $errormsg;
				break;
			default  : // this is a standard input field
				$arrOptions['type'] = "text";
		}
		
		if (isset($this->objField->readonly)) {
			$arrOptions['disabled'] |= $this->objField->readonly;
		}
		
		$value = str_replace('"','&quot;',$this->objField->value);
	
		$arrOptions['value'] = $value;
		
		$strHtml = parent::getHtmlStart();
		$strHtml.= tuksiFormElements::getInput($arrOptions);
		
		return parent::returnHtml($this->objField->name,$strHtml);
	}

	function saveData() {
		$bookOk = 1;
		$objPage = tuksiBackend::getInstance();
		$this->objField->value = str_replace('a&#778;', '&aring;', $this->objField->value);
		$this->objField->value = str_replace('A&#778;', '&Aring;', $this->objField->value);
		if ($this->objField->fieldvalue2 && strlen($this->objField->value) > $this->objField->fieldvalue2) {
			$this->arrError[] = $objPage->cmstext('text_exeeded') . "({$this->objField->fieldvalue2})";
			$bookOk = 0;
		}
		if($this->objField->fieldvalue3 == 2) {
			$bookOk = 0;
		}
		if($this->objField->fieldvalue3 == 3) {
			if (preg_match('/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/i', $this->objField->value)) {
				$bookOk = 1;
			} else {
				$this->arrError[] = $objPage->cmstext('email_not_valid') . "({$this->objField->value})";
				$bookOk = 0;
			}
		}
		if($this->objField->fieldvalue3 == 4) {
			if (preg_match('/^#?[a-f0-9]{6}$/i', $this->objField->value) || empty($this->objField->value)) {
				if (strlen($this->objField->value) == 6) {
					$this->objField->value = '#' . $this->objField->value;
				}
				$this->objField->value = strtoupper($this->objField->value);
				$bookOk = 1;
			} else {
				$this->arrError[] = $objPage->cmstext('invalid_chars') . "({$this->objField->value})";
				$bookOk = 0;
			}
		}
		
		// Default chars allowed  
		$this->objField->value = strip_tags($this->objField->value);
		$objDB = tuksiDB::getInstance();
		
		if ($bookOk) {
			$sql = $this->objField->colname . " = '" . $objDB->escapeString($this->objField->value) . "'";
		} else {
			$sql = "";
		}
		return $sql;
	}
	function getListHtml() {
		return $this->objField->value;
	}
}
?>
