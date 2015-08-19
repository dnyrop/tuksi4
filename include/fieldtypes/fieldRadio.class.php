<?
/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldRadio extends field{

	function fieldRadio($objField) {
		parent::field($objField);
	
	}

	function getHTML() {
		$objDB = tuksiDB::getInstance();
		
		if($this->objField->fieldvalue2) {
			
			$Html  = parent::getHtmlStart();
			$Html .="<br>";
			$rs= $objDB->fetch($this->objField->fieldvalue2, array("type" => "object"));
			foreach($rs['data'] as $row){
				
				$checked="";
				if($this->objField->value == $row->id){
					$checked=" CHECKED ";
				}
				
				$Html .= tuksiFormElements::getRadio(array(	
											'type' => 'radio',
											'id' => $this->objField->htmltagname,
											'value' => $row->id,
											'checked' => $checked,
											'disabled' => $this->objField->readonly)) . $row->name.'<br /><br />';			
		}	
		}else{
			$arrRadios = array();
			$arrRadios = explode(";",$this->objField->fieldvalue1);
			$Html  = parent::getHtmlStart();
			foreach($arrRadios as $radios) {

				list($value,$desc) = explode(":",$radios);
				if($this->objField->value == $value) {
					$checked = true;
				}
				else {
					$checked = false;
				}
				$Html .= tuksiFormElements::getRadio(array(	
											'type' => 'radio',
											'id' => $this->objField->htmltagname,
											'value' => $value,
											'checked' => $checked,
											'disabled' => $this->objField->readonly)) . $desc . '<br /><br />';	
			}

			// Returning html
		}
		return parent::returnHtml($this->objField->name,$Html);
	}

	function saveData() {
	
		$sql= $this->objField->colname . " = '".$this->objField->value."'";
		
		return $sql;
	}

	function getListHtml() {
		global $TUKSI;
	
		if(!$this->objField->fieldvalue2) {
			list($val1,$val2)=explode(";",$this->objField->fieldvalue1);		
			if ($this->objField->value) {
							$this->objField->value = $val2; 
						} else {
							$this->objField->value = $val1;
						}
			$html = $this->objField->value . "&nbsp;";
		}
		else{
			$html = $this->objField->value . "&nbsp;";
		}
		return $html;
	}

}
?>