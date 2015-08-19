<?
class pNewsletterBase {
	
	private $arrNewsletter = array();
	
	//class for the content area returns with modules
	function __construct(){
		
		$this->tpl = new tuksiSmarty();
	
	}
	
	function getText(){
		return $this->getAreas(false);
	}
	
	//return function for html
	function getHtml(){
		return $this->getAreas();
	}
	
	function getAreas($ishtml = true){
		
		$objPage = tuksiNewsletter::getInstance();
		$arrConf = tuksiConf::getPageConf($objPage->treeid);
		$objDB = tuksiDB::getInstance();
		$this->useUTF8 = $arrConf['mail_encoding'] == 'utf8';

		$this->tpl->assign('useUTF8', $this->useUTF8);
		
		//load links for newsletter
		$sqlLinks = "SELECT * FROM mail_link ";
		$sqlLinks.= "WHERE pg_page_templateid = '{$objPage->arrTree['pg_page_templateid']}' AND cmssitelangid = '{$objPage->arrTree['cmssitelangid']}'";
		$rsLinks = $objDB->fetch($sqlLinks);
				
		if ($rsLinks['ok'] && $rsLinks['num_rows'] > 0) {
			
			foreach ($rsLinks['data'] as &$arrLink) {
				
				$baseUrl = "http://" . $arrConf['url_site'];
				if (strlen($arrLink['url'])) {
					$baseUrl .= "/mail_redirect_link/{$arrLink['id']}.[TRACKINGID]/";
				}
				$objPage->addLink($arrLink['token'], $baseUrl);
			
			}	
			$this->tpl->assign("newsletter_link", $objPage->getLinks());
		}
		
		$sqlArea = "SELECT * FROM pg_contentarea ";
		$sqlArea.= "WHERE pg_page_templateid = " . $objPage->arrTree['pg_page_templateid']. " ORDER BY seq";
		$rsArea = $objDB->fetch($sqlArea);
		foreach ($rsArea['data'] as &$arrArea) {
			//set function for the different areas
			$this->setContent($arrArea['id'], $arrArea['templatetoken'],$ishtml);
		}
	 	
		return $this->tpl->fetch($objPage->arrTree['template']);
	}
	
	//set function for html in the content area
	function setContent($areaid, $token,$ishtml){
	
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiNewsletter::getInstance();
		
		$sqlMod = "SELECT c.*, m.classname FROM pg_content c, pg_module m ";
		$sqlMod.= "WHERE c.cmstreeid = ".$objPage->treeid. " AND c.pg_contentareaid = $areaid AND c.pg_moduleid = m.id AND c.isactive = 1 ";
		$sqlMod.= "ORDER BY c.seq";
		
		$rsMod = $objDB->fetch($sqlMod,array('type' => 'object'));
		
		$content = "";
		
		foreach($rsMod['data'] as $objMod){
		
			if (preg_match("/^(.*).tpl$/", $objMod->classname, $m)) {
				$template = $m[1];
				$objMod->classname = "mNewsletterBase";
			}else{
				$template = "standard";
			}
			$status = tuksiTools::loadClass(dirname(__FILE__)."/../../modules/newsletter/" . $objMod->classname . ".class.php", $objMod->classname); 
			
			if (!$status){
				$objContent = new $objMod->classname($objMod,$template);
				$objContent->useUTF8 = $this->useUTF8;
				if($ishtml) {
					$content.= $objContent->getHtml();
				} else {
					$content.= $objContent->getText();
				}
				
				// In newsletter we need full url, so we append site url
				if (preg_match_all("/\"(\/newsletter\/downloads\/\d+\/)/", $content, $m)) {
					$arrConf = $objConf->getConf();
					
					if (count($m)) {
						foreach ($m[1] as $link) {
							$content = str_replace($link, $arrConf['url_site'] . $link, $content);
						}
					}
				}
			} else {
				$content.= $status;
			}
		}
		$this->tpl->assign($token, $content);
	}
}
?>
