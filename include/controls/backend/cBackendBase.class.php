<?
/**
 * Enter description here...
 *
 * @todo PHP Doc missing
 * @package tuksiBackendPage
 */
class cBackendBase extends cBase {
	
	public $arrConf;
	
	private $arrStatusMessages = array();
	
	// Indeholder website tekster
	public $arrTabs = array();
	
	public $arrHistory = array();
	
	public $arrBannedButtons = array();
	
	public $objTopmenu, $objMenu, $topMenuId;
	
	function __construct() {
	
		parent::__construct();
		
		tuksiBackend::$instance = $this;
		
		//Loading backend configuration
		/*$conf = tuksiConfig::getInstance();

		if(($this->arrConf = $conf->getbackendConf()) === false) {

			die('could load backend conf');			
		
		} else {
			$this->sitelang = $this->arrConf['lang'];
		}
		
		$objDebug = tuksiDebug::getInstance();
		
		if($objDebug->isActive()) {
			tuksiDB::setDebug();
			$objDebug->log("Site status", "Udviklingssite");
			//rettes igennem
		}
		*/
		$this->addJavascript("/javascript/backend/libs/base.js");
		$this->addJavascript("/javascript/backend/libs/prototype.js");
		$this->addJavascript("/javascript/backend/libs/scriptaculous/scriptaculous.js?load=builder,effects,dragdrop,controls");
		$this->addJavascript("/javascript/backend/tuksi.js");
		$this->addJavascript("/javascript/backend/libs/tuksi.util.js");
		$this->addJavascript("/javascript/backend/libs/tuksi.window.js");
		$this->addJavascript("/javascript/backend/libs/tuksi.pagegenerator.js");
		$this->addJavascript("/javascript/backend/libs/jquery.js");

		$this->loadFieldtypeJavascript();
	}

	/**
	 * loadFieldtypeJavascript: Load fieldtype javascript files in the javascript array  
	 * 
	 * @access public
	 * @return void
	 */
	function loadFieldtypeJavascript() {
		$objDB = tuksiDB::getInstance();
		$sql = "SELECT js_include FROM cmsfieldtype WHERE js_include IS NOT null";

		$arrReturn = $objDB->fetch($sql, array('expire' => 180));

		if ($arrReturn['num_rows']) {
			foreach ($arrReturn['data'] as $arrJs) {
				if ($arrJs) {
					$arrJsFiles = explode(';', $arrJs['js_include']);
					foreach ($arrJsFiles as $jsFile) {
						$this->addJavascript($jsFile);
					}
				}
			}
		}
		
	}

	/**
	 * Bestemmer hvilket treeID der skal vises
	 * Sætter $this->treeid, $this->previewMode og måske $this->arrPage['isfrontpage']
	 */
	function setPage() {
		
		$objDebug = tuksiDebug::getInstance();
		$objUser = tuksiBackendUser::getInstance();
		
		// Henter tree obj fra 
		$this->treeid = $_GET->getInt('treeid');

		// Hvis treeid ikke er fundet, sæt treeid til forside ID
		if (!$this->treeid || $this->treeid == $this->arrConf['rootid']) {
			$this->treeid = tuksiIni::$arrIni['setup']['default_backend_treeid'];
			$this->isFrontpage();
		}

		$this->tabid = $_GET->getInt('tabid');
		$this->rowid = $_GET->getInt('rowid');
		
		$objDB = tuksiDB::getInstance();
		
		//get first tab for current tree id
		$sqlTab = "SELECT DISTINCT t.* ";
		$sqlTab.= "FROM cmstreetab t, cmsperm p, cmsusergroup ug ";
		$sqlTab.= "WHERE p.pread = 1 AND p.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = {$objUser->getUserID()} ";
		$sqlTab.= "AND p.cmstreetabid = t.id AND p.itemtype = 'tree' AND t.cmstreeid = '{$this->treeid}' ";
		
		if($this->tabid) {
			$sqlTab.= "AND t.id = '{$this->tabid}' ";
		}
		
		$sqlTab.= "ORDER BY t.seq LIMIT 1";

		$arrTab = $objDB->fetch($sqlTab);
		
		//die($sqlTab);
		if($arrTab['num_rows'] == 1) {
			
			$this->tabid = $arrTab['data'][0]['id'];
			$this->arrTab = $arrTab['data'][0];
			
			//load all permission for the current tab and user
			$this->arrPerms = tuksiPerm::getTreeTabUserPerms($objUser->getUserID(), $this->treeid,$this->tabid);
			
			//load current tabs
			$sqlTab = "SELECT DISTINCT t.*,ct.value_".tuksiIni::$arrIni['setup']['admin_lang']." as langname ";
			$sqlTab.= "FROM (cmstreetab t, cmsperm p, cmsusergroup ug) ";
			$sqlTab.= "LEFT JOIN cmstext ct ON (ct.token = t.name) ";
			$sqlTab.= "WHERE p.pread = 1 AND p.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = {$objUser->getUserID()} ";
			$sqlTab.= "AND p.cmstreetabid = t.id AND p.itemtype = 'tree' AND t.cmstreeid = '{$this->treeid}' ";
			$sqlTab.= "ORDER BY t.seq ";
			$rsTab = $objDB->fetch($sqlTab);
			
			if($rsTab['num_rows'] > 1) {
				foreach ($rsTab['data'] as $arrTab) {
					$isactive = false; 
					if($arrTab['id'] == $this->tabid) {
						$isactive = true;
					}
					if($arrTab['langname'])
						$arrTab['name'] = $arrTab['langname'];
					
					$this->addTab($arrTab['name'],$this->getUrl($this->treeid,$arrTab['id']),$isactive);
				}
			}
		} else {
		
			$arrConf = tuksiConf::getConf();
			$url = $this->getUrl($arrConf['setup']['default_backend_treeid']);
			//error_log('url: ' . $url . '-' . $sqlTab);
			header("Location: $url");
			exit();
		}
		
		$objDebug->log($this->treeid,"TreeID");
	}
	
	/**
	 * Henter side information på valgte treeid ($this->treeid).
	 *
	 * @return object Returnere object med værdier fra cmstree  tabel.
	 */
	function getPageInformation($treeid) {

		$objDebug = tuksiDebug::getInstance();
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT t.id, t.parentid, t.name, cfs.id AS pagetemplateid, t.pg_page_templateid AS frontendpagetemplateid, tt.name AS menuname, tt.name AS title, cfs.classname AS classname, ";
		$sql.= "t.cmscontextid, t.pg_urlpart_full AS url, t.cmssitelangid, cl.cmssiteid, tt.backendhook AS hook, ct.value_".tuksiIni::$arrIni['setup']['admin_lang']." AS langname ";
		$sql.= "FROM cmstree t ";
		$sql.= "INNER JOIN cmstreetab tt ON (t.id = tt.cmstreeid) ";
		$sql.= "INNER JOIN pg_page_template cfs ON (tt.cms_page_templateid = cfs.id) ";
		$sql.= "LEFT JOIN cmssitelang cl ON (t.cmssitelangid = cl.id) ";
		$sql.= "LEFT JOIN cmstext ct ON (ct.token = tt.name) ";
		$sql.= "WHERE t.id = '{$this->treeid}' AND tt.id = '{$this->tabid}'";

		$arrReturn = $objDB->fetch($sql, array('cache' => 1, 'expire' => 10));

		if ($arrReturn['num_rows']) {
			$arrTree	= $arrReturn['data'][0];
			if ($arrTree['langname']) {
				$arrTree['menuname'] = $arrTree['langname'];
				$arrTree['title'] = $arrTree['langname'];
			}
			$objDebug->log($arrTree['title'],"Tree loaded");
			return $arrTree;
		} else {
			$objDebug->warning("NO treeID","Treeid not found:");
			return false;
		}

	} // End getPageInformation()
	/**
	 * Loader page skabelon kode via information from database fra $this->arrTree.
	 *
	 */
	function loadPage() {
		
		$objDebug = tuksiDebug::getInstance();
		
		if (is_array($this->arrTree)) {
			// Check if Classname have template extension (.tpl)
			// If template, default classname is replaced with "pBase"
			
			if (preg_match("/.tpl$/", $this->arrTree['classname'], $m)) {
				$this->arrTree['template']= $this->arrTree['classname'];
				$this->arrTree['classname'] = "pBase";
			} else {
				$this->arrTree['template'] = $this->arrTree['classname'] . ".tpl";
			}
			
			$objDebug->log($this->arrTree['classname'] . ", Template = " . $this->arrTree['template'],"Page class:");

			$class_filename = dirname(__FILE__) . "/pages/" . $this->arrTree['classname'] . ".class.php";

			$status = tuksiTools::loadClass($class_filename, $this->arrTree['classname']);
			
			if (!$status) {
				$this->objPageTemplate = new $this->arrTree['classname']();
				$this->putContent($this->objPageTemplate->getHtml());
			} else {
				$this->debug->warning($this->arrTree['classname'] . ".class.php",$status);
				$this->putContent($status . " (pages/{$this->arrTree['classname']}.class.php)");
			} // End if load class OK
	
		} // End if arrTree is_object
		 else {
		 	$objDebug->warning($this->cmsText('pagelayout_notfound'));
		 	//link to setup
		 	
		 	
		 	$link = $this->getUrl($this->arrConf['link']['treeadmin_treeid']);
		 	$link.= "&nodeid=" . $this->treeid;
			$this->putContent("Sideskabelon ikke fundet. <a href='$link'>setup</a>");
		 }


	} // End loadPage()
	
	
	function getCurrentUrl(){
		$url = "/" . tuksiIni::$arrIni['setup']['admin'] . "/?treeid=" . $this->treeid;
		
		if($this->tabid)
			$url.= "&tabid=".$tabid;
			
		return $url;
	}
	
	function getUrl($treeid = '',$tabid = 0){
		
		$url = "/" . tuksiIni::$arrIni['setup']['admin'] . "/?treeid=" . $treeid;
		if($tabid > 0) {
			$url.= "&tabid=".$tabid;
		}
		return $url;
	}
	
	function addTab($name,$url,$isactive = false){
		$this->arrTabs[] = array(	'name' => $name,
															'url' => $url,
															'isactive' => $isactive);
	}
	
	function setTabs(){
		$arrTabs = array_reverse($this->arrTabs);
		$this->tplMain->assign('tabs',$arrTabs);
	}
	

	/**
	 * Returnere tekster tilknyttet fil 
	 *
	 * @return array 
	 */
	
	function getCmsTexts() {
		return $this->CMSTEXT;
	}
	
	// * ------------------------------------------------------------------------- *
	// Get script text by string
	// * ------------------------------------------------------------------------- *
	
	function cmsText($str) {
		$objText = tuksiText::getInstance();
		return $objText->getText($str);
	}
	
	//parse str with config and settings
	
	public function parseStr($str){
		
		if(is_array($this->arrConf) && count($this->arrConf) > 0) {
			foreach ($this->arrConf as $key => $mixedVal) {
				if(is_array($mixedVal)) {
					foreach ($mixedVal as $seckey => $value) {
						if(is_array($mixedVal)) {
							$str = str_replace("#$key.$seckey#",$value,$str);
						}
					}
				} else {
					$str = str_replace("#$key#",$mixedVal,$str);
				}
			}
		}
		return $str;
	}
	
	public function status($message){
		if(is_object($message) || is_array($message)) {
			$message = print_r($message,1);
		}
		$this->arrStatusMessages[] = array(	'message' => $message,
																				'type' => 'correct',
																				'nb' => count($this->arrStatusMessages));
		
		$this->tplMain->assign("statusMessage",$this->arrStatusMessages);
	}
	
	public function alert($message){
		if(is_object($message) || is_array($message)) {
			$message = print_r($message,1);
		}
		$this->arrStatusMessages[] = array(	'message' => $message,
																				'type' => 'error',
																				'nb' => count($this->arrStatusMessages));
		
		$this->tplMain->assign("statusMessage",$this->arrStatusMessages);
	}
	
	private function setBreadcrumb(){
		$arrBreadCrumb = array();
		
		if(isset($this->arrCurrentNode['parentids']) && count($this->arrCurrentNode['parentids']) > 0) {
			$arrBreadCrumb = $this->makeBreadcrumb($this->arrSitemap);
		}	
		
		$this->tplMain->assign('breadcrumb',$arrBreadCrumb);
	}
	
	private function makeBreadcrumb($arrNodes){
		
		$arrBreadCrumbs = array();
		
		foreach($arrNodes as $arrNode) {
			if(in_array($arrNode['id'],$this->arrCurrentNode['parentids']) || $arrNode['id'] == $this->treeid) {
				
				$arrBreadCrumbs[] = $arrNode;
				
				if(is_array($arrNode['nodes'])) {
					$arrNewBreadCrumb = $this->makeBreadcrumb($arrNode['nodes']);
					$arrBreadCrumbs = array_merge($arrBreadCrumbs,$arrNewBreadCrumb);
				}
				
			}
		}
		
		return $arrBreadCrumbs;
	}
	
	public function initHistory(){
		if(isset($_SESSION['USERHISTORY']) && is_array($_SESSION['USERHISTORY'])) {
			$this->arrHistory = $_SESSION['USERHISTORY'];	
		}
	}

	public function getLastHistory(){
		if(count($this->arrHistory) > 0) {
			$arrTmp = array_reverse($this->arrHistory);
			return $arrTmp[0];
		}
		return array('treeid' => '','title' => '','url' => '');
	}
	
	public function addHistory($treeid,$title){
		
		$arrLastHis = $this->getLastHistory();
		
		if($arrLastHis['treeid'] != $treeid) {
			$arrHis = array('title' => $title,
											'treeid' => $treeid,
											'url' => $this->getUrl($treeid));
			$_SESSION['USERHISTORY'][] = $arrHis;
			$this->arrHistory[] = $arrHis;
		}
	}
	
	public function	setHistory(){
		//get user prev histori
		$arrTmpHis =  array_slice(array_reverse($this->arrHistory),1,10);
		$this->tplMain->assign('history',$arrTmpHis);
	}
	
	
	/**
	 * Kører end() i parent klasse, og udskriver Debug information, samt HTML.
	 *
	 * @param bool $boolPrintContent Fortæller om HTML skal printes eller returneres.
	 * @return string Returnere HTML pr. default.
	 */
	function getHtml() {
		

		$objDebug = tuksiDebug::getInstance();
		
		if($objDebug->isActive()) {
		
			$arrConf = tuksiConf::getConf();
			
			$objDebug->log(join(", ", array_keys($arrConf['setup'])),"Site vars");
			$objDebug->log(join(", ", array_keys($this->arrPage)),"Page vars");
			
			//$objDebug->log(join(", ", array_keys($this->arrUser)),"User vars");
			
			if ($this->tplMain) {
				
				$tplDebug = new tuksiSmarty();
				
				$_SESSION['debug'] = $objDebug->fetch();
				
				$tplDebug->assign("tuksi_debug", $_SESSION['debug']);
				
				$this->addJavascript("/javascript/backend/libs/tuksi.debug.js");
				
				$this->tplMain->assign("tuksi_debug", $tplDebug->fetch('debug.tpl'));
				$this->tplMain->assign('loaddebug',true);
			}
		}
		
		$this->setTabs();
		$this->setHistory();
		
		$this->setBreadcrumb();

		$this->tplMain->assign("treeid", $this->treeid);
		//$this->tplMain->assign("page", $this->arrPage);
		$this->tplMain->assign("tree", $this->arrTree);
		
		return parent::getHTML();
	} // End end();
}
?>
