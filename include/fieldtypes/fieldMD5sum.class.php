<?

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldMd5sum extends field {

	function fieldMd5sum($objField) {
		
		parent::field($objField);
	}

	function getHTML() {
		if (!$this->objField->fieldvalue1) {
			$this->objField->fieldvalue1 = "200";
		}
		
		$errormsg = "";
		$error = false;
		if(count($this->arrError) > 0) {
			$errormsg = join($this->arrError,'<br />');
			$error = true;	
		} 		

		$HtmlTag = tuksiFormElements::getInput(array(
													'type' => 'password',
													'name' => $this->objField->htmltagname,											
													'disabled' => $this->objField->readonly,
													'maxlength' => $this->objField->fieldvalue2,
													'errormsg' => $errormsg,
													'error' => $error));
													
		$Html  = parent::getHtmlStart();
		$Html .= $HtmlTag;
		return parent::returnHtml($this->objField->name, $Html);
	
	}// getHTML
	
	function saveData() {
		$objPage = tuksiBackend::getInstance();
		$bookOk = 1;
		
		if(!$this->objField->value)
		  $bookOk = 0;
		else{
		// " not allowed in input field
  		$this->objField->value = str_replace("\"", "", $this->objField->value);

		// Append secret key -string
		if($this->objField->fieldvalue1){
		  $this->objField->value = $this->objField->fieldvalue1 . $this->objField->value;
		}
		
		// Check input length
  		if ($this->objField->fieldvalue2 && strlen($this->objField->value) > ($this->objField->fieldvalue2+strlen($this->objField->fieldvalue1))) {
		  $this->arrError[] = $objPage->cmstext('text_exeeded');
		  $bookOk = 0;
		}

		// Validate string using supplied regex - if any
  		if ($this->objField->fieldvalue3 && !preg_match($this->objField->fieldvalue3,$this->objField->value)) {
		  $this->arrError[] = $objPage->cmstext('invalid_chars');
		  $bookOk = 0;
		}

		// No HTML tags
		$this->objField->value = strip_tags($this->objField->value);
		if($bookOk)
		  $sql = $this->objField->colname . " = '" . $this->objField->value . "'";
		else
		  $sql = "";

		}
		if($bookOk)
		  $ReturnSQL = $this->objField->colname . " = '" . md5($this->objField->value). "'";

		//		echo "String: ".mysql_escape_string($this->objField->value) . " MD5: " . md5($this->objField->value);
		return $ReturnSQL;
	}
	function getListHtml() {
		return $this->objField->value;
	}

} // END Class
?>
