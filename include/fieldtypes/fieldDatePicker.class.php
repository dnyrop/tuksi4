<?
/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldDatePicker extends field {

	function __construct($objField) {
		parent::field($objField);
	
	}

	function getHTML() {
		
		$objPage = tuksiBackend::getInstance();
		
		$tpl = new tuksiSmarty();
		
		$arrOptions = array('usehour' => 0,
												'usetime' => 0,
												'id' => $this->objField->id,
												'value' => '',
												'htmltagname' => $this->objField->htmltagname);
		
		
		if($this->objField->value) {
			$ts = strtotime($this->objField->value);
			if($this->objField->fieldvalue1) {
				$arrOptions['value'] = date('d-m-Y H:i',$ts);
			} else if($this->objField->fieldvalue2) {
				$arrOptions['value'] = date('d-m-Y H',$ts);
			} else {
				$arrOptions['value'] = date('d-m-Y',$ts);	
			}
			
		} 
		if($this->objField->fieldvalue1) {
			$arrOptions['usetime'] = "1";
		} elseif($this->objField->fieldvalue2) {
			$arrOptions['usehour'] = "1";
		} 
		
		$tpl->assign('options',$arrOptions);
		
		
		return parent::returnHtml($this->objField->name,$tpl->fetch('fieldtypes/fieldDatepicker.tpl'));
	}

	function saveData() {
		
		$objPage = tuksiBackend::getInstance();
		
		$sql = '';
		$ok = false; 
		if($this->objField->fieldvalue1) {
			//11-1-2007 10:07
			if(preg_match("/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])\ ([0-9]|[0-1][0-9]|2[0-3]):([1-9]|[0-5][0-9])$/",$this->objField->value,$match)) {
				$d = (int) $match[1];
				$m = (int) $match[2];
				$y = (int) $match[3];
				$h = (int) $match[4];
				$min = (int) $match[5];
				$ok = true;
			} else {
				$objPage->alert('Date format needs to be dd-mm-yyyy tt:mm '. $this->objField->value);
			}
		} else if($this->objField->fieldvalue2) { 
			//11-1-2007 10:07
			if(preg_match("/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])\ ([0-9]|[0-1][0-9]|2[0-3])$/",$this->objField->value,$match)) {
				$d = (int) $match[1];
				$m = (int) $match[2];
				$y = (int) $match[3];
				$h = (int) $match[4];
				$ok = true;
			} else {
				$objPage->alert('Date format needs to be dd-mm-yyyy tt '. $this->objField->value);
			}
		} else {
			if(preg_match("/^([1-9]|0[1-9]|[12][0-9]|3[01])\-([1-9]|0[1-9]|1[012])\-(19[0-9][0-9]|20[0-9][0-9])$/",$this->objField->value,$match)) {
				$d = (int) $match[1];
				$m = (int) $match[2];
				$y = (int) $match[3];
				$ok = true;
			}else {
				if (!$this->objField->fieldvalue3) {
					$objPage->alert('Date format needs to be dd-mm-yyyy ' . $this->objField->value);
				}
			}
		}
		
		if($ok) {
			if (!checkdate($m,$d,$y)) {
				$objPage->alert("The entered date does not exist: $d.$m.$y");
			} else {
				if($this->objField->fieldvalue1) {
					$value = "$y-$m-$d $h:$min";	
				} else if($this->objField->fieldvalue2) {
					$value = "$y-$m-$d $h:00";	
				} else {
					$value = "$y-$m-$d 00:00";	
				}
				$sql= $this->objField->colname . " = '$value'";
			}
		} else {
			if ($this->objField->fieldvalue3) {
				$sql = $this->objField->colname . " = NULL ";
			}
		}
		
		return $sql;
	}

	function getListHtml() {
		global $TUKSI;
	
		return $this->objField->value;
	}

}
?>
