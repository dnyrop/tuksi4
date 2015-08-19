<?
/**
 * Backend Factory class
 *
 * @package tuksiBase
 */
class tuksiBackend {

	static public $instance;
	
	function __construct() {
		
	}
	
	static function getInstance($if_exist = false) {
		
		if (isset(self::$instance)) {
			return self::$instance;
		}
		
		// Do not make instance
		if ($if_exist) {
			return;
		}
			
		$treeid = $_GET->getInt('treeid');
		$treetabid = $_GET->getInt('tabid');
		
		if (!isset($treeid) || $treeid == 0) {

			//$this->treeid = $this->arrConf['rootid']; 
			$treeid = tuksiIni::$arrIni['setup']['default_backend_treeid']; 
			
		}
		$arrConf = tuksiConf::getConf();

		if (!$arrConf['site']['allowtuksi']) {
			header("Location: /");
			exit();
		}

		// Tuksi backend kan kun køres fra prod website hvis prod_tuksi er sat til 1
		if (tuksiIni::$arrIni['setup']['status'] == 'preview') {
			$url_tuksi = 'http://' . $arrConf['site']['url_prodsite'] . '/' . $arrConf['setup']['admin'];
			header("Location: $url_tuksi");
			exit();
		}
		
		$objDB = tuksiDB::getInstance();

		$objUser = tuksiBackendUser::getInstance();
		

		$sql = "SELECT c.classname, tt.name FROM cmstree t, cmstreetab tt, pg_page_template p, cmscontrol c ";
		$sql.= "WHERE t.id = '{$treeid}' AND t.isdeleted = 0 AND t.id = tt.cmstreeid AND tt.cms_page_templateid = p.id AND p.cmscontrolid = c.id ";

		$sqlTab = "SELECT c.classname ";
		$sqlTab.= "FROM cmstreetab t, cmsperm p, cmsusergroup ug, pg_page_template pp, cmscontrol c ";
		$sqlTab.= "WHERE t.cms_page_templateid = pp.id AND pp.cmscontrolid = c.id AND ";
		$sqlTab.= "p.pread = 1 AND p.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = {$objUser->getUserID()} ";
		$sqlTab.= "AND p.cmstreetabid = t.id AND p.itemtype = 'tree' AND t.cmstreeid = '{$treeid}' ";

		
		if ($treetabid) {
			$sqlTab.= "AND t.id = '{$treetabid}' ";
		}
		$sqlTab.= "ORDER BY t.seq LIMIT 1";

		$arrReturn = $objDB->fetchItem($sqlTab);

		//print_r($arrReturn);
		//print $sql . '<br>';
		$sql = "SELECT c.classname FROM cmstree t, cmscontrol c ";
		$sql.= "WHERE t.isdeleted = 0 AND t.id = '{$treeid}' AND t.cmsbackendcontrolid = c.id";
		
		$arrReturn = $objDB->fetchItem($sql);
		//print_r($arrReturn);
		
		if ($arrReturn['ok'] && $arrReturn['num_rows'] > 0) {
			$classname = $arrReturn['data']['classname'];
			
			$objPage = new $classname();
									
			return $objPage;
		} 
		
	}
	
}
	
?>
