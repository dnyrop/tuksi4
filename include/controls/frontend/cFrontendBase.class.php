<?php

/**
 * Alle page controls uses this for basic functions used on all frontend pages.
 * 
 * @package tuksiFrontendPage
 *
 */
class cFrontendBase extends cBase {
	
	public $previewMode = false;

	function __construct() {
		
		parent::__construct();

		tuksiFrontend::$instance = $this;
		
		//$this->addJavascript("/javascript/frontend/libs/base.js");
		//$this->addJavascript("/javascript/frontend/tuksi.js");
		//$this->addJavascript("/javascript/frontend/libs/tuksi.util.js");

	}
	
	/**
	 * Returner htmlindhold
	 *
	 * @return html
	 */

	function getHTML() {
		
		$tplDebug = new tuksiSmarty();

		//print_r(tuksiIni::$arrIni);
		
		if (tuksiIni::$arrIni['debug']['active']) {
			
			$objDebug = tuksiDebug::getInstance();
        
			$arrDebug = $objDebug->fetch();
			
			$arrDebug['info']['cache'] = tuksiIni::$arrIni['cache']['active'] ? 'Yes' : 'No';
			$arrDebug['info']['emulateprod'] = tuksiIni::$arrIni['setup']['emulatelive'] ? 'Yes' : 'No';
			$arrDebug['info']['treeid'] = $this->treeid;
			
			$_SESSION['frontend_debug'] = $arrDebug;
			
    	$tplDebug->assign("tuksi_debug", $arrDebug);
    
   		$this->addJavascript("/javascript/frontend/libs/tuksi.debug.js");
    
			$this->tplMain->assign("loaddebug", true);
			$this->tplMain->assign("tuksi_debug", $tplDebug->fetch('debug_frontend.tpl'));
		}
		
		$objGA = new tuksiGoogleAnalytics();
		
		$this->tplMain->assign('google_analytics', $objGA->getHTML());
		
		return parent::getHTML();
	}
	
	/**
	 * Henter side information på valgte treeid ($this->treeid).
	 *
	 * @return object Returnere object med værdier fra cmstree  tabel.
	 */
	function getPageInformation($treeid) {

		$objDB = tuksiDB::getInstance();
		
		if (!$this->previewMode) {
			$sqlWhere = "AND t.pg_isactive = 1";
		} else
			$sqlWhere = "";

		$sql = "SELECT t.id, cfs.id AS pagetemplateid, t.pg_menu_name AS menuname, t.pg_browser_title AS title, cfs.classname AS classname, t.pg_urlpart, t.pg_urlpart_full, pg_metakeywords, pg_metadescription ";
		$sql.= " FROM cmstree" . tuksiIni::$arrIni['setup']['tableext'] . " t, pg_page_template" . tuksiIni::$arrIni['setup']['tableext'] . " cfs ";
		$sql.= " WHERE t.id = '{$treeid}' $sqlWhere  AND cfs.id = t.pg_page_templateid";

		$arrReturn = $objDB->fetch($sql, array('cache' => 1, 'expire' => 10));

		if ($arrReturn['num_rows']) {
			$arrTree	= $arrReturn['data'][0];
			$this->setPageArray($arrTree);

			return $arrTree;
		} else {
			// $this->debug->warning("NO treeID","Treeid not found:");
			return false;
		}

	} // End getPageInformation()

	/**
	 * Loader page skabelon kode via information from database fra $this->arrTree.
	 *
	 */
	function loadPage() {
		
		if (is_array($this->arrTree)) {

			// Check if Classname have template extension (.tpl)
			// If template, default classname is replaced with "pBase"
			if (preg_match("/.tpl$/", $this->arrTree['classname'], $m)) {
				$this->arrTree['template']= $this->arrTree['classname'];
				$this->arrTree['classname'] = "pFrontendBase";
			} else {
				$this->arrTree['template'] = $this->arrTree['classname'] . ".tpl";
			}
		
			$this->objPageTemplate= new $this->arrTree['classname']();
			$this->putContent($this->objPageTemplate->getHtml());


		} // End if arrTree is_object
		 else {
		 	//$this->debug->warning("Sideskabelone ikke fundet.");
			$this->putContent("Sideskabelon ikke fundet." . $this->arrTree['id']);
			
		 }


	} // End loadPage()
	
	function loadSitemapInfo($objSitemap) {
		$this->objSitemap = $objSitemap;

		$this->arrSitemap = $this->objSitemap->getSitemap();
		
		$this->arrUrlParts = $this->objSitemap->getUrlParts();

		$this->arrTreeObjs = $this->objSitemap->getTreeObjs();

		$this->arrTreeTitles = $this->objSitemap->getTreeTitles();
		
		$this->previewMode = $this->objSitemap->getPreviewMode();
	
		$treeid = $this->objSitemap->getTreeid();
		
		return $treeid;
	}
}

?>
