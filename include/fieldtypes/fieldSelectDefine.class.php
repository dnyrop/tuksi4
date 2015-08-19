<?php

/**
 * 
 * Select define
 * [FIELDVALUE1] = , som seperator
 * [FIELDVALUE2] = 1=Giver ID forløbende numre (default).  2=Splitter værdierne (value1=tekst1) med "=".  3=Værdierne sættes som ID.
 *
 *@package tuksiFieldType
 */


class fieldSelectDefine extends field{

	function fieldSelectDefine($objField){
		parent::field($objField);
		
	}
	
	function getHTML() {
		
		if (!$this->objField->fieldvalue3)
			$this->objField->fieldvalue3 = 1;
		 
		$HtmlTag = '<SELECT CLASS="forminput200" onchange="javascript:changed = 1;" NAME="' . $this->objField->htmltagname .'">';
		
		if($this->objField->fieldvalue2)
		  $HtmlTag .= "<OPTION VALUE=\"0\">{$this->objField->fieldvalue2}</OPTION>";
		
		if(is_array($this->objField->fieldvalue1)) {
			
			foreach ($this->objField->fieldvalue1 as $arrOption) {
				
				if ($arrOption['value'] == $this->objField->value) 
						$HtmlTag .= "<OPTION SELECTED VALUE=\"".$arrOption['value']."\">".$arrOption['name']."</OPTION>";
			 	else 
					$HtmlTag .= "<OPTION VALUE=\"".$arrOption['value']."\">".$arrOption['name']."</OPTION>";
			
			}
		} else {

			$arrValues = explode(",",$this->objField->fieldvalue1);
			if(is_array($arrValues)){
				$idnumber = 1;
				foreach($arrValues as $name){
					
					if ($this->objField->fieldvalue3 == 1)
						$id = $idnumber;
	
					if ($this->objField->fieldvalue3 == 2) {
						list($value, $text) = explode("=", $name);
						$id = $value;
						$name = $text;
					}
	
					if ($this->objField->fieldvalue3 == 3)
						$id = $name;
					
					if ($id == $this->objField->value) 
						$HtmlTag .= "<OPTION SELECTED VALUE=\"$id\">$name</OPTION>";
				 	else 
						$HtmlTag .= "<OPTION VALUE=\"$id\">$name</OPTION>";
					
					$idnumber++;	
				} // end foreach 
			}
		}
		$HtmlTag .= "</SELECT>";
			
		$Html  = parent::getHtmlStart();
		$Html .= $HtmlTag;
		return parent::returnHtml($this->objField->name,$Html);
		
	}

	function saveData()	{
		$sql = $this->objField->colname . " = '" . $this->objField->value . "'";
		return $sql;
	}
	
	function getListHtml() {
	
		if (!$this->objField->fieldvalue3)
			$this->objField->fieldvalue3 = 1;
		 
		$arrValues = explode(",",$this->objField->fieldvalue1);

		if(is_array($arrValues)){
			$idnumber = 1;
			foreach($arrValues as $name){
				
				if ($this->objField->fieldvalue3 == 1)
					$id = $idnumber;

				if ($this->objField->fieldvalue3 == 2) {
					list($value, $text) = explode("=", $name);
					$id = $value;
					$name = $text;
				}

				if ($this->objField->fieldvalue3 == 3)
					$id = $name;
				
				if ($id == $this->objField->value) {
					return $name;
				}
				$idnumber++;	
			} // end foreach 
		}
	}
}

?>
