<?php

/**
 * Handles cmsusers
 * login/logout/permission etc.
 * 
 * @package tuksiBackend
 */

class tuksiBackendUser {
	
	static private $instance;
	
	private $arrUser;
	private $isLoggedIn = false;
	
	public function __construct() {
		$this->secSession();
		
		$this->initUser();
	}

	static function getInstance() {

		if (!self::$instance){
			self::$instance = new tuksiBackendUser();
		}
		return self::$instance;
	}
	
	function setLangFromUser() {
		if (isset($this->arrUser['langcode'])) {
			tuksiIni::$arrIni['setup']['admin_lang'] = $this->arrUser['langcode'];
		}
	}
	function initUser() {
		
		if (isset($_SESSION['USERID']) && $_SESSION['USERID']) {
			$this->getUserFromSession();
			$this->setLangFromUser();
			$this->loadUserGroupInfo();
			$this->isLoggedIn = true;
			tuksiDebug::log("User logged in through session","Checking user");
			
		} else {
			if($this->checkUserCookie()) {
				$this->isLoggedIn = true;
				$this->setLangFromUser();
				$this->loadUserGroupInfo();
				tuksiDebug::log("User logged in through cookie","Checking user");
			} else {
				$this->isLoggedIn = false;
				tuksiDebug::log("User not logged in","Checking user");
			}
		}
		
	}
	
	static public function getUserInfo(){
		$objUser = tuksiBackendUser::getInstance();
		
		$arrInfo = $objUser->getUserFromSession();
		return $arrInfo;
	}
	
	public function isLogged() {
		return $this->isLoggedIn;
	}
	
	function getUserID() {
		if (isset($_SESSION['USERID'])) {
			return $_SESSION['USERID'];
		} else {
			return 100;
		}
	}
	/**
	 * Funktion til håndtering af login
	 *
	 * @param object $objPage
	 * @param int $goto
	 * @return unknown
	 */
	function login($username,$password,$setCookie = false) {
	
		$objDB = tuksiDB::getInstance();
		
		if(strlen($username) > 0 && strlen($password) > 0)  {
		
			$username = trim($username);
			
			if ($arrUser = $this->getUserFromName($username)) {
				if(md5("TUKS!" . $password) == $arrUser['password']) {
					$this->arrUser = $arrUser;
					
					$_SESSION['USERID'] = $arrUser['id'];
					$this->initUser();
					
					if($setCookie) {
						$this->setCookie($username,$arrUser['password']);
					}
					return true;	
				} else {
					return false;
				}
			} else {
				//set error
				return false;
			}
		} else {
			return false;
		}
	} // End login;
	
	/**
	 * Logoff
	 *
	 * @return unknown
	 */
	public function logout() {
		
		$cookie = "TUKSI_" . preg_replace("%[^a-zA-Z]%","", $_SERVER['SERVER_NAME']);
		
		if (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie] != 1) {
			setcookie($cookie,"", time() - 3600);
			setcookie($cookie,"", time() - 3600, '/');
		}
		
		$this->makeNewSession();
	}
	
	public function sendPasswordByEmail($email) {
		if(($arrUser = $this->getUserByEmail($email)) !== false){
			//insert send mail code
			$newPass = tuksiTools::generatePassword(7);
			
			$objDB = tuksiDB::getInstance();
			$objPage = tuksiBackend::getInstance();
			
			$sqlUpdate = "UPDATE cmsuser SET password = md5(concat('TUKS!','{$newPass}')) ";
			$sqlUpdate.= "WHERE id = '{$arrUser['id']}'";
			$objDB->write($sqlUpdate);

			// Send email videre til getHtml
			$sentto = $arrUser['email'];
			
			// Lav mail og send
			$this->emailTpl = new tuksiSmarty();
			$this->emailTpl->assign("username",$arrUser['login']);
			$this->emailTpl->assign("name",$arrUser['name']);
			$this->emailTpl->assign("password",$newPass);
			$this->emailTpl->assign("sitename","Tuksi");
			$this->emailTpl->assign("siteurl",$_SERVER['HTTP_HOST']);
			
			//admin_email
			$arrConf = tuksiConf::getConf();
			$m = new tuksiEmail($arrConf['email']['admin_name'],$arrConf['email']['admin_email']);
			$m->to_email = $arrUser['email'];
			$m->subject = "Glemt password";
			$m->email_text = $this->emailTpl->fetch("email/lostpassword_mail.tpl");
			$m->make_email();
			$m->send();
			
			return true;
		} else {
			return false;
		}
	}
	
	private function getUserByEmail($email){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlUser = "SELECT * ";
		$sqlUser.= "FROM cmsuser ";
		$sqlUser.= "WHERE email = '".$objDB->realEscapeString($email)."' AND active = 1 ";
		
		if(($rsUser = $objDB->fetch($sqlUser)) !== false) {
			$arrUser = $rsUser['data'][0];
			return $arrUser;
		} else {
			return false;
		}
	}
	
	private function getUserFromName($username){
			
		$objDB = tuksiDB::getInstance();
		
		$sqlUser = "SELECT * ";
		$sqlUser.= "FROM cmsuser ";
		$sqlUser.= "WHERE login = '".mysql_real_escape_string($username)."' AND active = 1 ";
		
		if(($rsUser = $objDB->fetch($sqlUser)) !== false) {
			$arrUser = $rsUser['data'][0];
			return $arrUser;
		} else {
			return false;
		}
	}
	
	/**
	 * Sets arrUser array
	 *
	 * @return unknown
	 */
	function getUserFromSession(){
		
		if (isset($this->arrUser))
			return $this->arrUser;
			
		$objDB = tuksiDB::getInstance();

		if (!empty($_SESSION['USERID'])) 
			$userId = intval($_SESSION['USERID']);
		else
			$userId = 0;
		
		$sqlUser = "SELECT * ";
		$sqlUser.= "FROM cmsuser ";
		$sqlUser.= "WHERE id = '{$userId}' AND active = 1 ";
		
		$rsUser = $objDB->fetchItem($sqlUser);

		if ($rsUser['num_rows'] > 0) {
			$this->arrUser = $rsUser['data'];
			return $this->arrUser;
		} else {
			return false;
		}
	}
	
	public function setCookie($login,$password){
		$cookieName = "TUKSI_" . preg_replace("%[^a-zA-Z]%","", $_SERVER['SERVER_NAME']);
		setcookie ($cookieName, md5("TUKS!" . $login.":".$password), time()+3600*24*7*50, "/");
	}
	
	private function checkUserCookie(){
		
		$cookie = "TUKSI_" . preg_replace("%[^a-zA-Z]%","", $_SERVER['SERVER_NAME']);
		
		if (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie] != 1) {
			
			$mdpassword = $_COOKIE[$cookie];
			$safe_md5password = tuksiValidate::secField($mdpassword, MD5);
			
			if ($safe_md5password) {
				
				$objDB = tuksiDB::getInstance();
				
				$sqlUser = "SELECT * FROM cmsuser ";
				$sqlUser.= "WHERE md5(concat('TUKS!',login,':',password)) = '{$safe_md5password}' AND active = 1";	
							
				if(($rsUser = $objDB->fetchItem($sqlUser)) !== false) {
					if($rsUser['num_rows'] == 1) {

						$this->arrUser = $rsUser['data'];
						
						$_SESSION['USERID'] = $rsUser['data']['id'];
						
						return true;
					} else {
						return false;
					}
				} else {
					return false;
				}			
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	
	
	/**
	 * Get user securitylevel
	 *
	 * @param int $userid CMS bruger ID
	 * @return int Returnere level
	 */
	
	function getUserSecurityLevel($userid) {
		
		$level = 0;
		
		$objDB = tuksiDB::getInstance();
		
		// Getting securitylevel Default = 0 (No rights)
		$sqlSecurity = "SELECT cg.securitylevel FROM cmsgroup cg, cmsusergroup cup ";
		$sqlSecurity.= "WHERE cup.cmsuserid = '{$userid}' AND cup.cmsgroupid = cg.id ";
		$sqlSecurity.= "ORDER BY cg.securitylevel DESC LIMIT 1";
		$rsSecurity = $objDB->fetch($sqlSecurity);
		if ($rsSecurity['ok'] && $rsSecurity['num_rows'] > 0) {
			$level = $rsSecurity['data'][0]['securitylevel'];
		} else {
			$level = 0;
		}
		return $level;
	}

	function loadUserGroupInfo() {
		
		$this->arrUser['securitylevel'] = $this->getUserSecurityLevel($this->arrUser['id']);
		$this->arrUser['usergroup'] = $this->getUserGroups($this->arrUser['id']);
		
	}
	// * ------------------------------------------------------------------------- *
	// Get user groups 
	// * ------------------------------------------------------------------------- *

	function getUserGroups($userid) {
		
		if (isset($this->arrGroups))
			return $this->arrGroups;
			
		$arrGroups = array();
		
		$objDB = tuksiDB::getInstance();
		
		// Getting securitylevel Default = 0 (No rights)
		$sqlGroups= "SELECT cg.id, cg.name FROM cmsgroup cg, cmsusergroup cup ";
		$sqlGroups.= "WHERE cup.cmsuserid = '{$userid}' AND cup.cmsgroupid = cg.id";
		
		//$this->addDebug("User groups", $sqlGroups);
		$rsGroups = $objDB->fetch($sqlGroups,array('type' => 'object'));
		
		if ($rsGroups['ok'] && $rsGroups['num_rows'] > 0) {
			foreach ($rsGroups['data'] as $rowData) {
				$arrGroups[$rowData->id] = $rowData;
			}
			//$this->addDebug("User groups", print_r($this->USERGROUP, 1));
		}
		return $arrGroups; 
	}
	
	// * ------------------------------------------------------------------------- *
	// Getting user permissions
	// * ------------------------------------------------------------------------- *

	function getRights($treeid,$userid) {
		return tuksiPerm::getTreeUserPerms($treeid,$userid);
	}
	
	/**
	 * Check that the session hasnt been hijacked, 
	 * The user must come from the same IP and user and browser.
	 *
	 */
	private function secSession() {
		
		// make a md5 generated from user agent and ip address
		list($a, $b, $c) = explode(".", $_SERVER['REMOTE_ADDR']);
		$md5 = md5($a. $b. $c);
		
		if (!isset($_SESSION['session_md5'])) {
			$this->makeNewSession();
			$_SESSION['session_md5'] = $md5;
			return true;
		} 
		
		if ($md5 != $_SESSION['session_md5']) {
			$this->makeNewSession();
			$_SESSION['session_md5'] = $md5;
			//$this->debug->log("User has wrong md5, making new (" . session_id() . ") ","Checking session");
		}
	}
	
	private function makeNewSession(){
		
		/*$cookie = "TUKSI_" . preg_replace("%[^a-zA-Z]%","", $_SERVER['SERVER_NAME']);
		
		if (isset($_COOKIE[$cookie]) && $_COOKIE[$cookie] != 1) {
			setcookie($cookie,"", time() - 3600);
			setcookie($cookie,"", time() - 3600, '/');
		}*/
		
		$_SESSION = array();
		session_destroy();
		session_start();
	}
}
?>
