<?php

/**
 * Base page klasse for alle frontend pages.
 *
 * @package tuksiFrontendPage
 */

class pFrontendBase {
	//class for the content area returns with modules
	function __construct () {
		
		$this->objPage = tuksiFrontend::getInstance(); 

		$this->arrTree = $this->objPage->arrTree;
 	 
		$this->tplPage = new tuksiSmarty();
	}

  //return function for html
  function getHtml(){

		$objDB = tuksiDb::getInstance();
		$arrConf = tuksiConf::getConf();
		
		// Hent hver kolonne
		$sqlArea = "SELECT * FROM pg_contentarea{$arrConf['setup']['tableext']} ";
		$sqlArea.= "WHERE pg_page_templateid = '". $this->objPage->arrTree['pagetemplateid'] ."'";
		
		$arrReturn = $objDB->fetch($sqlArea, array('expire' => 360, 'name' => 'Getting content areas'));

		$html = '';
		
		if ($arrReturn['ok']) {

			$arrArea = array();
			foreach ($arrReturn['data'] as &$arrAreaRow) {
				$arrArea[strtolower($arrAreaRow['templatetoken'])] = $this->getContent($arrAreaRow['id'], $arrAreaRow['templatetoken'],$this->objPage->arrTree['id'], $this->objPage->arrTree['pagetemplateid']);
			}
			
			$this->tplPage->assign("area", $arrArea);

			$html = $this->tplPage->fetch("pages/frontend/" . $this->objPage->arrTree['template']);
		}
		
		return $html;
		

	} // End getHtml();

  /**
   * set function for html in the content area
   *
   * @param int $areaid
   * @param string $token
   * @return string HTML content
   */
	function getContent($areaid, $token,$treeid,$templateid){
		
		$objDB = tuksiDb::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$arrModule = array();
	
		// Hent moduler i valgte kolonne
		$sqlMod = "SELECT r.*, m.classname, m.cache_timeout ";
		$sqlMod.= "FROM pg_content{$arrConf['setup']['tableext']} r, pg_module{$arrConf['setup']['tableext']} m ";
		$sqlMod.= "WHERE r.cmstreeid = ".$treeid. " AND r.pg_contentareaid = $areaid AND r.pg_moduleid = m.id AND r.isactive = 1 ";
		$sqlMod.= "ORDER BY r.seq";
	
		$arrReturn= $objDB->fetch($sqlMod, array('type' => 'object', 'expire' => 360, 'name' => 'Getting content areas')) or print mysql_error();
		
		$module_count = 0;
		$module_count_max = 0;
		$content = '';
	
		if ($arrReturn['ok']) {
			$module_count_max = $arrReturn['num_rows'];
	
			if ($module_count_max) {
	
				foreach ($arrReturn['data'] as $objMod) {
					$objMod->count = ++$module_count;
					$objMod->first = $objMod->count == 1;
					$objMod->last = $objMod->count == $module_count_max;
					$objMod->areaid = $areaid;
					$objMod->areaToken = $token;
					$objMod->templateid = $templateid;
					
 					$objMod->currentPlacement = array(	'areaid' => $areaid,
			   																			'token' => $token,
			   																			'type' => $templateid,
			   																			'pagetemplate' => $templateid,
			   																			'numModules' => $module_count_max,
			   																			'seq' => $module_count);	
			   																							
					$objModule = mFrontendBase::getInstance($objMod);
			
					if (is_subclass_of($objModule, 'mFrontendBase')) {
						
						$html= trim($objModule->getHtml());
				
						$content.= $html;
					} else {
						$content .= "Module {$objMod->classname} not loadet<br/>";
					}
				}
			}
		}
	
		$arrModules['count'] = $module_count_max;
		$arrModules['content'] = $content;
	
		return $arrModules;
	
	} // End setContent();
} // End class s_base

?>
