<?
/**
 * @package tuksiFieldType
 */
class fieldEnumSelect extends field {

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {
		global $TUKSI;

		if (!$this->objField->fieldvalue3)
			$this->objField->fieldvalue3 = "200";

		$objDB = tuksiDB::getInstance();
		
		$arrReturn = $objDB->getFields($this->objField->tablename, $this->objField->colname);
		
		if ($arrReturn['num_rows'] > 0){
			
		  $options=explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2",$arrReturn['data'][0]['Type']));
		  
		  $HtmlTag = '<select class="forminput' . $this->objField->fieldvalue3 . '" onchange="javascript:changed = 1;" NAME="' . $this->objField->htmltagname .'">';
		  
		  if($this->objField->fieldvalue2)
		    $HtmlTag .= "<option value=\"0\">{$this->objField->fieldvalue2}</option>";
		    
		  $Max = count($options) or $TUKSI->debug(mysql_error());
		  $No = 0;
		  foreach($options as $value){
		    if ($value == $this->objField->value) 
		      $HtmlTag .= "<option selected=\"selected\" value=\"$value\">$value</option>";
		    else 
		      $HtmlTag .= "<option value=\"$value\">$value</option>";
		  } // end while 
		  
		  $HtmlTag .= "</select><br><br>";
		}
		else {
		  $HtmlTag = " SQL string is invalid.";	
		} 
		
		$Html  = parent::getHtmlStart();
		$Html .= $HtmlTag;
		return parent::returnHtml($this->objField->name, $Html);
	
	}// getHTML
	
	function saveData() {
		global $TUKSI;
		
		if ($this->objField->value)
			$ReturnSQL = $this->objField->colname . " = '" . mysql_escape_string ($this->objField->value). "'";
    
		return $ReturnSQL;
	}
	function getListHtml() {
		return $this->objField->value;
	}

} // END Class
?>
