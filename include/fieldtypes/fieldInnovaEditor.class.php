<?php
// Template Class

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldInnovaEditor extends field {

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {

		$objPage = tuksiBackend::getInstance();
		$tpl = new tuksiSmarty();

		$base_url = "/thirdparty/";
		
		$tpl->assign("base", $base_url . "innovaeditor");
		$tpl->assign("htmltagname", $this->objField->htmltagname);
		$tpl->assign("value", $this->objField->value);
		
		$objPage->addJavascript('/thirdparty/innovaeditor/scripts/language/danish/editor_lang.js');
		$objPage->addJavascript('/thirdparty/innovaeditor/scripts/innovaeditor.js');
		
		$HtmlTag = parent::getHtmlStart();
		$HtmlTag.= $tpl->fetch('fieldtypes/fieldInnovaEditor.tpl');
	
		return parent::returnHtml($this->objField->name, $HtmlTag);
	}

	function saveData() {
		$objDB = tuksiDB::getInstance();
		
		if ($this->objField->value) 
		$sqlReturn = $this->objField->colname . " = '" . $objDB->escapeString(stripslashes($this->objField->value)). "'";
    
		return $sqlReturn;
	}

	function getListHtml() {
		return $this->objField->value;
	}
	
	function releaseData() {
		
		//check for uploaded files
		$html = $this->objField->value;
		
		//find ids for uploaded files
		if(preg_match("/href=\"\/downloads\/([0-9]+)\//i",$html,$m)) {
			tuksiRelease::releaseTableRowRaw('cmslinkupload',$m[1]);
		}
		
	}
	
	function getFrontendValue() {
		
		$returnValue = $this->objField->value;
		
		preg_match_all('/(cmstree:[0-9]+)/i',$returnValue,$m);

		if(count($m[0]) > 0) {
			foreach ($m[0] as $link) {
				$arrLink = fieldLink::makeUrl($link);
				$returnValue= str_replace($link,$arrLink['url'],$returnValue);
			}
		}
		
		// Remove empty HTML content
    if($returnValue == "<br />\r\n") { 
      $returnValue = '';
    }
    
		return $returnValue;
	}
	
} // END Class
?>
