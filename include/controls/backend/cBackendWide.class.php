<?php

/**
 * Setup a page in the sytems including:
 * -Checking user permission
 * -Loading/building the menu
 * -Building the breadcrumb
 * -Loading page modules
 * -Loading general page information
 *
 * @package Tuksi
 */
class cBackendWide extends cBackendBase {

	
	function __construct() {
		parent::__construct();
			
		$this->setMainTemplate("controls/backend/" . __CLASS__ . ".tpl");
				
		$objTuksiUser =  tuksiBackendUser::getInstance();
		
		if ($objTuksiUser->isLogged()){
			header("Location: /" . tuksiIni::$arrIni['setup']['admin'] . "/");
			exit();
		}
		$this->setPage();
		
		$this->arrTree = $this->getPageInformation($this->treeid);
		
		// Henter side fra pagegenerator system
		$this->loadPage();
	}
}

?>