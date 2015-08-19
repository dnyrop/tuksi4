<?php

/**
 * Handles perms in tuksi
 *
 */

class tuksiPerm {
	
	function __construct(){
		
	}
	
	public function getTreeTabUserPerms($userid,$treeid,$tabid){
		
		$objDB = tuksiDB::getInstance();
		
		// Getting standard permissions
		$arrPerm = array(	'READ' => false,
											'ADD' => false,
											'SAVE' => false,
											'ADMIN' => false,
											'DELETE' => false);
		
		$sqlPerm = "SELECT p.pread, p.padd, p.psave, p.padmin, p.pdelete FROM cmsperm p, cmsusergroup ug ";
		$sqlPerm.= "WHERE p.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '" . $userid . "' AND p.itemtype = 'tree' AND p.rowid = '" . $treeid . "' AND p.cmstreetabid = '{$tabid}'";

		$arrRsPerm = $objDB->fetch($sqlPerm);
		if($arrRsPerm['ok']) {
			foreach ($arrRsPerm['data'] as &$arrUsrPerm) {
				$arrPerm['READ'] = !$arrPerm['READ'] ?  $arrUsrPerm['pread'] : $arrPerm['READ'];
				$arrPerm['ADD'] = !$arrPerm['ADD'] ? $arrUsrPerm['padd'] : $arrPerm['ADD'];
				$arrPerm['SAVE'] = !$arrPerm['SAVE'] ? $arrUsrPerm['psave'] : $arrPerm['SAVE'];
				$arrPerm['ADMIN'] = !$arrPerm['ADMIN'] ? $arrUsrPerm['padmin'] : $arrPerm['ADMIN'];
				$arrPerm['DELETE'] = !$arrPerm['DELETE'] ?  $arrUsrPerm['pdelete'] : $arrPerm['DELETE'];
			}	
		} else {
			return false;
		}
		
		// Getting extra permissions
		$sqlExtraPerm = "SELECT v.name FROM cmspermelement c, cmsvariable v, cmsusergroup ug ";
		$sqlExtraPerm.= "WHERE c.cmsgroupid = ug.cmsgroupid AND c.cmsvariableid = v.id AND c.cmstreeid = '" . $treeid . "' AND ug.cmsuserid = '" . $userid . "' AND c.cmstreetabid = '".$tabid."' ";
		$arrRsExtraPerm = $objDB->fetch($sqlExtraPerm);
		if($arrRsExtraPerm['ok']) {
			foreach($arrRsExtraPerm['data'] as &$arrExtraPerm) {
				$arrPerm[$arrExtraPerm['name']] = true;
			}
		}
		return $arrPerm;
	}
	
	public function getTreePerms($treeid){
		
		$objDB = tuksiDB::getInstance();
		
		$arrGroups = array();
		
		$sqlGroup = "SELECT id, name FROM cmsgroup order by name";
		$arrRsResult = $objDB->fetch($sqlGroup);
		foreach($arrRsResult['data'] as &$arrGroup) {		
			
			$arrGroups[$arrGroup['id']]['id'] = $arrGroup['id'];
			$arrGroups[$arrGroup['id']]['name'] = $arrGroup['name'];
			$arrGroups[$arrGroup['id']] = self::getTreeGroupPerms($treeid,$arrGroup['id']);
			
		}	
		$tplPerm->assign("group",$arrGroups);
		
		return $tplPerm->fetch("backend/elementPerms.tpl");
	}
	
	public function getTreeTabGroupPerms($treeid,$tabid,$groupid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlPerm = "SELECT p.padd as `add`, p.pread as `read`, p.psave as save, p.pdelete as `delete`, p.padmin as admin FROM cmsperm p ";
		$sqlPerm.= "WHERE p.itemtype = 'tree' AND p.cmsgroupid = '".$groupid."' AND p.cmstreetabid = '".$tabid."' AND  p.rowid = '".$treeid."' ";
		$arrRsPerm = $objDB->fetch($sqlPerm);
		$arrPerm = $arrRsPerm['data'][0];
		$arrPerm['extraperm'] = self::getTreeTabGroupExtraPerms($treeid,$tabid,$groupid);
		
		return $arrPerm;
	}
	
	public function getTreeTabAllGroupsPerms($treeid,$tabid){
		
		$objDB = tuksiDB::getInstance();
		
		$arrPerms = array();
		
		$sqlGroup = "SELECT id, name FROM cmsgroup order by name";
		$arrRsGroup = $objDB->fetch($sqlGroup);
		foreach($arrRsGroup['data'] as &$arrGroup) {
			$arrPerms[$arrGroup['id']] = self::getTreeTabGroupPerms($treeid,$tabid,$arrGroup['id']);
			$arrPerms[$arrGroup['id']]['name'] = $arrGroup['name'];
			$arrPerms[$arrGroup['id']]['id'] = $arrGroup['id'];
		}
		return $arrPerms;
	}
	
	public function getTreeTabGroupExtraPerms($treeid,$tabid,$groupid){
		
		$objDB = tuksiDB::getInstance();
		
		$arrExtraPerms = array();
		
		$sqlExtraPerm = "SELECT e.id,v.id as extrapermid, v.name FROM cmspermelement e, cmsvariable v ";
		$sqlExtraPerm.= "WHERE e.cmsvariableid = v.id AND cmstreetabid = '".$tabid."' AND cmstreeid = '" . $treeid . "' AND cmsgroupid = '" . $groupid . "' ";
		$sqlExtraPerm.= "GROUP BY v.id ORDER BY v.name";
		$arrRsExtraPerm = $objDB->fetch($sqlExtraPerm);
		if($arrRsExtraPerm['ok'] && $arrRsExtraPerm['num_rows'] > 0) {
			$arrExtraPerms = $arrRsExtraPerm['data'];
		}

		return $arrExtraPerms;
	}
	
	public function deleteExtraPerm($treeid,$tabid,$groupid,$extrapermid){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlDelExtraPerm = "DELETE FROM cmspermelement ";
		$sqlDelExtraPerm.= "WHERE cmstreeid = '$treeid' AND cmstreetabid = '$tabid'  AND cmsgroupid = '$groupid' AND cmsvariableid = '$extrapermid'";
		
		$objDB->write($sqlDelExtraPerm);
	}
	
	function addTreeTabPerm($treeid,$tabid,$cmsgroupid, $arrPerm = array()) {
		
		$objDB = tuksiDB::getInstance();
		
		$read = isset($arrPerm['read']) ? $arrPerm['read'] : 0;
		$add = isset($arrPerm['add']) ? $arrPerm['add'] : 0;
		$save = isset($arrPerm['save']) ? $arrPerm['save'] : 0;
		$admin = isset($arrPerm['admin']) ? $arrPerm['admin'] : 0;
		$delete = isset($arrPerm['delete']) ? $arrPerm['delete'] : 0;
		
		//check if row is already there
		$sqlCheck = "SELECT id FROM cmsperm WHERE itemtype= 'tree' AND cmsgroupid = '$cmsgroupid' AND cmstreetabid = '$tabid' AND rowid = '$treeid' ";
		$arrRsChck = $objDB->fetch($sqlCheck);
		if($arrRsChck['num_rows'] > 0) {		
			$sql = "UPDATE cmsperm SET pread = '$read', padd = '$add', psave = '$save', padmin = '$admin', pdelete = '$delete', delete_me = 0 ";
			$sql.= "WHERE id = '".$arrRsChck['data'][0]['id']."'";
		} else {
			$sql = "REPLACE INTO cmsperm (itemtype, cmsgroupid, pread, padd, psave, padmin, pdelete, rowid,cmstreetabid,delete_me) ";
			$sql .= "VALUES('tree', '$cmsgroupid','$read', '$add','$save', '$admin','$delete', '$treeid','$tabid',0)";
		}
		$rs = $objDB->write($sql);
		
		if(count($arrPerm['extraperm']) > 0) {
			foreach ($arrPerm['extraperm'] as $arrExtraPerm) {
				self::setTreeTabExtraPerm($treeid,$tabid,$cmsgroupid,$arrExtraPerm['extrapermid']);
			}
		}
	}
	
	
	function cleanupTreePerm($treeid,$tabid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "DELETE FROM cmsperm ";
		$sql.= "WHERE itemtype = 'tree' AND rowid= '{$treeid}' AND cmstreetabid = '{$tabid}' AND  delete_me = 1";
		$objDB->write($sql);
	}
	
	function prepareTreePerm($treeid,$tabid,$grpid = 0) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmsperm SET delete_me = 1 ";
		$sql.= "WHERE itemtype = 'tree' AND cmstreetabid = '{$tabid}' AND rowid= '{$treeid}'";
		
		if($grpid > 0) {
			$sql.= " AND cmsgroupid = '$grpid' ";
		}
		
		$objDB->write($sql);
	}
	
	
	public function getExtraPermTypes(){
		
		$objDB = tuksiDB::getInstance();
		
		$arrExtraPerms = array();
		
		$sqlExtraPerm = "SELECT * FROM cmsvariable WHERE isperm = '1' ORDER BY name";
		$arrRsExtraPerm = $objDB->fetch($sqlExtraPerm);
		$arrExtraPerms = $arrRsExtraPerm['data'];
		
		return $arrExtraPerms;
	}
	
	public function setTreeTabExtraPerm ($treeid,$tabid,$groupid,$extrapermid){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlInsert = "REPLACE INTO cmspermelement (cmstreeid, cmsgroupid, cmsvariableid,cmstreetabid) VALUES ";
		$sqlInsert.= "('" .$treeid . "','" . $groupid. "','" . $extrapermid . "','".$tabid."')"; 
		
		$r = $objDB->write($sqlInsert);
	}
	
	
	public function applyPerm($treeid,$grpid,$arrPerms){
		
		$objDB = tuksiDB::getInstance();
								
		$sqlTab = "SELECT id FROM cmstreetab WHERE cmstreeid = '$treeid'";
		$rsTab = $objDB->fetch($sqlTab);
		if($rsTab['num_rows'] == 0) {
			return false;
		} else {
			
			foreach ($arrPerms as $key => $value) {
				$arrSqlPerms[] = " $key = '$value' ";
			}
		
			$sqlPerms = join(", ",$arrSqlPerms);
			
			foreach ($rsTab['data'] as $arrTab) {
				self::prepareTreePerm($treeid,$arrTab['id'],$grpid);
				self::addTreeTabPerm($treeid,$arrTab['id'],$grpid,$arrPerms);
				self::cleanupTreePerm($treeid,$arrTab['id']);
				
				if(count($arrPerms['extraperm']) > 0) {
					foreach ($arrPerms['extraperm'] as $arrExtraPerm) {
						self::setTreeTabExtraPerm($treeid,$arrTab['id'],$grpid,$arrExtraPerm['extrapermid']);
					}
				}
			}
		}
	}
	
	public function getTreeTabUsergrps ($treeid,$tabid){
		
		$objDB = tuksiDB::getInstance();
			
		$sql = "SELECT * ";
		$sql.= "FROM cmsperm ";
		$sql.= "WHERE itemtype = 'tree' AND cmstreetabid = '{$tabid}' AND rowid= '{$treeid}' AND pread = 1 AND psave = 1 ";
		$sql.= "GROUP by cmsgroupid ";
		$rs = $objDB->fetch($sql);
		if($rs['ok']) {
			return $rs['data'];
		}
	
	}
	
	
	public function copyPermToChildren($treeid,$tabid,$groupid){
		
		$arrRights = self::getTreeTabGroupPerms($treeid,$tabid,$groupid);
		
		$objTree =  tuksiTree::getInstance();
		$arrSubNodes = $objTree->getAllSubNodes($treeid);
		
		if(count($arrSubNodes) > 0) {
			foreach ($arrSubNodes as $id) {
				//get Tabs for current treeid
				self::applyPerm($id,$groupid,$arrRights);
			}
		}
	}
}

?>