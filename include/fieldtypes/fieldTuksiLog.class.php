<?php

class fieldTuksiLog extends field{

	function __construct($objField) {
		parent::field($objField);
		
		$this->objField = $objField;
	}

	function getHTML() {
		
		$arrLog = tuksiLog::getLogForPage($this->objField->rowid);
		
		$tpl = new tuksiSmarty();
		
		$tpl->assign('log',$arrLog);
		
		$html = $tpl->fetch('fieldtypes/fieldTuksiLog.tpl');
		return parent::returnHtml($this->objField->name,$html,array('fullwidth' => true));
	}
	
	function saveData() {
		
	}
}