<?

/**
 * mFrontendNewslist
 * 
 * value1 = treeid med nyhederne
 *
 * @uses tuksiDebug
 * @uses tuksiSmarty
 * 
 * @package tuksiFrontend
 * 
 */

class mFrontendNewslist extends mFrontendBase {

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

		$loadNewslist = true;
		
		$arrUrlParts = array_reverse($objPage->arrUrlParts);
		$currentUrlpart = $arrUrlParts[0];

		$baseurl = str_replace('.html', '', $objPage->arrTree['pg_urlpart_full']);

		//take care of language
		if($s = strpos($baseurl,"/")) {
			$baseurl = substr($baseurl,$s+1);
		}
		
		//ckeck if we should show a news
		if($baseurl != $currentUrlpart) {
			
			//see if we can find the news
			$sqlTree = "SELECT t.*,a.templatetoken as token, a.id as areaid ";
			$sqlTree.= "FROM cmstree{$arrConf['setup']['tableext']} t, pg_contentarea a ";
			$sqlTree.= "WHERE a.pg_page_templateid = t.pg_page_templateid AND t.pg_urlpart = '$currentUrlpart' AND t.parentid = '".$this->objMod->value1."' and t.pg_isactive = 1 AND isdeleted = 0 ";
			
			$arrRs = $objDB->fetchItem($sqlTree);
			
			if($arrRs['ok'] && $arrRs['num_rows'] == 1) {
				$loadNewslist = false;
				$arrTree = $arrRs['data'];
				$arrContent = pFrontendBase::getContent($arrTree['areaid'],$arrTree['token'],$arrTree['id'],$arrTree['pg_page_templateid']);
				return $arrContent['content'];
			}
		}
		$arrList = $this->getData($this->objMod->value1);

		$this->tpl->assign('baseurl', $baseurl);
		$this->tpl->assign("arrList", $arrList);

		return parent::getHTML();
	}
	
	function getData($treeid) {
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$sql = "SELECT id, pg_menu_name AS name, pg_urlpart AS urlpart, pg_urlpart_full AS urlpart_full, DATE(pg_starttime) AS date, pg_comment AS teaser ";
		$sql.= "FROM cmstree{$arrConf['setup']['tableext']} ";
		$sql.= "WHERE parentid = '{$treeid}' and pg_isactive = 1 AND isdeleted = 0 ";
		$sql.= "ORDER BY pg_starttime DESC ";
		
		$arrReturn = $objDB->fetch($sql);
		
		return $arrReturn['data'];
	}
	
} // End class mFrontendNewslist

?>