<?php
class	mFrontendModuleOverview extends mFrontendBase {

	//return the html for the module
	function __construct(&$objMod){
		parent::__construct($objMod);
		$this->tpl = new tuksiSmarty();
	}
	/**
	 * Henter HTML
	 */

	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiFrontend::getInstance();
		
		$arrConf = tuksiConf::getConf();
		
		$sqlGroups = "SELECT id, name ";
		$sqlGroups.= "FROM cmstree{$arrConf['setup']['tableext']} ";
		$sqlGroups.= "WHERE parentid = '{$objPage->treeid}'";
		$rsGroups = $objDB->fetch( $sqlGroups );
		
		$arrModules = array();

		if( $rsGroups['ok'] && $rsGroups['num_rows'] > 0 ) {
					
			for($i = 0; $i < $rsGroups['num_rows']; $i++) {
						
				$sqlModules = "SELECT name, pg_urlpart_full ";
				$sqlModules.= "FROM cmstree{$arrConf['setup']['tableext']} ";
				$sqlModules.= "WHERE parentid = '{$rsGroups['data'][$i]['id']}' AND pg_isactive = '1'";
				
				$rsModules = $objDB->fetch( $sqlModules );
				
				if( $rsModules['ok'] && $rsModules['num_rows'] > 0 ) {
					
					$arrSubModules = array();
					for($x = 0; $x < $rsModules['num_rows']; $x++) {

						if (!empty($rsModules['data'][$x]['pg_teaser'])) {
							$teaser = $rsModules['data'][$x]['pg_teaser'];
						} else {
							$teaser = '';
						}
						
						$arrSubModules[] = array(
							'name' => $rsModules['data'][$x]['name'],
							'teaser' => $teaser, 
							'link' => $rsModules['data'][$x]['pg_urlpart_full']
						);
					
					}	
					
					$arrModules[] = array(
						'name' => $rsGroups['data'][$i]['name'],
						//'icon' => $rsGroups['data'][$i]['pg_icontype'],
						'modules' => $arrSubModules
					);
					
				}
			}
		}
		
		$this->tpl->assign( "modules", $arrModules );
		
		return parent::getHTML();			
	}
}
?>
