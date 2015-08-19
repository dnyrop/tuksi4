<?php

/**
 * ??
 *
 * @package tuksiBackend
 */

class tuksiPageGeneratorStatus {
	
	private function __construct(){}	
	
	static public function getLatestCreatedPages($limit = 10,$siteid = 0){
		return self::getLatestPages($limit,'created',$siteid);
	}

	static public function getLatestChangedPages($limit = 10,$siteid = 0){
		return self::getLatestPages($limit,'changed',$siteid);
	}	
	
	static public function getLatestPublishedPages($limit = 10,$siteid = 0){
		return self::getLatestPages($limit,'published',$siteid);
	}
	
	static public function get1LatestDeletedPages($limit = 10,$siteid = 0){
		return self::getLatestPages($limit,'deleted',$siteid);
	}
	
	static private function getLatestPages($limit = 10,$orderType = 'created',$siteid = 0){
		
		$where = "";
		
		switch ($orderType) {
			case 'created':
				$orderBy = " datecreated ";
				$where = " AND isdeleted = 0 ";
				$eventid = 2;
				break;
			case 'published':
				$orderBy = " datepublished ";
				$where = " AND datepublished <> '' AND isdeleted = 0 ";
				$eventid = 4;
				break;
			case 'changed':
				$orderBy = " datechanged ";
				$where = " AND isdeleted = 0 ";
				$eventid = 3;
				break;
			case 'deleted':
				$orderBy = " datedeleted ";
				$where = " AND isdeleted = 1 ";
				$eventid = 16;
				break;
			default:
				$orderBy = " datecreated ";
				$where = " AND isdeleted = 0 ";
				$eventid = 2;
				break;
		}
		
		$objDB = tuksiDB::getInstance();

		if(!is_int($limit))
		$limit = 10;

		$sqlLang = "";
		
		if($siteid)	{
			$sqlSitelang = "SELECT * FROM cmssitelang WHERE cmssiteid = '$siteid' ";
			$rsSiteLang = $objDB->fetch($sqlSitelang);
			if($rsSiteLang['ok'] && $rsSiteLang['num_rows'] > 0) {
				
				foreach ($rsSiteLang['data'] as $arr){
					$arrLang[] = $arr['id'];
				}
				$sqlLang = " AND (t.cmssitelangid = '" . join("' OR t.cmssitelangid='",$arrLang) . "') ";
			}
		}
		
		$sqlLatest = "SELECT t.*,date_format(t.$orderBy,'%d.%m.%y %H:%i') as orderdate ";
		$sqlLatest.= "FROM cmstree t ";
		$sqlLatest.= "WHERE t.cmscontextid = 2 AND t.datecreated <> '' $where $sqlLang ";
		$sqlLatest.= "ORDER BY t.$orderBy desc,t.id limit " . $limit;
		
		$arrLatest = $objDB->fetch($sqlLatest, array('name' => 'Get Latest'));
		
		if($arrLatest['num_rows'] > 0) {
			
			foreach ($arrLatest['data'] as &$arr) {
				$arr['user'] = self::getUser($arr['id'],$eventid);
				if($orderType != 'deleted')
					$arr['backendurl'] = tuksiTools::getBackendUrl($arr['id']);
			}
			
			return $arrLatest['data'];
		} else {
			return false;
		}
		
	}
	static function getUser($treeid,$eventtypeid){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlLog = "SELECT * FROM cmseventlog l, cmsuser u ";
		$sqlLog.= "WHERE l.relationid = '$treeid' AND l.cmseventlogtypeid = '$eventtypeid' AND u.id = l.cmsuserid ";
		$sqlLog.= "ORDER BY l.dateadded DESC limit 1";
		
		$rsLog = $objDB->fetchItem($sqlLog);
		
		if($rsLog['ok'] && $rsLog['num_rows'] == 1) {
			return $rsLog['data']['name'];
		} else {
			return "N/A";	
		}
	}
}
?>
