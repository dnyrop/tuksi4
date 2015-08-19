<?php

/**
 *  Module that handles the administration and creation of backend users
 *
 * @package tuksiBackendModule
 */

class mBackendAccount extends mBackendBase {
	
	/**
	 *	Constructor 
	 * 
	 * @param object $objMod Module object
	 */
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	/**
	 * Builds a standard view/edit for the current user
	 *
	 * @return unknown
	 */
	
	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		if($this->userActionIsSet('SAVE') && $objPage->arrPerms["SAVE"]) {
			$this->saveData();
		}
		
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$objStdTpl = new tuksiStandardTemplateControl();
		
		$objStdTpl->addHeadline($objPage->cmstext('account'));
		
		$objStdTpl->addInputElement($objPage->cmsText("name"),array('id' => "account_name",'value' => $arrUser['name'],'error' => $nameError));
		$objStdTpl->addInputElement($objPage->cmsText("email"),array('id' => "account_email",'value' => $arrUser['email'],'error' => $emailError));
		$objStdTpl->addInputElement($objPage->cmsText("login"),array('id' => "account_login",'value' => $arrUser['login'],'error' => $loginError));
		$objStdTpl->addInputElement($objPage->cmsText("new_password").":",array('id' => 'new_password','type' => 'password','error' => $passError));
		$objStdTpl->addInputElement($objPage->cmsText("new_password_repeat").":",array('id' => 'new_password_repeat','type' => 'password','error' => $passError));
		
		$sqlLang = "SELECT name,langcode as value FROM cmslanguage ";
		$sqlLang.= "WHERE isactive = 1 ORDER BY seq";
		$rsLang = $objDB->fetch($sqlLang);
		
		$arrLangs = array();
		
		if($rsLang['num_rows'] > 0) {
			foreach ($rsLang['data'] as $arrLang) {
				$arrLangs[] = $arrLang;	
				if($arrLang['value'] == $arrUser['langcode']) {
					$langSelected = $arrLang['value'];
				}
			}
		}
		
		$objStdTpl->addSelectElement($objPage->cmsText("language"),array('id' => 'account_language','selected' => $langSelected,'options' => $arrLangs));
		
		$this->addButton("BTNSAVE","","SAVE");				
		
		$returnHtml = $objStdTpl->fetch();
		
		return $returnHtml;
	}
	
	/**
	 * validates the submitted data for the current users
	 * if validated saves the data
	 * if the language has been changed reload to page to update the cms language
	 */
	
	function saveData(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$arrSql = array();
		$arrError = array();
		
		if($_POST->getStr('new_password')) {
			$newPass = $_POST->getStr('new_password');
			$newPassRepeat = $_POST->getStr('new_password_repeat');
			
			if($newPass == $newPassRepeat) {
				if(tuksiValidate::checkPassword($newPass,LOW) === true) {
					//allright to save password
					$arrSql[] = " password = md5(concat('TUKS!','{$newPass}')) ";
				} else {
					$arrError[] = $objPage->cmsText("error_password_chars");
					$passError = true;
				}
			} else {
					$arrError[] = $objPage->cmsText("error_password_different");
					$passError = true;
			}
		}
		
		$name = $_POST->getStr('account_name');
		$login = $_POST->getStr('account_login');
		$email = $_POST->getStr('account_email');
		
		if(strlen($name) > 0){
			$arrSql[] = " name= '".$objDB->realEscapeString($name)."' ";
		}
		
		if(strlen($login) > 0){
			if($this->checkLogin($login)) {
				$arrSql[] = " login = '".$objDB->realEscapeString($login)."' ";
			} else {
				$arrError[] = $objPage->cmsText("error_login_exists");
				$loginError = true;
			}
		}
		if(strlen($email) > 0){
			if($this->checkEmail($email)) {
				$arrSql[] = " email = '".$objDB->realEscapeString($email)."' ";
			} else {
				$arrError[] = $objPage->cmsText("error_email_exists");
				$emailError = true;
			}
		}
		if(count($arrError) > 0) {

			$objPage->alert(join("<br />",$arrError));
		
		} else {
			
			//saving lang
			$lang = $_POST->getStr('account_language');
			$arrSql[] = " langcode = '".$objDB->realEscapeString($lang)."' ";
			
			$strSql = "UPDATE cmsuser SET " . join(", ",$arrSql) . " WHERE id = '".$arrUser['id']."' ";
			$objDB->write($strSql);
			$objPage->status($objPage->cmsText('user_saved'));
			
			header("location: " . $objPage->getUrl($objPage->treeid));
			die();
		}
	}
	
	/**
	 * Checks if the submitted login isn't already in use
	 *
	 * @param String $login
	 * @return boolean
	 */
	
	function checkLogin($login){
		
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$sql = "SELECT * FROM cmsuser ";
		$sql.= "WHERE login = '{$login}' AND id <> '".$arrUser['id']."' ";
		
		$arrRs = $objDB->fetch($sql);
		
		if($arrRs['num_rows'] > 0){
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Checks if the submitted e-mail isn't already in use
	 *
	 * @param string $email
	 * @return boolean
	 */
	
	function checkEmail($email){
		
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$sql = "SELECT * FROM cmsuser ";
		$sql.= "WHERE email = '{$email}' AND id <> '".$arrUser['id']."' ";
		
		$arrRs = $objDB->fetch($sql);
		
		if($arrRs['num_rows'] > 0){
			return false;
		} else {
			return true;
		}
	}
}
?>
