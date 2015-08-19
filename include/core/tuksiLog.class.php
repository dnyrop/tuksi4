<?

/**
 * Enter description here...
 *
 * @package ??
 */

class tuksiLog {
	
	private function __construct(){
		
	}
	
	public function add($type,$relationid = '',$tablename = '',$content = '',$value1 = '',$value2 = '',$value3 = '') {
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		if(is_int($type)) {
			$logType = $type;
		} else {
			$logType = self::getLogtype($type);
		}
		
		$arrValues = array(	'cmseventlogtypeid' => $logType,
												'cmsuserid' => $arrUser['id'],
												'tablename' => $tablename,
												'relationid' => $relationid,
												'cmstreeid' => $objPage->treeid,
												'value1' => $value1,
												'value2' => $value2,
												'value3' => $value3,
												'content' => $content);
		
		$arrValuesRaw = array('dateadded' => 'now()');												
		
		$r = $objDB->insert('cmseventlog',$arrValues,$arrValuesRaw);
	}
	
	public function treeAction($type,$treeid,$content = '',$value1 = '',$value2 = '',$value3 = '') {
		self::add($type,$treeid,'cmstree',$content,$value1,$value2,$value3);
	}
	
	private function getLogtype($token) {
		$objDB = tuksiDB::getInstance();
		
		if($token != '') {
		
			$sql = "SELECT id FROM cmseventlogtype ";
			$sql.= "WHERE token = '".$objDB->realEscapeString($token)."' ";
			
			$arrRs = $objDB->fetch($sql);
			
			if($arrRs['ok'] && $arrRs['num_rows'] == 1) {
				return $arrRs['data'][0]['id'];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function getLogForPage($treeid,$tabid = 0){
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT date_format(l.dateadded,'%d.%m.%y %H:%i') as dateadded,t.name as eventname,u.name as username FROM cmseventlog l,cmseventlogtype t,cmsuser u ";
		$sql.= "WHERE l.relationid = '{$treeid}' AND l.cmseventlogtypeid = t.id AND u.id = l.cmsuserid ";
		$sql.= "ORDER BY l.dateadded desc ";
		$sql.= "LIMIT 20";

		$rs = $objDB->fetch($sql);
		
		return $rs['data'];
	}
	
	
}
?>
