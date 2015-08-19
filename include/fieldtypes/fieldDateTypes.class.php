<?

/**
 * 
 * Checkbox input field
 * Fieldvalues:
 * None
 *
 * @package tuksiFieldType
 */

class fieldDateTypes extends field{

	function __construct($objField) {
		parent::field($objField);
	
	}

	function getHTML() {
		
		$HtmlTag = parent::getHtmlStart();
				
		list($year, $month, $day) = explode("-", $this->objField->value);	

		$currentyear	=	date("Y",time());
		$currentmonth	=	date("m",time());
		$currentday		=	date("d",time());
	
		if($this->objField->fieldvalue2 == 1){
			$day = $currentday;
			$month = $currentmonth;
			$year = $currentyear;
		}
	
		if (!is_numeric($this->objField->fieldvalue1)) {
			$this->objField->fieldvalue1 = 5;
		}
		
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_DAY\" class=\"forminput50\">"; 
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = 1;$t<32;$t++) {
			if ($t == $day) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";

		}
		$HtmlTag .= "</select> ";

		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_MONTH\" class=\"forminput50\">";
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = 1;$t<13;$t++) {
			if ($t == $month) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";
		}
		$HtmlTag .= "</select> ";

		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_YEAR\" class=\"forminput60\">";
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = $currentyear - $this->objField->fieldvalue1;$t<($currentyear+3);$t++) {
			if ($t == $year) { 
			$HtmlTag .= "<option selected value=\"$t\">$t</option>\n";
			} else {
				$HtmlTag .= "<option value=\"$t\">$t</option>\n";
			}	
		}
		
		
		$HtmlTag .= "</select>";
		return parent::returnHtml($this->objField->name,$HtmlTag);
		
	}
	
	function saveData() {
		
		$sql = $this->objField->colname . " = '{$_POST[$this->objField->htmltagname . "_YEAR"]}-{$_POST[$this->objField->htmltagname . "_MONTH"]}-{$_POST[$this->objField->htmltagname . "_DAY"]}'";
		return $sql;
	}


	function getListHtml() {
		
		$myTime = explode(" ", $this->objField->value);
		$myDate = explode("-", $myTime[0]);
		$html = $myDate[2] . "-". $myDate[1] ."-". $myDate[0];
		return $html;
	}
}

class fieldDatetime extends field{

	function fieldDatetime($objField) {
		parent::field($objField);
	
	}

	function getHTML() {
	
		$HtmlTag = parent::getHtmlStart();
	
		list($p1, $p2) = explode(" ", $this->objField->value);	
		list($year, $month, $day) = explode("-", $p1);	
		list($hour, $min, $sec) = explode(":", $p2);	
		$currentyear	=	date("Y",time());
		$currentmonth	=	date("m",time());
		$currentday		=	date("d",time());
		$currenthour = date("H",time());	
		$currentmin = date("i",time());	
		$currentsec = date("s",time());	
		
		if($this->objField->fieldvalue2 == 1){
			$day = $currentday;
			$month = $currentmonth;
			$year = $currentyear;
			$hour = $currenthour;
			$min = $currentmin;
			$sec = $currentsec;
		}
		
		
		if (!is_numeric($this->objField->fieldvalue1)) {
			$this->objField->Fieldvalue1 = 5;
		}

		
		$HtmlTag.= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_DAY\" class=\"forminput50\">"; 
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = 1;$t<33;$t++) {
			if ($t == $day) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";

		}
		$HtmlTag .= "</select>";

		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_MONTH\" class=\"forminput50\">";
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = 1;$t<13;$t++) {
			if ($t == $month) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_YEAR\" class=\"forminput60\">";
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = $currentyear - $this->objField->fieldvalue1;$t<($currentyear+3);$t++) {
			if ($t == $year) { 
			$HtmlTag .= "<option selected value=\"$t\">$t</option>\n";
			} else {
				$HtmlTag .= "<option value=\"$t\">$t</option>\n";
			}	
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_HOUR\" class=\"forminput50\">";
		for ($t = 0;$t<(25);$t++) {
			if ($t == $hour) { 
			$HtmlTag .= "<option selected value=\"$t\">$t</option>\n";
			} else {
				$HtmlTag .= "<option value=\"$t\">$t</option>\n";
			}	
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_MIN\" class=\"forminput50\">";
		for ($t = 0;$t<(61);$t++) {
			if ($t == $min) { 
			$HtmlTag .= "<option selected value=\"$t\">$t</option>\n";
			} else {
				$HtmlTag .= "<option value=\"$t\">$t</option>\n";
			}	
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_SEC\" class=\"forminput50\">";
		for ($t = 0;$t<(61);$t++) {
			if ($t == $sec) { 
			$HtmlTag .= "<option selected value=\"$t\">$t</option>\n";
			} else {
				$HtmlTag .= "<option value=\"$t\">$t</option>\n";
			}	
		}
		$HtmlTag .= "</select>";

		if ($this->objField->fieldvalue2) {
			// Now it cant be changed
			$ReturnHtml = " $day/$month $year $hour:$min:$sec";
		}
		return parent::returnHtml($this->objField->name,$HtmlTag);
		
	}
	
	function saveData(){
		
		$value = $_POST[$this->objField->htmltagname . "_YEAR"]. "-" . $_POST[$this->objField->htmltagname . "_MONTH"] . "-" . $_POST[$this->objField->htmltagname . "_DAY"]." " . $_POST[$this->objField->htmltagname . "_HOUR"]. ":".$_POST[$this->objField->htmltagname . "_MIN"].":" . $_POST[$this->objField->htmltagname . "_SEC"];
		$sql = $this->objField->colname . " = '{$value}'";
		return $sql;
	}
	function getListHtml() {
		
		$myTime = explode(" ", $this->objField->value);
		$myDate = explode("-", $myTime[0]);
		$html = $myDate[2] . "-". $myDate[1] ."-". $myDate[0]."&nbsp;" .$myTime[1] . "&nbsp;";
		
		return $html;
	}
}

class fieldTime extends field{

	function fieldTime($objField) {
		parent::field($objField);
	
	}

	function getHTML() {
	
		$HtmlTag = parent::getHtmlStart();
				
		list($hour, $min, $sec) = explode(":", $this->objField->value);	

		$currenthour=	date("h",time());
		$currentmin	=	date("i",time());
		$currentsec	=	date("s",time());

		if (!is_numeric($this->objField->fieldvalue1)) {
			$this->objField->fieldvalue1 = 5;
		}
		
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_HOUR\" class=\"forminput50\">"; 
		for ($t = 0;$t<=24;$t++) {
			if ($t == $hour) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";

		}
		$HtmlTag .= "</select>";

		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_MIN\" class=\"forminput50\">";
		for ($t = 0;$t<=60;$t++) {
			if ($t == $min) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";
		}
		$HtmlTag .= "</select>";

		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_SEC\" class=\"forminput50\">";
		$HtmlTag .= "<option value=\"0\">0</option>\n";
		for ($t = 0;$t<= 60;$t++) {
			if ($t == $sec) { 
			$HtmlTag .= "<option selected value=\"$t\">$t</option>\n";
			} else {
				$HtmlTag .= "<option value=\"$t\">$t</option>\n";
			}	
		}
		
		
		$HtmlTag .= "</select>";
		return parent::returnHtml($this->objField->name,$HtmlTag);
		
	}
	
	function saveData() {
		
		$sql = $this->objField->colname . " = '{$_POST[$this->objField->htmltagname . "_HOUR"]}:{$_POST[$this->objField->htmltagname . "_MIN"]}:{$_POST[$this->objField->htmltagname . "_SEC"]}'";
		return $sql;
	}


	function getListHtml() {
		
		list($hour, $min, $sec)= explode(":", $this->objField->value);
		$html = $hour . ":" . $min . ":" . $sec;
		return $html;
	}
}

class fieldDatetime_ts extends field{

	function fieldDatetime($objField) {
		parent::field($objField);
		
	}

	function getHTML() {
	
		$HtmlTag = parent::getHtmlStart();
	
		$ts = $this->objField->value;
		
		$year = substr($ts,0,4);
		$month = substr($ts,4,2);
		$day = substr($ts,6,2);
		$hour = substr($ts,8,2);
		$min =substr($ts,10,2);
		$sec = substr($ts,12,2);

		$currentyear	=	date("Y",time());
		$currentmonth	=	date("m",time());
		$currentday		=	date("d",time());
		$currenthour = date("H",time());	
		$currentmin = date("i",time());	
		$currentsec = date("s",time());	
		
		if($this->objField->fieldvalue2 == 1){
			$day = $currentday;
			$month = $currentmonth;
			$year = $currentyear;
			$hour = $currenthour;
			$min = $currentmin;
			$sec = $currentsec;
		}
		
		
		if (!is_numeric($this->objField->fieldvalue1)) {
			$this->objField->Fieldvalue1 = 5;
		}

		
		$HtmlTag.= "<select onchange=\"javascript:changed = 1;\" name=\"".$this->objField->htmltagname."_DAY\" class=\"forminput50\">"; 
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = 1;$t<33;$t++) {
			if ($t == $day) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";

		}
		$HtmlTag .= "</select>";

		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_MONTH\" class=\"forminput50\">";
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = 1;$t<13;$t++) {
			
			if ($t == $month) {
				$select = " SELECTED ";
			}  
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
				$HtmlTag .= "<option $select value=\"$tStr\">$tStr</option>\n";
				$select = "";
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_YEAR\" class=\"forminput60\">";
		$HtmlTag .= "<option value=\"0\">-</option>\n";
		for ($t = $currentyear - $this->objField->fieldvalue1;$t<($currentyear+3);$t++) {
			if ($t == $year) $sel = "SELECTED";
			
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}

			$HtmlTag .= "<option $sel value=\"$tStr\">$tStr</option>\n";
			$sel = "";
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_HOUR\" class=\"forminput50\">";
		for ($t = 0;$t<25;$t++) {
			
			if($t == $hour) $sel = "SELECTED";
			
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$HtmlTag .= "<option $sel value=\"$tStr\">$tStr</option>\n";
			$sel = "";
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_MIN\" class=\"forminput50\">";
		for ($t = 0;$t<(61);$t++) {
			if ($t == $min) $sel = "SELECTED";
			
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$HtmlTag .= "<option $sel value=\"$tStr\">$tStr</option>\n";
			$sel = "";
		}
		$HtmlTag .= "</select>";
		$HtmlTag .= "<select onchange=\"javascript:changed = 1;\"  name=\"".$this->objField->htmltagname."_SEC\" class=\"forminput50\">";
		for ($t = 0;$t<(61);$t++) {
			if ($t == $sec) $sel = "SELECTED";
			
			$tStr = $t;
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$HtmlTag .= "<option $sel value=\"$tStr\">$tStr</option>\n";
			$sel = "";
		}
		$HtmlTag .= "</select>";

		if ($this->objField->fieldvalue2) {
			// Now it cant be changed
			$ReturnHtml = " $day/$month $year $hour:$min:$sec";
		}
		return parent::returnHtml($this->objField->name,$HtmlTag);
		
	}
	
	function saveData(){
		
		$value = $_POST[$this->objField->htmltagname . "_YEAR"].$_POST[$this->objField->htmltagname . "_MONTH"].$_POST[$this->objField->htmltagname . "_DAY"].$_POST[$this->objField->htmltagname . "_HOUR"].$_POST[$this->objField->htmltagname . "_MIN"].$_POST[$this->objField->htmltagname . "_SEC"];
		$sql = $this->objField->colname . " = '{$value}'";
		return $sql;
	}
	function getListHtml() {
		
		$myTime = explode(" ", $this->objField->value);
		$myDate = explode("-", $myTime[0]);
		$html = $myDate[2] . "-". $myDate[1] ."-". $myDate[0]."&nbsp;" .$myTime[1] . "&nbsp;";
		
		return $html;
	}
}
?>
