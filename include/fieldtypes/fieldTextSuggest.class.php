<?php

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldTextSuggest extends field {

	static $arrLang = array();
	
	function fieldTextSuggest($objField){
		parent::field($objField);
	}
	
	function getHTML() {
		//make like
		$objPage = tuksiBackend::getInstance();
		
		$objPage->addJavascript('/javascript/backend/fieldtypes/fieldTextSuggest.js');

		$arrOptions = array('id' => $this->objField->htmltagname);

		$arrOptions['width'] = $this->objField->fieldvalue1;

		if (isset($this->objField->readonly)) {
			$arrOptions['disabled'] = $this->objField->readonly;
		}

		$value = str_replace('"','&quot;',$this->objField->value);

		$arrOptions['value'] = $value;

		$strHtml = parent::getHtmlStart();
		$strHtml.= tuksiFormElements::getCmstextInput($arrOptions);

		$arrValues = $this->getCurrentValue();

		$emptyValue = $objPage->cmstext('empty');

		if($arrValues['id']) {

			$objStdTpl = new tuksiStandardTemplateControl();

			$arrLangs = array();
			
			$tpl = new tuksiSmarty();
			$tpl->assign('lang',$arrValues['lang']);
			
			$strHtml.= $tpl->fetch('fieldtypes/fieldTextSuggest.tpl');

			$arrConf = tuksiConf::getConf();

			$editUrl = $objPage->getUrl($arrConf['link']['text_admin_treeid'],$arrConf['link']['text_admin_cms_tabid']);

			$objStdTpl->addElement("","<a href=\"" . $editUrl . "&rowid=" . $arrValues['id']."\">".$objPage->cmstext('edit')."</a>");

			$strHtml.= $objStdTpl->fetch();
		}
		
		return parent::returnHtml($this->objField->name,$strHtml);
		
	}
	
	function saveData(){
		
		//check if token exists
		$objDB = tuksiDB::getInstance();
		
		if($this->objField->value) {
		
			$sqlChk = "SELECT * FROM cmstext WHERE token = '".$objDB->realEscapeString($this->objField->value)."' ";
			$arrChk = $objDB->fetchItem($sqlChk);
			if($arrChk['num_rows'] != 1) {
				//insert token
				$sqlInsToken = "INSERT INTO cmstext SET token = '".$objDB->realEscapeString($this->objField->value)."' ";
				$rsIns = $objDB->write($sqlInsToken);
			}
		}
		
		$sql = $this->objField->colname . " = '" . mysql_real_escape_string($this->objField->value) . "'";
		return $sql;
	}
	
	function getCurrentValue(){
		
		$arrValues = array();
		$tokenId = 0;
		
		//get current active languages
		$objDB = tuksiDB::getInstance();
		
		if(count(self::$arrLang) == 0) {
			$sqlLang = "SELECT langcode,name,id FROM cmslanguage WHERE isactive = 1";
			$rsLang = $objDB->fetch($sqlLang);
			if($rsLang['num_rows'] > 0) {
				self::$arrLang = $rsLang['data'];
			}
		}
		
		if($this->objField->value != '') {
			$sqlChk = "SELECT * FROM cmstext WHERE token = '".$objDB->realEscapeString($this->objField->value)."' ";
			$arrChk = $objDB->fetchItem($sqlChk);
			if($arrChk['num_rows']){
				//insert token
				$arrVal = $arrChk['data'];
				
				$tokenId = $arrVal['id'];
	
				foreach (self::$arrLang as $code) {
					$arrValues[$code['langcode']] =  array(	'value' =>  str_replace('"','&quot;',$arrVal['value_' . $code['langcode']]),
																									'name' => $code['name'],
																									'id' => $arrVal['id'],
																									'lang' => $code['langcode']);
				}
			}
		}
		return array('id' => $tokenId,
								'lang' => $arrValues);
	}
	
	function getListHtml() {
		return $this->objField->value;
	}
	
	function getFrontendValue(){
		$value = $this->getCurrentLangValue();
		return $value;
	}
	
	function getCurrentLangValue(){
		
		$objDB = tuksiDB::getInstance();
		
		$arrConf = tuksiConf::getConf();
		
		$postfix = $arrConf['setup']['admin_lang'];
		
		if($this->objField->value != '') {
			$sqlChk = "SELECT value_".$postfix." as value FROM cmstext WHERE token = '".$objDB->realEscapeString($this->objField->value)."' ";
			$arrChk = $objDB->fetchItem($sqlChk);
			return $arrChk['data']['value'];
		} else {
			return "";
		}
	}
	
}


?>