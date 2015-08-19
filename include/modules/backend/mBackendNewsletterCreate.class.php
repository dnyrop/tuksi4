<?php
/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */
class mBackendNewsletterCreate extends mBackendBase {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		$newLayoutId = $_POST->getStr('layoutid');
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		if ($newLayoutId && $objPage->arrPerms["ADD"]) {
			
			$arrConf = tuksiConf::getConf();
			
			$objPageGenerator = tuksiPageGenerator::getInstance();
			$arrNew = $objPageGenerator->copyPage($arrConf['link']['newsletter_template_treeid'], $objPage->treeid);
			
			$cmssitelangid = $objPage->arrTree['cmssitelangid'];
			//update node with info
			$sqlUpd = "UPDATE cmstree SET pg_page_templateid = '{$newLayoutId}', cmssitelangid = '{$cmssitelangid}' WHERE id = '{$arrNew['NEWTREEID']}' ";
			$objDB->write($sqlUpd);

			$url = tuksiTools::getBackendUrl($arrNew['NEWTREEID']);
			
			header("Location: $url");
			exit();
		}
		
		//hent templates som er af den angivne type
		$sqlPage = "SELECT * FROM pg_page_template WHERE template_type = '2' AND isactive = 1 ORDER BY seq";
		$rsPage = $objDB->fetch($sqlPage);
		if ($rsPage['ok'] && $rsPage['num_rows']) {
		   //sætter html for visning af templates
		   
		   foreach ($rsPage['data'] as &$arrLayout) {
		      //laver urlen for oprettelse af en ny side med templaten
		      $arr[] = array(	'name' => $arrLayout['name'],
					      					'id' => $arrLayout['id'],
					      					'description' => $arrLayout['description'],
					      					'picture' => "/uploads/" . $arrLayout['picture']);
		   }//end while page
		   
		   $this->tpl->assign('layouts',$arr);
		   
		} // end if rows
		
		$returnHtml = parent::getHTML();
		return $returnHtml;
	}
	
	function saveData(){
		
	}
}
?>
