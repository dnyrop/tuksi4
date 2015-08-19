<?
/**
 * Frontend Factory class
 * 
 * Loads Sitemap and parses it til control class
 *
 * @package tuksiFrontend
 */
class tuksiFrontend {

	static public $instance;
	
	function __construct() {
	}
	
	static function getInstance($if_exist = false) {
		
		if (self::$instance) {
			return self::$instance;
		}
		
		// Do not make instance
		if ($if_exist) {
			return;
		}

		$arrConf = tuksiConf::getConf();
		$token = $_GET->getStr('token');
		if ($arrConf['site']['urlpart_prefix'] != $token) {
			$arrGet = $_GET->getData();
			if (isset($arrGet['urlpart'])) {
				$arrGet['urlpart'] = $token . '/' . $arrGet['urlpart'];
				$_GET = new tuksiInputfilter($arrGet);
			} // if
		} else {
			$token = '';
		} // if
		
		// Getting treeID from tuksiSitemap	
		$objSitemap = new tuksiFrontendSitemap();
		$objSitemap->setLoadAll(false);
		$objSitemap->setLoadLevels(1);
		
		$arrSitemap = $objSitemap->makeSitemap();	
			
		$treeid = $objSitemap->getTreeid();
		
		if ($treeid == $arrConf['site']['rootid'] && $token) {
			tuksiTree::shorturlRedirect($token, $arrConf['setup']['tableext']);
		}
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT c.classname ";
		$sql.= "FROM cmstree{$arrConf['setup']['tableext']} t, cmscontrol{$arrConf['setup']['tableext']} c, pg_page_template{$arrConf['setup']['tableext']} p ";
		$sql.= "WHERE t.id = '{$treeid}' AND t.pg_page_templateid = p.id AND p.cmscontrolid = c.id";
		
		$arrReturn = $objDB->fetchItem($sql, array('expire' => 360, 'name' => 'Getting main control by treeid'));

		if ($arrReturn['num_rows'] > 0) {
			
			$classname = $arrReturn['data']['classname'];
			
			$objPage = new $classname($objSitemap);
			
			return $objPage;
		} else {
			
			if ($arrConf['site']['rootid'] != $treeid) {
				http_response_code(301);
				header("Location: /");
				exit();
			}
			return false;
		}
		
	} // End getInstance()
	
}
	
?>
