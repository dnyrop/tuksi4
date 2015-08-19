<?

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */

class mBackendRssReader extends mBackendBase  {
	
	function __construct(&$objMod){
		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();
	}	

	function getHtml(){
		
		/*$url = "http://4.0.backend.dev.tuksi.com/test/rss.php";

		$objRss = new tuksiRssReader($url);
		
		if($objRss->load()) {
		
			$arrItems = $objRss->getItems();
				
			$title = $objRss->getTitle();
			
			$this->tpl->assign('title',$title);	
			$this->tpl->assign('items',$arrItems);
			
			$returnHtml = parent::getHTML();
		}*/
		return $returnHtml;
	
	}
}
?>