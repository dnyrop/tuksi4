<?php

/**
 * HTMLEditor
 * 
 * Release function releases files in cmslinkupload if selected via fieldLink
 *
 * @package tuksiFieldType
 */

class fieldFCKEditor extends field {

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {

		$objPage = tuksiBackend::getInstance();
		
		$tpl = new tuksiSmarty();

		$HtmlTag = parent::getHtmlStart();
		
		$tpl->assign("htmltagname", $this->objField->htmltagname);
		$tpl->assign("value", $this->objField->value);
		
		$HtmlTag.= $tpl->fetch('fieldtypes/fieldFCKEditor.tpl');
	
		return parent::returnHtml($this->objField->name, $HtmlTag,array('fullwidth' => true));
		
	}
	
	function saveData(){
		
		$objDB = tuksiDB::getInstance();
		$this->objField->value = str_replace('a&#778;', '&aring;', $this->objField->value);
		$this->objField->value = str_replace('A&#778;', '&Aring;', $this->objField->value);
		
		$sqlReturn = $this->objField->colname . " = '" . $objDB->escapeString(stripslashes($this->objField->value)) . "'";
    
		return $sqlReturn;
	}
	
	function getListHtml() {
		return $this->objField->value;		
	}
	
	function releaseData() {
		
		//check for files indserted in HTML via fieldLink/fieldFileUpload.
		// /downloads/[ID] file is released here.
		$html = $this->objField->value;
		
		//find ids for uploaded files
		if (preg_match_all("/href=\"\/downloads\/([0-9]+)\//i", $html, $m)) {
			foreach ($m[1] as $fileId) {
				tuksiRelease::releaseTableRowRaw('cmslinkupload', $fileId);
			}
		}
	}
	
	function getFrontendValue() {
		
		$returnValue = $this->objField->value;
		
		// Search <a> tags for file:ID or cmstree:ID
		preg_match_all('%"(((cmstree|file):[0-9]+)|(relative:[a-z0-9-_/]+\.html))"%i',$returnValue,$m);
		if(count($m[1]) > 0) {
			foreach ($m[1] as $link) {
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
}
