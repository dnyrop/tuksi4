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
class fieldCheckbox extends field{

	function fieldCheckbox($objField) {
		parent::field($objField);
		$this->objField = $objField;
	}

	function getHTML() {
		// Returning html
		$disabled = isset($this->objField->readonly) ? $this->objField->readonly : false;
		
		$Html  = parent::getHtmlStart();
		$Html.= tuksiFormElements::getCheckBox(array(	'id' => $this->objField->htmltagname,
														'checked' => $this->objField->value,
														'disabled' => $disabled));
		return parent::returnHtml($this->objField->name,$Html);
	}

	function saveData() {
		if ($this->objField->value)
			$sql= $this->objField->colname . " = '1'";
		else
			$sql= $this->objField->colname . " = '0'";

		return $sql;
	}

	function getListHtml() {

		$objPage = tuksiBackend::getInstance();

		$html = "&nbsp;";
		if ($this->objField->value) {
			$html = $objPage->cmstext('yes') . $html; 
		} else {
			$html = $objPage->cmstext('no') . $html;
		}

		return $html;
	}

}
?>
