<?php

/**
 * The main page control for the frontend system.
 *
 * @todo PHP doc
 * @package tuksiFrontendPage
 */
class cFrontendMainMySite extends cFrontendBase {

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

		$this->setMainTemplate("controls/frontend/" . __CLASS__ . ".tpl");
		
		$this->treeid = $this->loadSitemapInfo($objSitemap);
		
		$this->arrTree = $this->getPageInformation($this->treeid);

		$this->addHeadline($this->arrTree['title']);
		
		$this->addTitle(' : ' . $this->arrTree['title']);

		if (!empty($this->arrTree['pg_metakeywords'])) {
			$this->addMetaKeyword($this->arrTree['pg_metakeywords']);
		}

		if (!empty($this->arrTree['pg_metadescription'])) {
			$this->addMetaDescription($this->arrTree['pg_metadescription']);
		}

		// Indsætter menu udfra sitemap struktur
		$this->tplMain->assign("menu", $this->arrSitemap);
		
		// Henter side fra pagegenerator system
		$this->loadPage();

		// Laver stier til navigeringen 
		for ($i = 1; $i< count($this->arrTreeObjs) - 1; $i++) {
			$url = "/".$this->arrTreeObjs[$i]['pg_urlpart_full'];
			$url = $this->cleanUrl($url);
			$this->arrPath[] = array('name' => $this->arrTreeObjs[$i]['menuname'], 'url' => $url);
		}
		
	} // End pagegenerator_control();

	

} // end class pagegenerator_control
?>
