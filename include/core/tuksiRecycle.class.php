<?

class tuksiRecycle {
	
	function __construct(){}
	
	function getRecycledNodes(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlNodes = "SELECT id,name,parentid FROM cmstree WHERE isdeleted = 1";
		$arrNodes = $objDB->fetch($sqlNodes);
		
		if($arrNodes['ok'] && $arrNodes['num_rows'] > 0){
			
			foreach ($arrNodes['data'] as $arrNode){
				$arrAll[$arrNode['id']] = $arrNode;
			}
			foreach ($arrNodes['data'] as $arrNode){
				if(isset($arrAll[$arrNode['parentid']])){
					$arrAll[$arrNode['parentid']]['nodes'][] = 	&$arrAll[$arrNode['id']];
					$arrRm[] = $arrNode['id'];
				}
			}
			foreach($arrAll as $arr){
				
				if(!in_array($arr['id'],$arrRm)){
					
					$arr['parent_exsist'] = $this->checkParent($arr['parentid']);
					
					$arrComplete[] = $arr;
				}
			}
			
		}
		return $arrComplete;
	}
	
	function checkParent($treeid){
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT * FROM cmstree WHERE id = '$treeid' AND isdeleted = 0 ";
		$rs = $objDB->fetch($sql);
		if($rs['ok'] && $rs['num_rows'] > 0){
			return true;
		} else {
			return false;
		}
		
	}
	
	function moveFromTrash($treeid){
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmstree SET isdeleted = 0 WHERE id = '$treeid'";
		$rs = $objDB->write($sql);
	}
	
	
}

?>