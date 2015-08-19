<?php
/**
 * Handles user management
 * 
 * @uses tuksiDB
 * @uses tuksiBackend
 * @uses tuksiStandardTemplateControl
 * @uses tuksiValidate
 * @package tuksiBackendModule
 * 
 */

class mBackendUser extends mBackendBase {
	
	private $userChanged = false;
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		
		$this->currentUserId = 0;

		if($_POST->getInt("changeUser_".$this->objMod->id)) {
			$this->currentUserId = $_POST->getInt("changeUser_".$this->objMod->id);
			$this->userChanged = true;
		} elseif($_POST->getInt("currentUser_".$this->objMod->id)) {
			$this->currentUserId = $_POST->getInt("currentUser_".$this->objMod->id);
			$this->userChanged = false;
		}
	}
	
	public function getHTML(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$errors = array();
		if($objPage->action == "ADD") {
			$objPage->status($objPage->cmsText('useradded'));
			$this->add();
		} else if($objPage->action == "DELETE" && $this->currentUserId > 0 && !$this->userChanged) {
			$objPage->status($objPage->cmsText('userdeleted'));
			$errors = $this->delete();
		} else if($objPage->action == "SAVE" && $this->currentUserId > 0 && !$this->userChanged) {
			$errors = $this->saveData();
			if ($errors === true) {
				$objPage->status($objPage->cmsText('usersaved'));
			} else {
				$objPage->alert(join('<br />', $errors));
			}
		} 
		
		$objStdTpl = new tuksiStandardTemplateControl();
		
		$objStdTpl->addHeadline($objPage->cmsText('headline_useradm')."<a name=\"top\"></a>");
		
		$sqlUser = "SELECT c.*, max(cg.securitylevel) as securitylevelmax ";
		$sqlUser.= "FROM cmsuser c ";
		$sqlUser.= "LEFT JOIN cmsusergroup cu ON (c.id = cu.cmsuserid) ";
		$sqlUser.= "LEFT JOIN cmsgroup cg ON (cu.cmsgroupid = cg.id) group by c.id ORDER BY name";
		$rsUser = $objDB->fetch($sqlUser);
		
		$arrUsers = array();
		
		$arrUsers[] = array('value' => '-1','name' => $objPage->cmstext('btnchooseuser'));
		
		$arrCurrentUser = array();
		
		foreach($rsUser['data'] as $arrShowUser) {

			// Vis kun brugere under ens egen sikkerhedsniveau
			if (($arrShowUser['securitylevelmax'] >= 0 && $arrShowUser['securitylevelmax']  < $arrUser['securitylevel']) || ($arrUser['securitylevel'] == 1000)) {
				$arrUsers[] = array('value' => $arrShowUser['id'],'name' => $arrShowUser['name']);
			}
			if($this->currentUserId == $arrShowUser['id']) {
				$arrCurrentUser = $arrShowUser;
			}
		}
		
		$objStdTpl->addHiddenField(array("NAME" => "changeUser_".$this->objMod->id));
		
		$objStdTpl->addSelectElement( $objPage->cmstext("chooseuser"),array("name" 		=> "users_".$this->objMod->id, 
																"class" 		=> "forminput400",
																"options" 	=> $arrUsers,
																"selected"	=> $this->currentUserId,
																"onchange" 	=> "document.getElementById('changeUser_{$this->objMod->id}').value = this.options[this.options.selectedIndex].value;saveData();"));
		
																
		if(is_array($arrCurrentUser) && isset($arrCurrentUser['id'])) {
			
			$objStdTpl->addHiddenField(array("NAME" => "currentUser_".$this->objMod->id,"VALUE" => $this->currentUserId));
			
			$objStdTpl->addHeadline($objPage->cmstext('headlineuser'));
			
			$objStdTpl->addCheckboxElement($objPage->cmstext("active"),array(	"name" => "ACTIVE",
																									"value" => "1",
																									"checked" => $arrCurrentUser['active']));
			$objStdTpl->addInputElement($objPage->cmstext("username"),array(	"name" => "NAME",
																									"value" => $arrCurrentUser['name'],
																									"error" => $errors['name']));
			$objStdTpl->addInputElement($objPage->cmstext("login"),array(	"name" => "LOGIN",
																								"value" => $arrCurrentUser['login'],
																								"error" => $errors['login']));
			$objStdTpl->addPasswordElement($objPage->cmstext("password"),array(	"name" => "PASS",
																										"value" => "",
																										"error" => $errors['pass']));
			$objStdTpl->addInputElement($objPage->cmstext("email"),array(	"name" => "EMAIL",
																								"value" => $arrCurrentUser['email']));
			
			$arrLangs = array();

			$sqlLang = "SELECT name,langcode as value FROM cmslanguage ";
			$sqlLang.= "WHERE isactive = 1 ORDER BY seq";
			$rsLang = $objDB->fetch($sqlLang);
			
			$arrLangs = array();
			
			if($rsLang['num_rows'] > 0) {
				foreach ($rsLang['data'] as $arrLang) {
					$arrLangs[] = $arrLang;	
					if($arrLang['value'] == $arrCurrentUser['langcode']) {
						$langSelected = $arrLang['value'];
					}
				}
			}
																										
			$objStdTpl->addSelectElement( $objPage->cmstext("language"),array(
				"name" => "LANG",
				"options" => $arrLangs,
				"selected"	=> $langSelected,
			));
				
			$objStdTpl->addHeadline($objPage->cmstext('headlineusergroup'));
			
			//$objStdTpl->addButton();
			$sqlGroups  = "SELECT cu.id, cu.name, cug.cmsuserid, cu.groupdesc ";
		  $sqlGroups.= "FROM cmsgroup cu LEFT ";
		 	$sqlGroups.= "JOIN cmsusergroup cug on (cu.id = cug.cmsgroupid AND cug.cmsuserid = '{$this->currentUserId}') WHERE (cu.securitylevel < {$arrUser['securitylevel']} OR {$arrUser['securitylevel']} = 1000) AND cu.securitylevel > 0 ";
		 	$sqlGroups.= "ORDER BY cu.securitylevel DESC";

			$rsGroups = $objDB->fetch($sqlGroups) or print mysql_error();
			
			foreach ($rsGroups['data'] as $arrGrp) {
				$objStdTpl->addCheckboxElement($groupname,array("name" => "GRP_" . $arrGrp['id'],
																				"value" => 1,
																				"checked" => $arrGrp['cmsuserid'],
																				"desc" => $arrGrp['name'] . " (".$arrGrp['groupdesc'] . ")"));
			} // End while brugergruppe
			
			$this->addButton("BTNSAVE", "", "SAVE");
			$this->addButton("BTNDELETE", "", "DELETE");
		}																
		$this->addButton("BTNADD", "", "SAVE");
		return $objStdTpl->fetch();
	}
	
	public function saveData(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$boolOk = 1;
		// Lav tjekboks om til database format
		if ($_POST->getInt('ACTIVE')) {
			$ACTIVE = 1;
		}
	
		// Lav MD5 adgangskode til database
		$safe_password = $_POST->getStr('PASS');
		if ($safe_password) {
			$sqlPassword = " password = md5(concat('TUKS!','{$safe_password}')), ";
			
			// Tjek at password er godt nok. Skal være low.
			$pStatus = tuksiValidate::checkPassword($safe_password);
			
			if (is_array($pStatus)) {
				$boolOk = 0;
				$error['pass'] = "&nbsp;Password skal indholde sm&aring;, store bostaver, samt tal. Skal mindst v&aelig;re 5 tegn.";
			}
		}
		
		// Tjek at brugerlogin ikke er optaget
		$sqlCheckLogin = "SELECT id FROM cmsuser WHERE login = '{$_POST->getStr('LOGIN')}' AND id <> '{$this->currentUserId}'";
		$rs = $objDB->fetch($sqlCheckLogin);
		if ($rs['ok'] && $rs['num_rows']) {
			$boolOk = 0;
			$error['login'] = "&nbsp;Login er i brug.";
		}
		
		// Tjek at navn er indtastet
		if (!$_POST->getStr('NAME')) {
			$boolOk = 0;
			$error['name'] = "&nbsp;Navn skal indtastes.";
		}
		
		// Hvis alt OK, så gem bruger info. og opdater gruppe informationer
		if ($boolOk) {
			
			$SQL = "UPDATE cmsuser SET ";
			$SQL.= "login = '{$_POST->getStr('LOGIN')}', $sqlPassword name = '{$_POST->getStr('NAME')}', email = '{$_POST->getStr('EMAIL')}', ";
			$SQL.= "active = '$ACTIVE', langcode = '{$_POST->getStr('LANG')}' ";
			$SQL.= "WHERE id = '{$this->currentUserId}'";
			$result = $objDB->write($SQL);
	
			$result = $objDB->write("DELETE FROM cmsusergroup WHERE cmsuserid = '{$this->currentUserId}'");
			$SQL = "SELECT id FROM cmsgroup";
			$arrRS = $objDB->fetch($SQL);
			
			foreach ($arrRS['data'] as $arrGrp){
				$var = "GRP_" . $arrGrp['id']; 
				if ($_POST->getStr($var) || $arrGrp['id'] == 8) {
					$SQL = "INSERT INTO cmsusergroup (cmsuserid, cmsgroupid) values('{$this->currentUserId}','".$arrGrp['id']."')";
					$GrpResult = $objDB->write($SQL);
				}
			}
			return true;
		} else {
			return $error;
		}
	}
	
	/**
	 * Add user and set $this->currentUserId
	 *
	 */
	private function add(){
		
		$objDB = tuksiDB::getInstance();
		
		$SQL = "INSERT INTO cmsuser (login, password, name, email, active,langcode ) VALUES('?', '','', '', 0,'da')";
		//print $SQL;
		$result = $objDB->write($SQL) or print "Cant add " . mysql_error();
		
		// Sæt den tilføjede bruger som den valgte.
		$this->currentUserId =  $result['insert_id'];
		$this->userChanged = true;
	}
	
	/**
	 * Delete user by $this->currentUsreId
	 *
	 */
	private function delete(){
		
		$objDB = tuksiDB::getInstance();
		
		// Deleting from cmsuser
		$sql = "DELETE FROM cmsuser WHERE id = '{$this->currentUserId}'";
		//print $sql;
		@$objDB->write($sql);
		
		// Deleting from group relations 
		$sql = "DELETE FROM cmsusergroup WHERE cmsuserid = '{$this->currentUserId}'";
		// print $sql;
		@$objDB->write($sql);
	
		// unsetting soo no user is choosen
		$this->currentUserId = null;
	}
}
?>
