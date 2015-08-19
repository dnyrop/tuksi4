<?php

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldDateTime extends field{

	function __construct($objField) {
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
		
		$arrFormat = $this->objField->fieldvalue3;
		for($i = 0;$i < strlen($arrFormat);$i++) {
			switch ($arrFormat[$i]) {
				case 'y':
					$HtmlTag.= $this->makeYearSelect($year);
					break;
				case 'm':
					$HtmlTag.= $this->makeMonthSelect($month);
					break;
				case 'd':
					$HtmlTag.= $this->makeDaySelect($day);
					break;
				case 'h':
					$HtmlTag.= $this->makeHourSelect($hour);
					break;
				case 'I':
					$HtmlTag.= $this->makeMinuteSelect($min);
					break;				
				case 's':
					$HtmlTag.= $this->makeSecSelect($sec);
					break;		
				default:
					break;
			}
			
		}
		
		
		if (!is_numeric($this->objField->fieldvalue1)) {
			$this->objField->Fieldvalue1 = 5;
		} 

		if ($this->objField->fieldvalue2) {
			// Now it cant be changed
			$ReturnHtml = " $day/$month $year $hour:$min:$sec";
		}
		return parent::returnHtml($this->objField->name,$HtmlTag);
	}
	
	function saveData(){
		
		if($this->objField->readonly) {
			return "";
		} 
		
		$sql = "";
		
		$currentyear	=	date("Y",time());
		$currentmonth	=	date("m",time());
		$currentday		=	date("d",time());
		$currenthour = date("H",time());	
		$currentmin = date("i",time());	
		$currentsec = date("s",time());	
		
		
		$arrDates = array(	'year' => $_POST->getInt($this->objField->htmltagname . "_YEAR"),
							'month' => $_POST->getInt($this->objField->htmltagname . "_MONTH"),
							'day' => $_POST->getInt($this->objField->htmltagname . "_DAY"),
							'hour' => $_POST->getInt($this->objField->htmltagname . "_HOUR"),
							'min' =>  $_POST->getInt($this->objField->htmltagname . "_MIN"),
							'sec' => $_POST->getInt($this->objField->htmltagname . "_SEC"));
							
		foreach ($arrDates as $key => &$date) {
			if(!$date) {
				$date = ${'current'.$key};
			}
		}

		$value = $arrDates['year']. "-" . $arrDates['month'] . "-" . $arrDates['day']." " . $arrDates['hour']. ":".$arrDates['min'].":" . $arrDates['sec'];
		$sql = $this->objField->colname . " = '{$value}'";
		
		return $sql;
	}
	function getListHtml() {
		
		$myTime = explode(" ", $this->objField->value);
		$myDate = explode("-", $myTime[0]);
		$html = $myDate[2] . "-". $myDate[1] ."-". $myDate[0]."&nbsp;" .$myTime[1] . "&nbsp;";
		
		return $html;
	}
	
	
	function makeYearSelect($year){
		
		$currentyear = date("Y", time());
		
		$arrYears = array();
		for($i = intval($this->objField->fieldvalue1); $i <= $currentyear; $i++) {
			$arrYears[] = array('value' => $i, 'name' => $i);
		}

		return tuksiFormElements::getSelect(
		
			array(
				
				'id' => $this->objField->htmltagname."_YEAR",
				'options' => $arrYears,
				'selected' => $year,
				'disabled' => $this->objField->readonly,
				'width' => 60
			)
		);
	}
	
	function makeMonthSelect($month){
		
		$currentyear = date("Y",time());
		
		$arrMonths = array();
		
		for ($t = 1;$t<13;$t++) {
			
			$tStr = $t;
			
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$arrMonths[] = array(	'value' => $tStr,
									'name' => $tStr);
		}
		
		return tuksiFormElements::getSelect(array(	'id' => $this->objField->htmltagname."_MONTH",
													'options' => $arrMonths,
													'selected' => $month,
													'disabled' => $this->objField->readonly));
	}
	function makeDaySelect($day){
		
		
		$arrDays = array();
		
		for ($t = 1;$t<32;$t++) {
			
			$tStr = $t;
			
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$arrDays[] = array(	'value' => $tStr,
									'name' => $tStr);
		}
		
		return tuksiFormElements::getSelect(array(	'id' => $this->objField->htmltagname."_DAY",
													'options' => $arrDays,
													'selected' => $day,
													'disabled' => $this->objField->readonly));
	}
	
	function makeHourSelect($hour){
		
		$arrHours = array();
		
		for ($t = 1;$t<25;$t++) {
			
			$tStr = $t;
			
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$arrHours[] = array(	'value' => $tStr,
									'name' => $tStr);
		}
		
		return tuksiFormElements::getSelect(array(	'id' => $this->objField->htmltagname."_HOUR",
													'options' => $arrHours,
													'selected' => $hour,
													'disabled' => $this->objField->readonly));
	}

	function makeMinuteSelect($minute){
		
		
		$arrMinutes = array();
		
		for ($t = 1;$t<61;$t++) {
			
			$tStr = $t;
			
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$arrMinutes[] = array(	'value' => $tStr,
									'name' => $tStr);
		}
		
		return tuksiFormElements::getSelect(array(	'id' => $this->objField->htmltagname."_MIN",
													'options' => $arrMinutes,
													'selected' => $minute,
													'disabled' => $this->objField->readonly));
	}

	function makeSecSelect($second){
		
		
		$arrSeconds = array();
		
		for ($t = 1;$t<61;$t++) {
			
			$tStr = $t;
			
			if ($t < 10) { $tStr = "0" . $tStr;}
			
			$arrSeconds[] = array(	'value' => $tStr,
									'name' => $tStr);
		}
		
		return tuksiFormElements::getSelect(array(	'id' => $this->objField->htmltagname."_SEC",
													'options' => $arrSeconds,
													'selected' => $second,
													'disabled' => $this->objField->readonly));
	}
}

?>
