<?php

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendLogin extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		$objTuksiUser = tuksiBackendUser::getInstance();
		
		$action = $_POST->getStr('userAction');
		
		if($action != "") {
	
			if($action == "login") {
				
				$username 	= $_POST->getStr('username');
				$password 	= $_POST->getStr('password');
				$setCookie 	= $_POST->getStr("remember") ? true : false;
			
				if(!$objTuksiUser->login($username,$password,$setCookie)) {
					$this->tpl->assign("error",true);
				} 
				
			} else if($action == "sendpw") {
					
				$email = $_POST->getStr('email');
				
				if($objTuksiUser->sendPasswordByEmail($email) !== false) {
					$this->tpl->assign("passwordsent",true);
				} else {
					$this->tpl->assign('showGetPassword',true);
					$this->tpl->assign('emailerror',true);
				}
			}
		}
		
		if ($objTuksiUser->isLogged()){
			
			if($_GET->getStr('nexturl')) {
				$nexturl = urldecode($_GET->getStr('nexturl'));
			} else {
				$nexturl = "/".tuksiIni::$arrIni['setup']['admin']."/";
			}
			header("Location: $nexturl");
			exit();
		}
		
		$objPage->addJavascript("/javascript/backend/libs/tuksi.login.js");
		
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
	
	function saveData(){
		
	}
}
?>
