<?php

/**
 * The main page control for the frontend system.
 *
 * @todo PHP doc
 * @package tuksiFrontendPage
 */
class cFrontendMain extends cFrontendBase {

	/**
	 * Contains the sitemap used for the menus
	 *
	 * @var array
	 */
	var $arrSitemap = array();

	/**
	 * Contains urlparts for the current page.
	 *
	 * @var array
	 */
	var	$arrUrlParts = array();

	/**
	 * Contains alle tree object found via urlparts
	 *
	 * @var unknown_type
	 */
	var $arrTreeObjs	= array();

	// Indeholder side browser titler for alle urlparts
	/**
	 * Contains titles for alle tree objects in urlparts
	 *
	 * @var array
	 */
	var $arrTreeTitles = array();

	/**
	 * Page preview mode. Show website in dev mode
	 *
	 * @var bool
	 */
	var $previewMode = false;
	
	/**
	 * contructor for cFrontendMain.
	 * 
	 * Needs a sitemap object.
	 *
	 * @param object $objSitemap
	 */
	function __construct($objSitemap) {
		
		parent::__construct();
		
		$arrConf = tuksiConf::getConf();

		$this->setMainTemplate("controls/frontend/" . __CLASS__ . ".tpl");
		
		$this->treeid = $this->loadSitemapInfo($objSitemap);
		
		$this->arrTree = $this->getPageInformation($this->treeid);	
		
		$this->addHeadline($this->arrTree['title']);
		
		if (strlen($arrConf['site']['langtitle'])) {
			$this->addTitle($arrConf['site']['langtitle'], true);
		}
		
		$this->addTitle(' : ' . $this->arrTree['title']);
		
		if (!empty($this->arrTree['pg_metakeywords'])) {
			$this->addMetaKeyword($this->arrTree['pg_metakeywords']);
		} else {
			$this->addMetaKeyword($arrConf['site']['langmeta_keywords']);
		}

		if (!empty($this->arrTree['pg_metadescription'])) {
			$this->addMetaDescription($this->arrTree['pg_metadescription']);
		}else {
			$this->addMetaDescription($arrConf['site']['langmeta_description']);
		}
		


		// Indsætter menu udfra sitemap struktur
		$this->tplMain->assign("menu", $this->arrSitemap);
		
		// Henter side fra pagegenerator system
		$this->loadPage();

		// Laver stier til navigeringen 
		for ($i = 1; $i< count($this->arrTreeObjs) - 1; $i++) {
			$url = "/".$this->arrTreeObjs[$i]['pg_urlpart_full'];
			$this->arrPath[] = array('name' => $this->arrTreeObjs[$i]['menuname'], 'url' => $url);
		}
		
	} // End pagegenerator_control();

	
	

} // end class pagegenerator_control
?>
