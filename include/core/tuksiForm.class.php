<?php

/**
 * Enter description here...
 *
 * @todo php doc
 * @todo Test class
 * @package tuksiBase
 */
class tuksiForm {

	var $formtoken;
	var $arrError = array();
	var $boolOk = 1;
	// inputbox = 1, checkbox = 2, selectbox = 3.
	var $formtype = array();	
	
	function __construct($formToken = "FORM", $errorToken = "ERROR",$dynmamicVar = "") {
		
		$this->formtoken = $formToken;
		$this->dynmamicVar = $dynmamicVar;
		$this->errortoken = $errorToken;

		$this->validate = new tuksiValidate();
		
	}

	function ok() {
		return $this->boolOk;
	}

	function validate($valuetype, $formname, $errortext, $value1 = "", $value2 = "") {
			
		if(!empty($this->dynmamicVar))
			$value = $_POST->getStr($this->formtoken . "_" . $formname . "_" . $this->dynmamicVar);
		else
			$value = $_POST->getStr($this->formtoken . "_" . $formname);
			
		switch ($valuetype) {
			case('int') 	: $return = $this->validate->isInteger($value, $value1, $value2); break;
			case('float') 	: $return = tuksi_validate_isFloat($value, $value1, $value2); break;
			case('string') : $return = $this->validate->isString($value, $value1, $value2); break;
			case('html') 	: $return = tuksi_validate_isHtml($value, $value1); break;
			case('email') 	: $return = $this->validate->isEmail($value); break;
			case('date') 	: $return = $this->validate->isDate($value, $value1); break;
			case('date2') 	: $return = $this->validate->isDate2($value, $value1); break;
			case('time') 	: $return = tuksi_validate_isTime($value, $value1); break;
			case('datetime'): $return = tuksi_validate_isDatetime($value, $value1); break;
			case('phone')	: $return = $this->validate->isPhone($value, $value1, $value2); break;
			case('reg') 	: $return = tuksi_validate_isReg($value, $value1); break;
			default			: $return = 0;
		}

		if ($return) 
			return 1;
		else
			$this->addError($errortext, $formname);
		
	}

	function addError($errortext = "", $formname = "") {
		$this->boolOk = 0;
		if ($errortext) {
			if ($formname)
				$this->arrError[$formname] = $errortext;
			else
				$this->arrError[] = $errortext;
		}
	}
	function printErrors($delimiter = "<br>") {
		if (is_array($this->arrError)) 
			return join($delimiter, $this->arrError);
		else
			return "";
	}
	function getFormValues() {
		$arrTokens = array();
		$postData = $_POST->getData();
		foreach ($postData as $key => $value) {
			if (preg_match("/^{$this->formtoken}_/", $key, $m)) {
				$formname = str_replace($this->formtoken . "_", "", $key);
				if (!isset($this->formtype[$formname]))
					$this->formtype[$formname] = 1;
				
					
				if(!empty($this->dynmamicVar)) {
					$key = preg_replace("/\_".$this->dynmamicVar."$/","",$key);
				}	
				$arrTokens[$key] = array("value" => stripslashes($value), "formtype" => $this->formtype[$formname]);
			}
		}
		return $arrTokens;
	}

	function getFormValuesFromSession($sessionvar = "") {
		$arrTokens = array();
	
		if ($sessionvar)
			$session = $_SESSION[$sessionvar];
		else
			$session = $_SESSION;
		if (is_array($session))
		foreach ($session as $key => $value) { 
			if (preg_match("/^{$this->formtoken}_/", $key, $m)) { 
				$formname = str_replace($this->formtoken . "_", "", $key);
				if (!isset($this->formtype[$formname]))
					$this->formtype[$formname] = 1;
					$arrTokens[$key] = array("value" => $value['value'], "formtype" => $this->formtype[$formname]);
			}
		}
		
		return $arrTokens;
	}

	function getFormErrors() {
		$arrTokens = array();
		if(count($this->arrError))
		foreach ($this->arrError as $key => $value) 
			$arrTokens[$this->errortoken . "_" . strtolower($key)] = $value;
		
		return $arrTokens;
	}
	
	// * Updates templates with values from error values 
	function updateFormErrors(&$tpl) {
		
		$arrValues = $this->getFormErrors();
		foreach ($arrValues as $key => $value) {
			if (preg_match("/^{$this->errortoken}_/i", $key, $m)) {
				$tpl->assign($key, $value);
			}
		}
	}
	
	// * Updates templates with values from $_POST
	function updateForm(&$tpl) {
		
		$arrValues = $this->getFormValues();
		foreach ($arrValues as $key => $value) {
			if (preg_match("/^{$this->formtoken}_/", $key, $m)) {
				$tpl->assign($key, $this->transformValue($value));
			}
		}
	}
	// * Updates templates with values from $_POST
	function updateFormDB(&$tpl, $objDB) {

		foreach ($objDB as $key => $value) {
			$tpl->addtoken(strtolower($this->formtoken . "_" . $key), $objDB->{$key});
		}
	}


	function defineCheckbox($formname) {
		$this->formtype[$formname] = 2;	
	}
	function updateSession() {
	}
	function deleteSession() {
	}

	function transformValue($arrValue) {

		switch ($arrValue['formtype']) {
			case(1) : return $arrValue['value']; break;
			case(2) : if ($arrValue['value'])
									return " CHECKED ";
								else
									return "";
								break;
			default : return $arrValue['value'];
		}
	
	}

	
}

?>
