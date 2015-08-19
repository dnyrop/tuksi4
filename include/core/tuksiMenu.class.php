<?php

/**
 * Class that handles the menu building
 *
 * @package tuksiBackend
 */

class tuksiMenu {
	
	private $rootId;
	private $arrNodes;
	public $arrOpenNodes = array();
	public $openSubNodes = true;
	private $arrActiveNodes = array();
	static $arrParentNodes = array();
	
	function __construct($rootId,$activeId = 0){
		$rootId = intval($rootId);
		if($rootId > 0) {
			$this->rootId = $rootId;
			$this->activeId = $activeId;
		} else {
			die('root id needs to be larger than zero and an integer value');
		}
	}
	
	public function setOpenNodes($arrNodes) {
		if(is_array($arrNodes)) {
			foreach ($arrNodes as &$treeId) {
				$this->arrOpenNodes[$treeId] = $treeId;
				$arrParents = $this->getParentNodes($treeId);
				if(is_array($arrParents) && count($arrParents) > 0) {
					foreach ($arrParents as &$parentId) {
						$this->arrOpenNodes[$parentId] = $parentId;
					}
				}
			}
		}
		$this->arrOpenNodes = array_unique($this->arrOpenNodes);
	}
	
	public function getMenu($userId = 0){
		
		$userId = intval($userId);
		
		if($userId > 0) {
			$this->arrNodes = $this->getNodes($this->rootId,$userId);
		}
		
		return $this->arrNodes;
	}
	
	
	public function setActiveNodes($arrId = array()){
		
		if(count($arrId) > 0) {
			foreach ($arrId as $id) {
				array_push($this->arrActiveNodes,$id);
			}
			array_unique($this->arrActiveNodes);
		}
		
	}

	private function getNodes($parentid,$userId) {
		
		$arrNodes = array();
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$sqlNodes = "SELECT DISTINCT t.id,t.name,t.pg_isactive as isactive,t.datechanged,t.cmscontextid,t.datepublished,t.cmstreetypeid,t.value1,t.value2,t.value3, ";
		$sqlNodes.= "txt.value_{$arrConf['setup']['admin_lang']} AS namelang, t.cmssitelangid, ";
		$sqlNodes.= " (SELECT count(*) FROM cmstree tt WHERE tt.parentid = t.id) AS haschild ";
		$sqlNodes.= "FROM (cmstree t, cmsperm p, cmsusergroup ug) ";
		$sqlNodes.= "LEFT JOIN cmstext txt ON (t.name = txt.token) ";
		$sqlNodes.= "WHERE t.parentid = '{$parentid}' AND ug.cmsuserid = '" . $userId . "' AND p.itemtype = 'tree' AND t.isdeleted = 0 AND show_inmenu = 1 ";
		$sqlNodes.= "AND p.cmsgroupid = ug.cmsgroupid AND p.rowid = t.id AND p.pread = 1 ";
		$sqlNodes.= "ORDER BY t.seq";

		//pg_show_inmenu fjernet
		// print $sqlNodes . "\n\n<br><br>";
		
		$arrRsNodes = $objDB->fetch($sqlNodes);
		
		if($arrRsNodes['ok'] && $arrRsNodes['num_rows'] > 0) {

			foreach($arrRsNodes['data'] as &$arrNode) {
				
				if(in_array($arrNode['cmstreetypeid'], array(2, 3))){
					if($arrNode['cmstreetypeid'] == 3) {
						$arrNode['external_link'] = $arrNode['value1'];	
						if($arrNode['value2']) {
							$arrNode['popup'] = $arrNode['value2'];
						}
					} elseif($arrNode['cmstreetypeid'] == 2) {
						if($arrNode['value1']) {
							$sql = $arrNode['value1'];
							if($objDB->validateSelectSQL($sql)) {
								$arrRs = $objDB->fetch($sql);
								if(count($arrRs['data']) > 0){
									foreach ($arrRs['data'] as $arrValues) {
										//make node array
										$arrNewNode = array('id' => $arrNode['id'],
																				'isactive' => 1,
																				'name' => $arrValues['name'],
																				'rowid' => $arrValues['id']);
										$arrNodes[$arrNode['id']."_".$arrValues['id']] = $arrNewNode;
									}	
								}
							}
						}
					}
					continue;
				}
				
				if(!$arrNode['datepublished'] || !$arrNode['datechanged'] || (strtotime($arrNode['datechanged']) >  strtotime($arrNode['datepublished']))) {
					$arrNode['unpublished'] = true;
				} else {
					$arrNode['unpublished'] = false;
				}
				
				if($this->openSubNodes) {
					
					if(in_array($arrNode['id'],$this->arrOpenNodes)) {

						$arrNode['selected'] = true;
						
						if ($arrNode['haschild']) {
							if(($arrChild = $this->getNodes($arrNode['id'],$userId)) !== false) {
								$arrNode['nodes'] = $arrChild;
								$arrNode['has_children'] = true;
								$this->addToSession($arrNode['id']);
							}
						}
					} else {
						if($this->hasChildren($arrNode['id'],$userId)) {
							$arrNode['has_children'] = true;
						}
					}
				} 
				
				if(in_array($arrNode['id'],$this->arrActiveNodes)){
					$arrNode['selected'] = true;
				}
				
				$arrNode['url'] = tuksiTools::getBackendUrl($arrNode['id']);
				
				if (!$arrNode['cmssitelangid']) {
					if ($arrNode['namelang'])
						$arrNode['name'] = $arrNode['namelang'];
				}
				$arrNodes[$arrNode['id']] = $arrNode;
			}
		
			// Sæt first og last
			$max = count($arrNodes);
			$i = 0;
			foreach ($arrNodes as $id => $data) {
				$i++;
				$arrNodes[$id]['first'] = ($i == 1);
				$arrNodes[$id]['last'] = ($i == $max);
			}

			return $arrNodes;
		} else {
			return false;
		}
	}
	
	private function hasChildren($parentid,$userId){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlNodes = "SELECT DISTINCT t.id,t.name,t.pg_isactive as isactive,t.datechanged,t.datepublished FROM cmstree t, cmsperm p, cmsusergroup ug ";
		$sqlNodes.= "WHERE t.parentid = '{$parentid}' AND ug.cmsuserid = '" . $userId . "' AND p.itemtype = 'tree' AND t.isdeleted = 0 AND show_inmenu = 1 ";
		$sqlNodes.= "AND p.cmsgroupid = ug.cmsgroupid AND p.rowid = t.id AND p.pread = 1 ";
		$sqlNodes.= "ORDER BY t.seq";

		$arrRsNodes = $objDB->fetch($sqlNodes);
		
		if($arrRsNodes['ok'] && $arrRsNodes['num_rows'] > 0) {
			return true;
		} else {
			return false;
		}
	}
	public function openTo($treeid){
		$this->addToSession($treeid);
		$this->rootId;
	}
	
	private function getParentNodes($treeid,$arrParents = array()) {
		
		if($treeid > 0) {
		
			if(isset(self::$arrParentNodes[$treeid])) {
				return self::$arrParentNodes[$treeid];
			}
			
			$objDB = tuksiDB::getInstance();
			
			$sqlParents = "SELECT parentid FROM cmstree WHERE id = $treeid";
			$rsParent = $objDB->fetch($sqlParents);
			if($rsParent['ok'] && $rsParent['num_rows'] > 0) {
				$parentId = $rsParent['data'][0]['parentid'];		
				if($parentId == $this->rootId) {
					self::$arrParentNodes[$treeid] = $arrParents;
					return $arrParents;
				} else {
					$arrParents[] = $parentId;
					$arrtmp = $this->getParentNodes($parentId,$arrParents);
					self::$arrParentNodes[$treeid] = $arrtmp;
					return $arrtmp;
				}
			}
		}
		return $arrParents;
	}
	
	function loadOpenFromSession(){
		if(isset($_SESSION['menu']) && count($_SESSION['menu']['openids']) > 0) {
			if (count($_SESSION['menu']['openids'])) {
				foreach($_SESSION['menu']['openids'] as $id) {
					$this->arrOpenNodes[$id] = $id;
				}
			}
		}
	}
	
	private function addToSession($id){
		if(!isset($_SESSION['menu']) || !is_array($_SESSION['menu']['openids']) || !in_array($id,$_SESSION['menu']['openids'])){
			$_SESSION['menu']['openids'][$id] = $id;
		}
		if (isset($_SESSION['menu'])) {
			$_SESSION['menu']['openids'] = array_unique($_SESSION['menu']['openids']);
		}
	}
	
	
	function closeNode($treeid){
		
		if(is_array($_SESSION['menu']['openids']) && in_array($treeid,$_SESSION['menu']['openids'])) {
			unset($_SESSION['menu']['openids'][$treeid]);
		}
		if(is_array($this->arrOpenNodes) && in_array($treeid,$this->arrOpenNodes)) {
			unset($this->arrOpenNodes[$treeid]);
		}
	}
	
	private function closeChildren($treeid){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlParents = "SELECT id FROM cmstree WHERE parentid = $treeid";
		$rsParent = $objDB->fetch($sqlParents);
		if($rsParent['ok'] && $rsParent['num_rows'] > 0) {
			foreach ($rsParent['data'] as $arrId) {
				$this->closeNode($arrId['id']);
			}
		}
	}
	
	//find the current topmenu id
	public function getTopmenuId($id = 0){
		if($id > 0) {
			$arrParents = $this->getParentNodes($id);
			
			if(count($arrParents) == 0) {
				//got a topmenu
				return $id;
			} else {
				$arrParentsRev = array_reverse($arrParents);
				if($arrParentsRev[0] > 0) {
					return $arrParentsRev[0];
				}
			}
		}
	}
	
}
?>
