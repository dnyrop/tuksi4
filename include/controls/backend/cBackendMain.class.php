<?php

/**
 * Setup a page in the sytems including:
 * -Checking user permission
 * -Loading/building the menu
 * -Building the breadcrumb
 * -Loading page modules
 * -Loading general page information
 * 
 * @todo PHP doc
 * @package tuksiBackendPage
 */
class cBackendMain extends cBackendBase {

	// Indeholder assoc array af alle side fundet via urlparts
	public $arrTreeObjs	= array();

	// Preview af side fra Tuksi
	public $previewMode = false;

	private $arrOnSubmitJS = array();
	
	/**
	 * Construktør for klassen, som henter side information ud fra treeid
	 * @param int $treeid page id nummer
	 */
	function __construct() {
		parent::__construct();
		$this->setMainTemplate("controls/backend/" . __CLASS__ . ".tpl");
		
		$objTuksiUser =  tuksiBackendUser::getInstance();
		
		if (!$objTuksiUser->isLogged()){
			$nexturl = urlencode($_SERVER['REQUEST_URI']);
			header("Location: " . $this->getUrl(100) . "&nexturl=".$nexturl);
			exit();
		} 
			
		$this->setPage();
		
		$this->action = $_POST->getStr('userAction');		
		
		$this->arrTree = $this->getPageInformation($this->treeid);

		$this->initHistory();
		//add history
		$this->addHistory($this->arrTree['id'],$this->arrTree['name']);
		
		// Fase 2
		//$this->setPageStatus();
		
		$this->setPageVar("treeid", $this->treeid);
		$this->setPageVar("tabid", $this->tabid);
		
		$this->setPageVar("menuname", $this->arrTree['menuname']);
		
		$this->addHeadline($this->arrTree['title']);
		
		if ($this->arrTree['name']) {
			$this->addTitle($this->arrTree['name'], true);
		}
		$this->addTitle(' : ' . $this->arrTree['title']);

		if(isset($this->arrTree['pg_urlpart']))
			$this->setPageVar("pg_urlpart", $this->arrTree['pg_urlpart']);
		
		if(isset($this->arrTree['pg_urlpart_full']))
			$this->setPageVar("pg_urlpart_full", $this->arrTree['pg_urlpart_full']);

		if (!empty($this->arrTree['pg_metakeywords'])) {
			$this->addMetaKeyword($this->arrTree['pg_metakeywords']);
		}

		if (!empty($this->arrTree['pg_metadescription'])) {
			$this->addMetaDescription($this->arrTree['pg_metadescription']);
		}
	
		// Henter side fra pagegenerator system
		$this->loadPage();
		
		// Laver stier til navigeringen 
		for ($i = 1; $i< count($this->arrTreeObjs) - 1; $i++) {
			$url = "/".$this->arrTreeObjs[$i]['pg_urlpart_full'];
			$url = $this->cleanUrl($url);
			$this->arrPath[] = array('name' => $this->arrTreeObjs[$i]['menuname'], 'url' => $url);
		}
				
		$arrUser = tuksiBackendUser::getUserInfo();
		
		if(isset($arrUser['usergroup']['1'])){
			$this->tplMain->assign('showcontrolpanel',true);
		}		
		
		$this->runActions();
		
		
	} // End pagegenerator_control();

	private function runActions(){
		
		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();

		$arrConf = tuksiConf::getConf();

		// Respons to the action below
		switch($_GET->getStr('action_status')) {
			case('pagedeleted') :
				$objPage->status($this->cmsText('pagedeleted'));
				break;

			case('pagecopied') : 
				$objPage->status($objPage->cmsText('pagecopied'));
				break;

			case('pageadded') : 
				$objPage->status($objPage->cmsText('pageadded'));
				break;
		}
	
		if($this->action == "BACK") {
			$this->gotoBookmark();			
		} elseif ($this->action == "DELETE_PAGE" && ($objPage->arrPerms['DELETE'] || isset($arrUser['usergroup']['1']))) {
			$objPagegenerator = tuksiPageGenerator::getInstance();
			$objPagegenerator->deletePage($objPage->treeid);
			$goto_treeid = '?treeid=' . $objPage->arrTree['parentid'] . '&action_status=pagedeleted';
			
			header("location: /{$arrConf['setup']['admin']}/" . $goto_treeid);
			die();
		} elseif($this->action == "COPY_PAGE" && ($objPage->arrPerms['ADD'] || isset($arrUser['usergroup']['1']))) {
			$strJSON = $_POST->getStr('json');
			$json = new tuksiJSON();
			$arrValues = $json->parse($strJSON);
			if(($arrNew = $this->copyPage($arrValues)) !== false) {
				header("location: ".$this->getUrl($arrNew['NEWTREEID'])."&action_status=pagecopied");
				die();
			} else {
				$objPage->status($objPage->cmsText('pagecopiedfailed'));
			}
		} elseif($this->action == "ADD_PAGE" && ($objPage->arrPerms['ADDPAGE'] || isset($arrUser['usergroup']['1']))) {
			$strJSON = $_POST->getStr('json');
			$json = new tuksiJSON();
			$arrValues = $json->parse($strJSON);
			if(($arrNew = $this->addPage($arrValues)) !== false) {
				header("location: ".$this->getUrl($arrNew['NEWTREEID'])."&settings=1&action_status=pageadded");
				die();
			} else {
				$this->status($objPage->cmsText('pageaddfailed'));
			}
		} elseif($this->action == "MOVE_PAGE" && ($objPage->arrPerms['DELETE'] || isset($arrUser['usergroup']['1']))) {
			$strJSON = $_POST->getStr('json');
			
			$json = new tuksiJSON();
			$arrValues = $json->parse($strJSON);
			
			if(($arrNew = $this->movePage($arrValues)) !== false) {
				header("location: ".$this->getUrl($objPage->treeid));
				die();
			} else {
				$objPage->status($objPage->cmsText('pagecopiedfailed'));
			}
		} elseif($this->action == "MOVE_TAB" && ($objPage->arrPerms['DELETE'] || isset($arrUser['usergroup']['1']))) {
			$strJSON = $_POST->getStr('json');
			
			$json = new tuksiJSON();
			$arrValues = $json->parse($strJSON);
			
			if(($arrNew = $this->moveTab($arrValues)) !== false) {
				header("location: ".$this->getUrl($arrNew['treeid'],$arrNew['tabid']));
				die();
			} else {
				$objPage->status($this->cmsText('pagecopiedfailed'));
			}
			
		} elseif ($this->action == "RELEASE_PAGE" && ($objPage->arrPerms['RELEASEPAGE'] || isset($arrUser['usergroup']['1']))){
			
			$strJSON = $_POST->getStr('json');
			
			$json = new tuksiJSON();
			$arrValues = $json->parse($strJSON);
			
			$arrStatus = $this->releasePage($arrValues);

			if($arrStatus['ok']) {
				$objPage->status($this->cmsText('pagereleased'));
			} else {
				$objPage->status($arrStatus['errorText']);
			}
		}
	}
	
	function removeNode($id){
		$this->removeNodeFromMenu($id,$this->arrMenu);
	}
	
	function removeNodeFromMenu($id,&$arrMenu){
		foreach($arrMenu as &$arrNode){
			if($arrNode['id'] == $id) {
				$arrNode = array();
				break;
			} elseif(is_array($arrNode['nodes'])) {
				$this->removeNodeFromMenu($id,$arrNode['nodes']);
			}
		}
	}
	
	function setTopmenuID($arrNode) {

		if(is_array($arrNode['parentids'])) {
			$prev = 0;
			$arrNode['parentids'] = array_reverse($arrNode['parentids']);
			foreach($arrNode['parentids'] as $id) {
				if($id == 1) {
					if($prev > 0) {
						$this->topmenuId = $prev;
					} else {
						$this->topmenuId = $arrNode['id'];	
					}
					return $this->topmenuId;
				} 
				$prev = $id;
			}
			return false;
		}
		return false;
	}
	
	function makeTplMenu($arrNodes,$parent = 1){
		
		return $arrNodes['nodes'];
		$arrReturn = array();
		
		if(is_array($arrNodes['nodes'])) {
			foreach ($arrNodes['nodes'] as $arrNode) {
				$arrReturn[] = array('name' => $arrNode['name'],
											'parentid' => $parent,
											'id' => $arrNode['id']);
				$arrChildNodes = $this->makeTplMenu($arrNode,$arrNode['id']);
				$arrReturn = array_merge($arrReturn,$arrChildNodes);
			}
		}
		return $arrReturn;
	}
	
	
	function setPermissions(){
		if($this->treeid && $this->tabid) {
			//get permissions
		}
	}
	
	function checkPermissions(){
		if(!$this->PERM['READ']) {
			//goto to no access page
		}
	}
	
	function addBookmark($link = ""){
		if(!$link) {
			$link = $_SERVER['REQUEST_URI'];
		}
		$_SESSION['back_url'] = $link;
	}
	
	function gotoBookmark(){
		if(isset($_SESSION['back_url']) && $_SESSION['back_url'] != '') {
			header("Location: " . $_SESSION['back_url']);
			exit();
		}
	}
	
	function bookmarkSet(){
		if(isset($_SESSION['back_url']) && $_SESSION['back_url'] != '') {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function setPageStatus(){
		
		$arrStatus = array();
		
		$objDB = tuksiDB::getInstance();
		$arrTree = $objDB->fetchRow("cmstree",$this->treeid);

		$arrStatus['show'] = true;
		
		//created
		$sqlCreated = "SELECT cu.name,date_format(cel.dateadded,'%d/%m-%y %H:%i') as created FROM cmseventlog cel,cmsuser cu ";
		$sqlCreated.= "WHERE cel.cmseventlogtypeid = 2 AND cel.tablename = 'cmstree' AND cel.relationid = '{$this->treeid}' AND cu.id = cel.cmsuserid ";
		$arrRsCreated = $objDB->fetch($sqlCreated);
		if($arrRsCreated['num_rows'] == 1) {
			$arrStatus['author'] = $arrRsCreated['data'][0]['name'];
			$arrStatus['created'] = $arrRsCreated['data'][0]['created'];
		} 
		 
		//last modified
		$sqlModified = "SELECT cu.name,date_format(cel.dateadded,'%d/%m-%y %H:%i') as lastmodified FROM cmseventlog cel,cmsuser cu ";
		$sqlModified.= "WHERE cel.cmseventlogtypeid = 3 AND cel.tablename = 'cmstree' AND cel.relationid = '{$this->treeid}' AND cu.id = cel.cmsuserid ";
		$sqlModified.= "ORDER BY dateadded DESC LIMIT 1";
		$arrRsModified = $objDB->fetch($sqlModified);
		if($arrRsModified['num_rows'] == 1) {
			$arrStatus['modifiedby'] = $arrRsModified['data'][0]['name'];
			$arrStatus['lastmodified'] = $arrRsModified['data'][0]['lastmodified'];
		} 
		
		$objTuksiTree = new tuksiTree();
		
		if($arrTree['cmscontextid'] == 2) {
			if(!$arrTree['datepublished'] || !$arrStatus['lastmodified'] || !$objTuksiTree->liveTreeExists($this->treeid)) {
				$arrStatus['status'] = "Side endnu ikke released";
			} elseif(strtotime($arrStatus['lastmodified']) >  strtotime($arrTree['datepublished'] )) {
				$arrStatus['status'] = "Udv side ændret";
			} else {
				$arrStatus['status'] = "Udv og live side er ens";
			}
		}
		$arrStatus['perms'] = "";
		foreach ($this->arrPerms as $key => $value) {
			$arrStatus['perms'].= "$key: $value, ";	
		}
		
		$this->tplMain->assign("pagestatus",$arrStatus); 
	}
	
	
	
	
	function CheckPage() {

		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT c.* FROM pg_contentarea{$this->tableext} c, pg_contentareamodulerel{$this->tableext} r, pg_module{$this->tableext} m ";
		$sql.= " WHERE c.cms_page_templateid = ". $this->page->pagetemplateid ." AND r.cmstreeid = '".$this->objPage->treeid. "' AND r.pg_contentareaid = c.id AND r.pg_moduleid = m.id AND isactive = 1 ";
		$sql.= "  ORDER BY seq";

		$rsMod = $objDB->fetch($sql);

		if ($rsMod['num_rows'] > 0)
			return false;
		else
			return true;

	}
	
	/**
	 * Henter en nodes 2 parent tilbage
	 *
	 * @param int $treeid
	 * @return array
	 */
	function getParent($treeid) {

		$objTree2 = $this->getRow("cmstree{$this->tableext}", $treeid);
		$objTree1 = $this->getRow("cmstree{$this->tableext}", $objTree2->parentid);

		return array($objTree1->parentid, $objTree2->parentid);
	}
	
	
	function cleanUrl($strUrl){
		$strUrl = str_replace("bundmenu/","",$strUrl);
		$strUrl = str_replace("topmenu/","",$strUrl);
		return $strUrl;
	}
	
	/**
	 * Tilføjer knapper der skal vises i contentmenu rammen til $this->arrButtons. Dog vises de kun hvis man
	 * har rettigheder til at se dem
	 *
	 * @param string $button_name
	 * @param string $button_text
	 * @param string $permission_token
	 * @param string $confirm
	 * @param string $onlick
	 */
	function addButton($button_name, $button_text = "", $permission_token= "", $confirm = "", $onclick = "",$alert = "",$type = "normal",$icontype = "Normal",$href = "") {
		
		// check om knappen er banned
		if (!in_array($button_name,$this->arrBannedButtons)){
			$this->arrButtons[] = array(	"name" 							=> $button_name,
																		"text" 							=> $button_text,
																		"permission_token" 	=> $permission_token,
																		"confirm"						=> $confirm,
																		"alert" 						=> $alert,
																		"onclick" 					=> $onclick,
																		"type" 							=> $type,
																		"href" 							=> $href,
																		"icontype"					=> $icontype);
		}
	} // End addButton();
	
	
	function addActionButton($button_name, $button_text = "", $permission_token= "", $confirm = "", $onclick = "",$alert = ""){
		
		$href = '';
		switch ($button_name) {
			case 'BTNRELEASEPAGE':
				if(!$onclick)
					$onclick = "tuksi.pagegenerator.releasePageDialog('".$this->treeid."','".$this->cmsText('releasepage')."');return false;";
				$icontype = "ReleasePage";
				break;
			case 'BTNCOPYPAGE':
				if(!$onclick)
					$onclick = "tuksi.pagegenerator.copyPageDialog('".$this->treeid."','".$this->cmsText('copypage')."');return false;";
				$icontype = "CopyPage";
				break;
			case 'BTNMOVEPAGE':
				if(!$onclick)
					$onclick = "tuksi.pagegenerator.movePageDialog('".$this->treeid."','".$this->cmsText('movepage')."');return false;";
				$icontype = "MovePage";
				break;
			case 'BTNMOVETAB':
				if(!$onclick)
					$onclick = "tuksi.pagegenerator.moveTabDialog('".$this->treeid."','".$this->tabid."','".$this->cmsText('movetab')."');return false;";
				$icontype = "MoveTab";
				break;
			case 'BTNDELETEPAGE':
				if(!$onclick)
					$onclick = "tuksi.pagegenerator.deletePageDialog('".$this->cmsText('deletepage')."');return false;";
				$icontype = "DeletePage";
				break;
			case 'BTNADDPAGE':
				if(!$onclick)
					$onclick = "tuksi.pagegenerator.addPageDialog('".$this->treeid."','".$this->cmsText('addpage')."');return false;";
				$icontype = "AddPage";
				break;
			case 'BTNDEBUG':
				if(!$onclick)
					$onclick = "objTuksi.debug.showDebugPopup();return false;";
				$icontype = "Debug";
				break;
			case 'BTNDEBUGTOGGLE':
				if(!$onclick)
					$onclick = "toggleDebug();return false;";
				$icontype = "Debug";
				break;
			case 'BTNTREEADMIN':
				$arrConf = tuksiConf::getConf();
				$href = $this->getUrl($arrConf['link']['treeadmin_treeid'])."&nodeid=".$this->treeid."&nodetabid=".$this->tabid;
				$icontype = "TreeAdmin";
				break;	
			case 'BTNTPLADMIN':
				$arrConf = tuksiConf::getConf();
				$href = tuksiTools::getBackendUrl($arrConf['link']['pagetemplateadmin_treeid'],$arrConf['link']['frontend_pagetemplateadmin_tabid']);
				$href.= "&rowid=" . $this->arrTree['frontendpagetemplateid'];
				$icontype = "TreeAdmin";
				break;				
			default:
				break;
		}
		
	//{$conf.setup.admin}/?treeid={$link.treeadmin_treeid}&nodeid={$page.treeid}&nodetabid={$page.tabid}
		
		$this->addButton($button_name, $button_text, $permission_token, $confirm, $onclick ,$alert,'action',$icontype,$href);
	}
	
	/**
	 * adds butons to template
	 *
	 */
	function setButtons() {
		
		// array with buttons to show
		$arrShowed = array();
		$arrActionButtons = array();
		
		if (isset($this->arrButtons) && count($this->arrButtons) > 0 && is_array($this->arrButtons)) {
			
			$arrButtons = array();
			
			foreach ($this->arrButtons as &$arrButton) {
				
				$permtoken = str_replace("BTN", "", $arrButton['name']);

				// set class name on button
				switch($arrButton['name']) {
					
					case('BTNSAVE') 			: $arrButton['class'] = 'save'; break;
					case('BTNBACK') 			:	$arrButton['class'] = 'back'; break;
					case('BTNGOBACK') 		: $arrButton['class'] = 'back'; break;
					case('BTNADMIN') 			: $arrButton['class'] = 'admin'; break;
					case('BTNTREEADMIN')	:	$arrButton['class'] = 'admin'; break;
					case('BTNPREVIEW')		: $arrButton['class'] = 'preview'; break;
					case('BTNDEBUG') 			: $arrButton['class'] = 'debug'; break;
					case('BTNADDPAGE')		: $arrButton['class'] = 'add'; break;
					case('BTNCOPYPAGE')		:	$arrButton['class'] = 'copy'; break;
					case('BTNCOPY')				:	$arrButton['class'] = 'copy'; break;
					case('BTNADD') 				:	$arrButton['class'] = 'add'; break;
					case('BTNMOVEPAGE') 	:	$arrButton['class'] = 'move'; break;
					case('BTNMOVETAB') 		:	$arrButton['class'] = 'movetab'; break;
					case('BTNRELEASEPAGE'):	$arrButton['class'] = 'release'; break;
					case('BTNDELTAB') 		:	$arrButton['class'] = 'elete'; break;
					case('BTNDELETE') 		:	$arrButton['class'] = 'delete'; break;
					case('BTNSEND') 			: $arrButton['class'] = 'addarrow'; break;
					default 							:	if (strpos(strtolower($arrButton['name']),"add") > 0) {
																		$arrButton['class'] = 'add';
																	} else {
																		$arrButton['class'] = '';
																	}
				}
				// Try to get text for button
				if (!$arrButton['text'])
					$arrButton['text'] = $this->cmstext(strtolower($arrButton['name']));
		
				// Make sure a button only accrures ones
				if (!isset($arrShowed[$permtoken])) {
					
					//Add button to shoed array
					$arrShowed[$permtoken] = 1;
					if(!$arrButton['href']) {
						if($arrButton['onclick']) {
							if(!preg_match("/;$/",$arrButton['onclick'])) {
								$arrButton['onclick'].= ";";
							}
						}
						if($arrButton['alert']) {
							$arrButton['onclick'].= "alert('" . $arrButton['alert'] . "');return false;";
						} elseif(!$arrButton['onclick']) {
							$arrButton['onclick'].= "doAction('$permtoken'); return false;";	
						}
					}
					if($arrButton['type'] == 'action')
						$arrActionButtons[] = $arrButton;
					else	
						$arrButtons[] = $arrButton;
				}
			} // End foreach buttons
			
			$this->tplMain->assign("buttons", $arrButtons);
			$this->tplMain->assign("actionbuttons",$arrActionButtons);
		}
	} // End addButtonsToTpl();
	
	
	
	private function releasePage($objValues){
		
		$objPage = tuksiBackend::getInstance();
		$tuksiTree = tuksiTree::getInstance();
		$objPagegenerator = tuksiPageGenerator::getInstance();
		
		$arrStatus = $objPagegenerator->releasePage($objPage->treeid);
		
		if($arrStatus['ok']) {
			
			if($objValues->releasePageSubpages) {
				$objPagegenerator->releaseSubpages($objPage->treeid,$arrNew['NEWTREEID'],$arrToCopy);
			}
			
			$arrConf = tuksiConf::getPageConf($objPage->treeid);
			
			$tuksiTree->updateTreeUrl($arrConf['rootid'],true);
			return $arrStatus;
		} else {
			return $arrStatus;
		}
	}
	
	private function copyPage($objValues){
					
		$objPagegenerator = tuksiPageGenerator::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		
		$tuksiTree = tuksiTree::getInstance();
		
		$arrToCopy = $tuksiTree->getAllSubNodes($objPage->treeid);
		
		if(($arrNew = $objPagegenerator->copyPage($objPage->treeid,$objValues->copyPageTreeid,$objValues->copyPageName,$objValues->copyPagePlacement)) !== false) {
			//logger oprettelsen af den nye side
			tuksiLog::treeAction('pagecopied',$arrNew['NEWTREEID'],$objValues->copyPageName);
			
			if($objValues->copyPageSubpages) {
				$objPagegenerator->copySubPages($objPage->treeid,$arrNew['NEWTREEID'],$arrToCopy);
			}
			//sikrer urlparts
			$tuksiTree->updateTreeUrl($arrNew['NEWTREEID']);
			
			return $arrNew;
		} else {
			return false;			
		}
	}
	
	
	public function addPage($objValues){
		
		$objPage = tuksiBackend::getInstance();
		
		$objPagegenerator = tuksiPageGenerator::getInstance();
		
		if(!$objValues->addPageTreeid) {
			$objValues->addPageTreeid = $objPage->treeid;
		}
		if (!$objValues->addPageCopyFromTreeid) {
			$objValues->addPageCopyFromTreeid = $objPage->treeid;
		}
		
		$tuksiTree = tuksiTree::getInstance();
		if(($arrNew = $objPagegenerator->copyPage($objValues->addPageCopyFromTreeid, $objValues->addPageTreeid,$objValues->addPageName,$objValues->addPagePlacement,false)) !== false) {
			tuksiTree::updateTreeUrl($arrNew['NEWTREEID']);
			//need to set delete permission for the new page
			if(count($arrNew['tabs']) > 0) {
				foreach($arrNew['tabs'] as $tabid) {
					$arrGrp = tuksiPerm::getTreeTabUsergrps($arrNew['NEWTREEID'],$tabid);
					//be sure all have the delete option
					foreach ($arrGrp as $arr) {
						if(!$arr['pdelete']){
							tuksiPerm::addTreeTabPerm($arrNew['NEWTREEID'],$tabid,$arr['cmsgroupid'],array(	'read' => $arr['pread'],
																																															'admin' => $arr['padmin'],
																																															'save' => $arr['psave'],
																																															'add' => $arr['padd'],
																																															'delete' => true));
						}
						
					}
				}
			}
			return $arrNew;
		} else {
			false;
		}
	}
	
	private function movePage($objValues){
		
		$objPage = tuksiBackend::getInstance();
		
		$tuksiTree = tuksiTree::getInstance();
		
		$newParent = $objValues->movePageTreeid;
 		$placement = $objValues->movePagePlacement;
		
 		if($tuksiTree->checkSelf($objPage->treeid,$newParent)) {
 		
			if($placement == 2 || $placement == 3) {
				switch ($placement) {
					case 2:$action = "before";break;
					case 3:$action = "after";break;
					default:$action = "before";break;
				}
				// Flyt side enten før eller efter valgte side
				$newParentID = $tuksiTree->moveNode($objPage->treeid,$newParent,$action);

				// Her retures det nye parent ID så vi kan opdatere urlparts i træet.
				if ($newParent > 0) {
					tuksiTree::updateTreeUrl($newParentID);
					return true;
				} else {
					return false;
				}
			} elseif ($placement == 1) {
				// Flyt siden under valgte side. Side smides ind som den første
				if($tuksiTree->moveNodeAsChild($objPage->treeid,$newParent,'first')) {
					tuksiTree::updateTreeUrl($newParent);
					return true;
				} else {
					return false;
				}
			}
 		} else {
 			return false;
 		}		
	}
	private function moveTab($objValues){
	
		$objPage = tuksiBackend::getInstance();
		$tuksiTree = tuksiTree::getInstance();
		
		if($tuksiTree->moveTab($objValues->moveTabId,$objValues->moveTabTreeid,$objValues->moveTabPlacement)) {
					return array(	'treeid' => $objValues->moveTabTreeid,
												'tabid' => $objValues->moveTabId);
		}
		return false;
	}
	
	/**
		* Adds JS function to form onsubmit event
		*
		*/

	function addOnSubmitJS($strJS) {
		$this->arrOnSubmitJS[] = $strJS;
	}	
	
	function getHtml(){
		
		$arrUser = tuksiBackendUser::getUserInfo();
		
		if (isset($arrUser['usergroup']['1'])) {
			$this->addActionButton("BTNCOPYPAGE","","READ");
			$this->addActionButton("BTNMOVEPAGE","","READ");
			$this->addActionButton("BTNMOVETAB","","READ");
			$this->addActionButton("BTNDELETEPAGE","","READ");
			$this->addActionButton("BTNTREEADMIN","","READ");
			
			if($_SESSION['backend_debug_active']) {
				$this->addActionButton("BTNDEBUG","","READ");	
				$this->addActionButton("BTNDEBUGTOGGLE",$this->cmsText('debug_off'),"READ");	
			} else {
				$this->addActionButton("BTNDEBUGTOGGLE",$this->cmsText('debug_on'),"READ");
			}
		}
		
		$this->setButtons();

		if (count($this->arrOnSubmitJS)) 
			$this->tplMain->assign('onsubmit', join('', $this->arrOnSubmitJS));
		
		//load menu
		$objSitemap = new tuksiBackendSitemap($this->treeid);
		$objSitemap->loadMenu();
		
		$arrTopMenu = $objSitemap->getTopMenu($arrUser['id']);
		
		$this->tplMain->assign("topmenu",$arrTopMenu);		
		
		$arrMenu = $objSitemap->getMenu($arrUser['id']);
		$this->tplMain->assign("nodes", $arrMenu);
		
		return parent::getHtml();
	}

} // end class pagegenerator_control
?>
