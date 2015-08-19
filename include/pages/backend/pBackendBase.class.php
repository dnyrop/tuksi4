<?php

/**
 * pBackendBase 
 * 
 * @package tuksiBackendPage 
 * @author Henrik Jochumsen <hjo@dwarf.dk> 
 */
class pBackendBase {
	//class for the content area returns with modules
	function __construct () {

		$this->tplPage = new tuksiSmarty();
	}

  //return function for html
  function getHtml(){
		
  	$objPage = tuksiBackend::getInstance();
  	
 		$objDB = tuksiDB::getInstance();
		
  	// Hent hver kolonne
		$sqlArea = "SELECT * FROM pg_contentarea ";
		$sqlArea.= "WHERE pg_page_templateid = '". $objPage->arrTree['pagetemplateid'] ."'";
		
		$arrReturn = $objDB->fetch($sqlArea);
	
		$html = '';
		
		if ($arrReturn['ok']) {
	
			$arrArea = array();
			foreach ($arrReturn['data'] as &$arrAreaRow) {
				$arrArea[strtolower($arrAreaRow['templatetoken'])] = $this->setContent($arrAreaRow['id'], $arrAreaRow['templatetoken']);
			}
			
			$this->tplPage->assign("area", $arrArea);
			
			$this->tplPage->assign("treeid", $objPage->treeid);
			
			$html = $this->tplPage->fetch("pages/backend/" . $objPage->arrTree['template']);
			
		}
		return $html;
		

	} // End getHtml();

	function getBaseHtml($html){
		
		$objPage = tuksiBackend::getInstance();
		$tplBase = new tuksiSmarty();
	
		$tplBase->assign('content',$html);
		
		$baseHtml = $tplBase->fetch('pages/backend/' . __CLASS__ . '.tpl');
		
		return $baseHtml;
	
	}
	

  //set function for html in the content area
	function setContent($areaid, $token){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		$arrModule = array();

		tuksiDebug::log("Loading content area: ", $token);
		
		// Hent moduler i valgte kolonne
		$sqlMod = "SELECT c.*, m.classname FROM pg_content c, pg_module m ";
		$sqlMod.= " WHERE c.cmstreeid = ".$objPage->treeid. " AND c.cmstreetabid = '".$objPage->tabid."' AND ";
		$sqlMod.= "c.pg_contentareaid = $areaid AND c.pg_moduleid = m.id AND c.isactive = 1 ";
		$sqlMod.= " ORDER BY c.seq ";

		$arrReturn= $objDB->fetch($sqlMod, array('type' => 'object')) or print mysql_error();

		$module_count = 0;
		$module_count_max = 0;
		$content = '';

		if ($arrReturn['ok']) {
			$module_count_max = $arrReturn['num_rows'];

			if ($module_count_max) {

				foreach ($arrReturn['data'] as $objMod) {
					$objMod->count = ++$module_count;
					$objMod->areaid = $areaid;
					$objMod->areaToken = $token;
					$objMod->templateid = $objPage->arrTree['pagetemplateid'];

			   	$objMod->currentPlacement = array(	'areaid' => $areaid,
			   																			'token' => $token,
			   																			'type' => $objPage->arrTree['pagetemplateid'],
			   																			'pagetemplate' => $objPage->arrTree['pagetemplateid'],
			   																			'numModules' => $module_count_max,
			   																			'seq' => $module_count);

					
					$objModule = mBackendBase::getInstance($objMod);
					
					if (is_subclass_of($objModule, 'mBackendBase')) {
						
						$html= trim($objModule->getHtml());
				
						$content.= $html;
					} else {
						$content .= "Elementet {$objMod->classname} kunne ikke loades";
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
