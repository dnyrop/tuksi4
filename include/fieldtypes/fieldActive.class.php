<?php

/**
 * Checkbox input field
 *
 * Checkbox input field
 * Fieldvalues:
 * Fieldvalues:
 * None
 *
 * @package tuksiFieldType
 */
class fieldActive extends field{

	function __construct($objField) {
		parent::field($objField);
		
		$this->objField = $objField;
	}

	function getHTML() {
		
		
		$arrHtml = array();
		
		if ($this->objField->value) {
			$activeChecked = true;
			$hiddenChecked = false;
		} else {
			$activeChecked = false;
			$hiddenChecked = true;
		}
		
		$htmlActive = tuksiFormElements::getRadio(array('id' => "active_".$this->objField->htmltagname,
																										'name' => $this->objField->htmltagname,
																										'value' => 1,	
																										'checked' => $activeChecked));
		
		$htmlHidden = tuksiFormElements::getRadio(array('id' => "hidden_".$this->objField->htmltagname,
																										'name' => $this->objField->htmltagname,
																										'value' => 0,	
																										'checked' => $hiddenChecked));
		
		$html = '<label for="active_'.$this->objField->htmltagname.'" class="colorPositive">' . $htmlActive . 'Synlig</label>';
		$html.= '<label for="hidden_'.$this->objField->htmltagname.'" class="colorNegative">' . $htmlHidden . 'Skjult</label>';
		
		return parent::returnHtml($this->objField->name,$html);
	}

	function saveData() {
		if ($this->objField->value) 
			$sql= $this->objField->colname . " = '1'";
		else 
			$sql= $this->objField->colname . " = '0'";

		return $sql;
	}

	/**
	 * Used by lists to show simpel value from fieldtype.
	 *
	 * @return unknown
	 */
	function getListHtml() {
		
		$objPage = tuksiBackend::getInstance();
		
		if ($this->objField->value) {
			$this->objField->value = $objPage->cmstext('yes'); 
		} else {
			$this->objField->value = $objPage->cmstext('no');
		}
		$html = $this->objField->value . "&nbsp;";
		
		return $html;
	}

}

?>
