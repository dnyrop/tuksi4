<?php
/**
 * Grundklasse til alle module i frontenden
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 * 
 */
class mBackendBase {
	
	public $userAction;
	private $objHook;
	static $arrUserGroups;
	public $template;
	public $class;
	public $template_full;

	/**
	 * class for the content area returns with modules
	 *
	 * @param object $objMod Module object
	 * @param object $objPage Tuksi object
	 * @param int $cache_time Cache this objects HTML for x seconds.
	 * @return module_base
	 */
  	function __construct (&$objMod) {
  		$this->objMod 	= &$objMod;
		$this->setUserAction();
		$this->setHook();
			
		$this->template_full = "modules/backend/" . $this->objMod->template;
  	}
  	
  	function getCmsText() {
  		return tuksiText::getInstance($this->template_full);
  	}

	/**
	 * Laver en instance af modul
	 *
	 *
	 * @param object Modul der skal laves
	 * @param object Side objektet
	 * @return object Instance af modul
	 */
	static function getInstance($objMod) {

		if (preg_match("/.tpl$/", $objMod->classname, $m)) {
			$objMod->template = $objMod->classname;
			$objMod->classname = "mBackendStandard";
		} else {
			$objMod->template = $objMod->classname . ".tpl";
		}

		$objModule = new $objMod->classname($objMod);

		$objModule->class = $objMod->classname;
		$objModule->template = $objMod->template;
		return $objModule;
	}

/**
 * Checks if any hooks are set for the current page
 * If a hook has been set loads the hook into the class
 */
	
	public function setHook(){
		
		$objPage = tuksiBackend::getInstance();
		if(!empty($objPage->arrTree['hook'])) {
			if(@class_exists($objPage->arrTree['hook'])) {
				$this->objHook = new $objPage->arrTree['hook']($this->objMod);
			} else {
				tuksiDebug::warning($objPage->arrTree['hook'] . " couldn't be loaded");
			}
		}
	}

	/**
	 * If a hook is active loads this hook before doing the action
	 *
	 * @param string $action type of action etc. save, edit
	 * @return boolean
	 */
	
	function hookBefore($action){
		if($this->objHook) {
			return $this->objHook->before($action);
		}
		return true;
	}
	
	/**
	 * If a hook is active loads this hook after doing the action
	 *
	 * @param string $action type of action etc. save, edit
	 * @return boolean
	 */
	
	function hookAfter($action){
		if($this->objHook) {
			return $this->objHook->after($action);
		}
		return true;
	}
	
	/**
	 * parses a value to the active hook
	 *
	 * @param string $name
	 * @param string $value
	 */
	
	function addHookValue($name,$value){
		if($this->objHook) {
			$this->objHook->addValue($name,$value);
		}
	}
	
	
	/**
	 * Loads all the values and their coherent fieldtypes for the current modules
	 * If the fieldtype has a special value return the function is loaded
	 * The values are added to the modules template
	 *
	 * @return Array containing all values 
	 */
	
	public function addStandardFields() {
		
		$objDB = tuksiDB::getInstance();
		
		$arrFields = array();
		$sql = "SELECT fi.*, ft.classname ";
		$sql.= "FROM cmsfielditem{$arrConf['setup']['tableext']} fi, cmsfieldtype ft  ";
		$sql.= "WHERE fi.itemtype = 'pg' AND fi.cmsfieldtypeid = ft.id AND fi.relationid = '{$this->objMod->pg_moduleid}'";
		$sql.= "AND ft.speciel_frontend = 1 ";

		$arrReturn = $objDB->fetch($sql, array('type' => 'object'));
		
		foreach ($arrReturn['data'] as $arrFieldItem) {
        	$arrFieldTypes[$arrFieldItem->colname] = $arrFieldItem;
    }
        
		foreach ($this->objMod as $key => $value) {
			
			if (isset($arrFieldTypes[$key]->cmsfieldtypeid)) {
				$classname = $arrFieldTypes[$key]->classname;
				$arrFieldTypes[$key]->value = $value;
				$arrFieldTypes[$key]->pg_moduleid = $this->objMod->id;
				
				$objFieldType = new $classname($arrFieldTypes[$key]);
				
				$arrFields[$key] = $objFieldType->getFrontendValue();
				
				//print_r($arrFields[$key]);
			} else {
				$arrFields[$key] = $value;
			}
		}
		
		$arrFields['id'] = $this->objMod->id;
		
	 	$this->tpl->assign("module", $arrFields);

	 	return $arrFields;

	} // End addStandardFields();

	/**
	 * Loads the stand values and
	 * returns the html for the current module
	 *
	 * @return String html containing the module
	 */
	
	
	public function getHtml() {
		
		
		$this->addStandardFields();
		
		$html = $this->tpl->fetch($this->template_full, $this->objMod->id);
		
		return $html;
	}
	
	/**
	 * Sets the active action from POST
	 *
	 */
	
	private function setUserAction(){
		$strAction = $_POST->getStr('userAction');
		$this->userAction = $strAction;
	}
	
	/**
	 * Checks if a given action has been set by the user
	 *
	 * @param string $token
	 * @return boolean
	 */
	
	public function userActionIsSet($token){
		if(strlen($token) > 0) {
			if($this->userAction == $token) {
				return true;	
			}
		}
		return false;
	}
	
	/**
	 * Adds a button to the page if the user have the required permission
	 *
	 * For more information on the parameters look at include/controls/backend/cBackendMain.class.php
	 * 
	 * @param string $button_name
	 * @param string $button_text
	 * @param string $permission_token
	 * @param string $confirm
	 * @param string $onclick
	 * @param string $alert
	 * @param string $type
	 */
	
	public function addButton($button_name, $button_text = "", $permission_token= "", $confirm = "", $onclick = "",$alert = "",$type = "normal") {

		$objPage = tuksiBackend::getInstance();
		
		if((isset($objPage->arrPerms[$permission_token]) && $objPage->arrPerms[$permission_token]) || $this->checkUsergroupPerm($permission_token)) {
			$objPage->addButton($button_name, $button_text, $permission_token, $confirm,$onclick,$alert,$type);
		} 
	}
	
	/**
	 * Adds an actionbutton to the page if the user have the required permission
	 *
	 * or more information on the parameters look at include/controls/backend/cBackendMain.class.php
	 * 
	 * @param unknown_type $button_name
	 * @param unknown_type $button_text
	 * @param unknown_type $permission_token
	 * @param unknown_type $confirm
	 * @param unknown_type $onclick
	 * @param unknown_type $alert
	 * @param unknown_type $type
	 */
	
	public function addActionButton($button_name, $button_text = "", $permission_token= "", $confirm = "", $onclick = "",$alert = "",$type = "normal") {
		
		$objPage = tuksiBackend::getInstance();
		
		if((isset($objPage->arrPerms[$permission_token]) && $objPage->arrPerms[$permission_token]) || $this->checkUsergroupPerm($permission_token)) {
			$objPage->addActionButton($button_name, $button_text, $permission_token, $confirm,$onclick,$alert,$type);
		} 
	}
	
	public function getActionName($button_name) {
		return $button_name;
	}
	
	function checkUsergroupPerm($token){
		
		if(($pos = strpos($token,'USERGROUP_') !== false)){
			$grp = str_replace("USERGROUP_","",$token);
		
			if(empty(self::$arrUserGroups)) { 
				
				$objDB = tuksiDB::getInstance();
				
				$sql= "SELECT permtoken,id FROM cmsgroup";
				$rs = $objDB->fetch($sql);
				
				if($rs['ok'] && $rs['num_rows'] > 0){
					
					foreach ($rs['data'] as $arr){
						
						self::$arrUserGroups[$arr['permtoken']] = $arr['id'];
					}
				}
			}
			
			if(isset(self::$arrUserGroups[$grp])) {
				$usrGrp = self::$arrUserGroups[$grp];
				if($usrGrp > 0) {
					$arrUser = tuksiBackendUser::getUserInfo();
					if(isset($arrUser['usergroup'][$usrGrp])){
						return true;
					}
				}
			}
		}
		return false;
	}
}
?>
