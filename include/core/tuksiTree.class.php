<?
/**
 * Klasse som håndtere forskellige rutiner på cmstree
 * 
 * 
 * @package tuksiBackend
 */

class tuksiTree {

	static private $instance;
	private $liveTables = array();
	
	function __construct() {
	}
	
	static function getInstance() {
		if (!self::$instance){
			self::$instance = new tuksiTree();
		}
		return self::$instance;
	}
	
 
	function copyTreeNode($tree_parentid, $treeid_to = 0, $cmstreetypeid = 0,$newcmstreetypeid = 0) {
 
		$objDB = tuksiDB::getInstance();
		
		$tree_parentid = intval($tree_parentid);
				
		if($objDB->fetchRow('cmstree',$tree_parentid)) {
			
			if (empty($treeid_to))
				$treeid_to = $tree_parentid;
		
			$objParentTree = $objDB->fetchRow("cmstree", $tree_parentid, 'object');
	
			// * --------------------------------------------------------------------------------- *
		  // Adding new tree node
			// * --------------------------------------------------------------------------------- *
			if ($newcmstreetypeid) {
				$objParentTree->cmstreetypeid = $newcmstreetypeid;
			}
			
			$seq = $this->getNextSeq($treeid_to);
		  
			$arrValues = array(	
				'parentid' => $treeid_to,
				'name' => '?',
				'seq' => $seq,
				'cmstreetypeid' => $objParentTree->cmstreetypeid,
				'pg_isactive' => $objParentTree->pg_isactive,
				'value1' => $objParentTree->value1,
				'value2' => $objParentTree->value2,
				'value3' => $objParentTree->value3,
				'pg_page_templateid' => $objParentTree->pg_page_templateid,
				'cmscontextid' => $objParentTree->cmscontextid,
				'cmsbackendcontrolid' => $objParentTree->cmsbackendcontrolid,
				'cmssitelangid' => $objParentTree->cmssitelangid
			);
			
			if($cmstreetypeid) {
				$arrValues['cmstreetypeid'] = $cmstreetypeid;
			}
			
			$arrRawValues = array(
				'datecreated' => 'now()',
				'datechanged' => 'now()'
			);
			
			$arrRsIns = $objDB->insert('cmstree',$arrValues,$arrRawValues);
			$newTreeid = $arrRsIns['insert_id'];
			
			$arrNewTabs = $this->copyTreeTabs($tree_parentid,$newTreeid);
			
			return array("NEWTREEID" => $newTreeid, "tabs" => $arrNewTabs);
		} else {
			return false;
		}
	} // END Function copyTreeNode

	
	public function copyTreeTabs($fromTreeId,$toTreeId){
		
		$objDB = tuksiDB::getInstance();
		$arrNewTabs = array();
		
		
		$sqlTab = "SELECT * FROM cmstreetab WHERE cmstreeid = '$fromTreeId'";
		$rsTab = $objDB->fetch($sqlTab);
		
		if($rsTab['ok']) {
			foreach ($rsTab['data'] as $arrTab) {
				
				$sqlIns = "INSERT INTO cmstreetab (name, cmstreeid, seq, cms_page_templateid) ";
				$sqlIns.= " VALUES ('{$arrTab['name']}','$toTreeId','{$arrTab['seq']}','{$arrTab['cms_page_templateid']}')";
				$arrIns = $objDB->write($sqlIns) or print mysql_error();
			
				$newtabid = $arrIns['insert_id'];
				
				$arrNewTabs[$arrTab['id']] = $newtabid;
				
				$arrPerms = tuksiPerm::getTreeTabAllGroupsPerms($fromTreeId,$arrTab['id']);
				
				foreach ($arrPerms as $arrPerm) {
					tuksiPerm::addTreeTabPerm($toTreeId,$newtabid,$arrPerm['id'],$arrPerm);
				}
			}
			return $arrNewTabs;
		} else {
			return false;
		}
		
	}
	
	function createNode($type){
		//todo
		/*$sql = "INSERT INTO cmstree (cmscontextid) VALUES ('".intval($type)."')";
		$this->objDB->query($sql) or print mysql_error();
		$treeId = mysql_insert_id();
		
		$sqlIns = "INSERT INTO cmstreetab (name, cmstreeid) ";
		$sqlIns.= " VALUES ('Default','$treeId')";
	 	$this->objDB->query($sqlIns) or print mysql_error();
		$tabId = mysql_insert_id();
	 	
		return new tuksi_treenode(array(	'treeid' => $treeId,
													'tabs' => array(array('id' => $tabId))));*/
	}
	
	function getNextSeq($parent_treeid) {

		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT MAX(seq) as maxseq FROM cmstree WHERE parentid = '$parent_treeid'";
		$rsMax = $objDB->fetch($sql);
		if ($rsMax['num_rows']) {
			$seq = $rsMax['data'][0]['maxseq'];
		} else 
			$seq = 0;
			
		return $seq + 100;
		
	}
	
	function deleteTreeNode($treeid,$force = false) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "UPDATE cmstree SET isdeleted = 1,datedeleted = now() WHERE id = '{$treeid}'";
		$objDB->write($sql);
	}
	
	function deleteTreeNodeForGood($treeid,$live = false,$force = false) {

		$objDB = tuksiDB::getInstance();
		
		if($live)
			$append = 'live';
		else
			$append = '';
				
		$sql = "SELECT * FROM cmstree$append WHERE parentid = '$treeid'";
		$rs = $objDB->fetch($sql);
		
		if ($rs['num_rows'] > 0 && !$force) {

			$strError = "* Valgte node er ikke tom.<br>";
		
		} else {
			
			//get all tabs
			$sqlTabs = "SELECT * FROM cmstreetab$append WHERE cmstreeid = '$treeid'";
			$rsTabs = $objDB->fetch($sqlTabs) or print mysql_error();
			if($rsTabs['num_rows'] > 0) {
				foreach($rsTabs['data'] as $arrTab) {
					self::deleteTab($arrTab['id']);
					$sqlDelTab = "DELETE FROM cmsperm WHERE rowid = '{$arrTab['id']}' AND itemtype = 'treetab'";
					$objDB->write($sqlDelTab);
				}
			}
			
			$sql = "DELETE FROM cmstreeelement$append WHERE cmstreeid = '$treeid'";
			$result = $objDB->write($sql) or print mysql_error();
			$sql = "DELETE FROM cmstreetext$append WHERE cmstreeid = '$treeid'";
			$result = $objDB->write($sql) or print mysql_error();
			$sql = "DELETE FROM cmstreetab$append WHERE cmstreeid = '$treeid'";
			$result = $objDB->write($sql) or print mysql_error();
			$sql = "DELETE FROM cmstree$append WHERE id = '$treeid'";
			$result = $objDB->write($sql) or print mysql_error();
		}
		return $strError;
	} // END Function DeleteTreeNode
	
	
	function deleteTab($tabid){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		// * ---------------------------------------------------------------------------------- *
		// Henter de tilknyttede moduler for det aktuelle area
		// * ---------------------------------------------------------------------------------- *
		$sqlMod = "SELECT * FROM pg_content WHERE cmstreetabid = '{$tabid}' ORDER BY seq";
		$rsMod = $objDB->fetch($sqlMod,array('type' => 'object')) or print mysql_error();
		
		foreach($rsMod['data'] as $objMod){
				
			$sqlFieldInModule = "SELECT distinct(fi.id), ft.classname,fi.tablename, fi.colname, fi.name, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, fi.helptext ";
			$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldtype ft ";
			$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '".$objMod->pg_moduleid."' AND fi.cmsfieldtypeid = ft.id ";
			$sqlFieldInModule.= "ORDER BY fi.seq";
			
			$rsField = $objDB->fetch($sqlFieldInModule,array('type' => 'object'));
			
			//traveser de forskellige felttyper for det pågældende modul
			foreach ($rsField['data'] as $objField) {
				
				//sætter de nødvendige variable for klassen
				$objField->htmltagname  = "module_{$objMod->id}_{$objField->id}";
				$objField->value			= $objMod->{$objField->colname};//$_POST[$objField->htmltagname];
				$objField->vcolname 		= $objField->htmltagname;
				$objField->rowid 			= $objMod->id;
				$objField->rowData 		= $objMod;
				
				self::deleteItem($objField->classname,$objMod->{$objField->colname},$objField,$objPage);
			}
		}
		$sqlDel = "DELETE FROM pg_content WHERE cmstreetabid = $tabid";
		$objDB->write($sqlDel);
		$sqlTab = "DELETE FROM cmstreetab WHERE id = '{$tabid}'";
		$rsTab = $objDB->write($sqlTab);
		$sqlPerm = "DELETE FROM cmsperm WHERE cmstreetabid = '{$tabid}' AND itemtype = 'tree'";
		$objDB->write($sqlPerm);
		
	}
	
	public function deleteItem($fieldType,$value,$objField,&$objPage){
			if(is_callable(array($fieldType,'deleteData'))) {
				$objNewField = new $objField->classname($objField, $objPage);
				$objNewField->deleteData($objPage);
			} 
		}
	
	
	/**
	 * Returnere antal subnodes
	 *
	 * @param int $treeid
	 * @return int
	 */
	
	function haveSubNodes($treeid) {
		$objDB = tuksiDB::getInstance();
		$sql = "SELECT id FROM cmstree WHERE parentid = '{$treeid}'";
		$rs = $objDB->fetch($sql);
		return $rs['num_rows'];
	}
	
	/**
	 * Opdatering af node
	 *
	 * @param int $treeid
	 * @param int $treenodetypeid
	 */
	
	function updateTree($treeid, $treenodetypeid) {
			
		$objDB = tuksiDB::getInstance();
		$sql = "UPDATE cmstree SET cmstreetypeid = '$treenodetypeid' WHERE id  = '{$treeid}'";
		$arrRs = $objDB->write($sql);
		if($arrRs['ok']) {
			return true;
		} else {
			return false;
		}
 	 
	} // END Function updateTree


	function addTreeNode($parentid, $name) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "INSERT INTO cmstree (parentid, name, cmstreetypeid) VALUES('{$parentid}','".$objDB->realEscapeString($name)."', 1)";
		$rs = $objDB->write($sql) or $error = "Kunne ikke tilføje node: " . mysql_error() . $sql;
		
		if ($rs['ok'])
			return array($rs['insert_id'], "");
		else
			return array(0, $error);
	} // End addTreeNode

	
	function addTab($treeid, $name,$arrFields = array()) {
		
		$objDB = tuksiDB::getInstance();
	
		$addField = "";
		$addFieldValue = "";
		
		if(count($arrFields) > 0) {
			foreach ($arrFields as $key => $value) {
				$addField = ",$key";
				$addFieldValue = ",'$value'";
			}
		}
		
		$sql = "INSERT INTO cmstreetab (cmstreeid, name $addField) VALUES('{$treeid}','{$name}' $addFieldValue)";
		$arrRs = $objDB->write($sql) or $error = "Kan ikke tilføje ny tab" . mysql_error();
		if ($arrRs['ok'])
			return array($arrRs['insert_id'], "");
		else
			return array(0, $arrRs['error']);;
	} // End addTab

	function addTabValue($cmstreetabid, $cmsvariableid, $value) {
		
		$objDB = tuksiDB::getInstance();
		
		$value = stripslashes($value);

		$sqlInsert = "INSERT INTO cmstreeelement (cmstreetabid, value, cmsvariableid) ";
		$sqlInsert.= " VALUES('{$cmstreetabid}','" . mysql_escape_string($value) . "','{$cmsvariableid}')";

		$rs = $objDB->write($sqlInsert) or print "Kunne ikke tilføje ny variabel: " .mysql_error();
			
		if ($rs) 
			return true;
		else
			return false;
	}
	
	/**
	 * shiftTree moves the sequence of a given node either up or down
	 *
	 * @param int $treeid
	 * @param enum $direction [up,down]
	 */
	
	function alterNodeSequens($treeid,$direction){
		
		$objDB = tuksiDB::getInstance();
		
		if ($arrTree = $objDB->fetchRow('cmstree',$treeid)) {
		
			$sqlSeq = "SELECT * FROM cmstree ";
			$sqlSeq.= "WHERE parentid = '{$arrTree['parentid']}' AND isdeleted = 0 ORDER BY seq";
			$arrRsSeq = $objDB->fetch($sqlSeq);
			
			if($arrRsSeq['num_rows'] > 0) {
				$seq = 0;
				foreach ($arrRsSeq['data'] as &$arr) {
					
					if($treeid == $arr['id']) {
						if($direction == 'up') {
							$newSeq = $seq - 150;
						}else {
							$newSeq = $seq + 150;	
						}
						$sqlChanged = ",datechanged = now()";	
					} else {
						$newSeq = $seq;
						$sqlChanged = "";
					}
					$sqlUpd = "UPDATE cmstree SET seq = '$newSeq' $sqlChanged WHERE id = '{$arr['id']}'";
					$r = $objDB->write($sqlUpd);
					$seq+=100;
				}
				return true;
			}
		}	else {
			return false;
		}
	}

	
	function moveTab($tabid,$treeid,$position){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlUpd = "UPDATE cmsperm SET rowid = '$treeid' WHERE cmstreetabid='$tabid' ";
		$rs = $objDB->write($sqlUpd);

		$sqlUpd = "UPDATE pg_content SET cmstreeid = '$treeid' WHERE cmstreetabid='$tabid' ";
		$rs = $objDB->write($sqlUpd);


		$sqlSeq = "SELECT * FROM cmstreetab WHERE cmstreeid = '{$treeid}' ORDER BY seq";
		$rsSeq = $objDB->fetch($sqlSeq);

		if($rsSeq['num_rows'] > 0) {
			
			$nbRow = 1;
			$seq = 100;
			
			foreach($rsSeq['data'] as $arrTab) {
			
				if($nbRow == 1 && $position == 1) {
						//update sequence
						$sqlUpd = "UPDATE cmstreetab SET cmstreeid = '$treeid',seq = '$seq' WHERE id='$tabid' ";
						$objDB->write($sqlUpd);	
						$seq+= 100;	
					}
					
					$sql = "UPDATE cmstreetab set seq = '$seq' WHERE id = '{$arrTab['id']}'";
					$r = $objDB->write($sql);
					$seq+= 100;	
					
					//if first
					if($nbRow == $rsSeq['num_rows'] && $position == 2) {
						//update sequence
						$sqlUpd = "UPDATE cmstreetab SET cmstreeid = '$treeid',seq = '$seq' WHERE id='$tabid' ";
						$objDB->write($sqlUpd);	
					}
					$nbRow++;
			}
		}
		return true;
	}
	
	/**
	 * function that moves a given treenode either before, after or under another treenode
	 *
	 * @param int $treeid
	 * @param int $movetoid
	 * @param enum $action [before,after]
	 * @return int New parent treeid
	 */
	
	function moveNode($treeid,$movetoid,$action) {
		
		$objDB = tuksiDB::getInstance();
		
		$arrParent = $objDB->fetchRow('cmstree',$movetoid);
						
		$seq = 100;
		
		$sqlSeq = "SELECT * FROM cmstree WHERE parentid = '{$arrParent['parentid']}' ORDER BY seq";
		$rsSeq = $objDB->fetch($sqlSeq);
		
		if($rsSeq['num_rows'] > 0) {
			
			foreach($rsSeq['data'] as $arrTree) {
				if($movetoid == $arrTree['id']) {
			
					if($action == 'before')
						$newSeq = $seq - 50;
					else
						$newSeq = $seq + 50;	
						
					$arrValues = array(	'parentid' => $arrParent['parentid'],
															'seq' => $newSeq);
					$arrRaw = array('datechanged' => 'now()');
					
					$arrRS = $objDB->update('cmstree',$arrValues,$arrRaw,"id= '{$treeid}' ");	
				} 
				if($arrTree['id'] != $treeid) {
					$sql = "UPDATE cmstree set seq = '$seq' WHERE id = '{$arrTree['id']}'";
					$objDB->write($sql);
				}
				$seq+= 100;
			}
			return $arrParent['parentid'];
		
		} else {

			return 0;
		
		}
	}
	
	/**
	 * Function thats move a tree under a new parent node
	 *
	 * @param int $treeid
	 * @param int $movetoid
	 * @param enum $action [first,last]
	 */
	
	public function moveNodeAsChild($treeid,$movetoid,$action){
		
		$objDB = tuksiDB::getInstance();
		
		if($objDB->fetchRow('cmstree',$movetoid) && $objDB->fetchRow('cmstree',$treeid)) {
		
			$seq = 0;	
			$sqlSeq = "SELECT * FROM cmstree WHERE parentid = '{$movetoid}' ORDER BY seq";
			$rsSeq = $objDB->fetch($sqlSeq);
			$nbRow = 0;
			$nbRows = $rsSeq['num_rows'];
			if($nbRows > 0) {
				
				foreach($rsSeq['data'] as $arrTree) {
					//if first
					if($nbRow == 0 && $action == 'first') {
						//update sequence
						$sql = "UPDATE cmstree set datechanged = now(),parentid = '{$movetoid}',seq = '$seq' WHERE id = '{$treeid}'";
						$objDB->write($sql);	
						$seq+= 100;	
					}
					
					$sql = "UPDATE cmstree set seq = '$seq' WHERE id = '{$arrTree['id']}'";
					$objDB->write($sql);
					$seq+= 100;	
					
					//if first
					if($nbRow == $nbRows && $action == 'last') {
						//update sequence
						$sql = "UPDATE cmstree set datechanged = now(),parentid = '{$movetoid}',seq = '$seq' WHERE id = '{$treeid}'";
						$objDB->write($sql);	
					}
					$nbRow++;
				}
				return true;
			
			} else {
				
				$sql = "UPDATE cmstree set datechanged = now(),parentid = '{$movetoid}',seq = '100' WHERE id = '{$treeid}'";
				$rs = $objDB->write($sql);	
				
				//update treetype
				$sqlUpd = "UPDATE cmstree SET cmstreetypeid = '1' WHERE id = '{$movetoid}'";
				$rsUpd = $objDB->write($sqlUpd);
				
				return true;
				
			}
		} else {
			return false;
		}
	}
	
	function checkSelf($treeid,$parentid) {
		
		$status = true;
		
		if(intval($treeid) > 0 && intval($parentid) > 0 && ($treeid != $parentid)) {
		
			$arrNodes = $this->getAllSubNodes($treeid);
			if(is_array($arrNodes)) {
				foreach ($arrNodes as $nodeId) {
					if($parentid == $nodeId) {
						$status = false;	
					}
				}
			}
			
		} else {
			$status = false;
		}
		return $status;	
	}
	
	public function checkParent($treeid,$placement){
		
		$objDB = tuksiDB::getInstance();
		
		if($placement == 2 || $placement == 3) {
			$sql = "SELECT parentid FROM cmstree WHERE id = '$treeid'";
			$rs= $objDB->fetchItem($sql);
			if($rs['ok'] && $rs['num_rows']) {
				$treeid = $rs['data']['parentid'];
			} else {
				return false;
			}
		}
		$arrUser = tuksiBackendUser::getUserInfo();
		$arrUser['usergroup'];
		
		//get all tabs
		$sqlTab = "SELECT * FROM cmstreetab WHERE cmstreeid = '{$treeid}' ";
		$arrRsTab = $objDB->fetch($sqlTab);
		if($arrRsTab['ok'] && $arrRsTab['num_rows']) {
			
			foreach ($arrRsTab['data'] as $arrTab) {
				
				foreach($arrUser['usergroup'] as $arrGrp) {
					$arrPerm = tuksiPerm::getTreeTabGroupExtraPerms($treeid,$arrTab['id'],$arrGrp->id);
					if(count($arrPerm) > 0){
						foreach ($arrPerm as $perm) {
							if($perm['name'] == 'ADDPAGE'){
								return true;
							}
						}
					}
				}
			}
		} else {
			return false;
		}
	}
	
	
	/**
	 * Checks if a given tree node exstsis
	 *
	 * @param int $treeid
	 * @param String $type can be frontend, backend, intranet or newsletter
	 * @return boolean
	 */
	
	function treeNodeExists($treeid,$type = '') {
		
		$objDB = tuksiDB::getInstance();
		
		if(intval($treeid) > 0) {
			
			$sql = "SELECT id FROM cmstree WHERE id = '{$treeid}' ";
			
			if(!empty($type)) {
				switch ($type) {
					case 1: $sql.= " AND cmscontextid = 1 "; break;
					case 2: $sql.= " AND cmscontextid = 2 "; break;
					case 3: $sql.= " AND cmscontextid = 3 "; break;
					case 4: $sql.= " AND cmscontextid = 4 "; break;
					default:break;
				}
			}
			
			$rs = $objDB->fetch($sql);
			if($rs['num_rows'] > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param int $main_treeid (current treeid)
	 * @param bool $live
	 */
	function updateTreeUrl($treeid,$live = false) {

		$arrUrlPart = self::getBaseUrlPart($treeid);
		
		$arrConf = tuksiConf::getPageConf($treeid);
		
		self::updateUrlpart($treeid,$arrUrlPart['urlpart'],$arrUrlPart['urlpartself'],$arrConf['cmssitelangid'],$arrUrlPart['isfrontpage']);
	}
	
	private function updateUrlpart($treeid,$baseurl,$selfurlpart,$cmssitelangid,$isfrontpage){
		
		$objDB = tuksiDB::getInstance();
		if($isfrontpage)
			$currentUrlPart = $baseurl;
		else 	
			$currentUrlPart = $baseurl . ".html";
			
		$arrNodes = self::getSubNodesData($treeid);
		
		$count = count($arrNodes);
		error_log($count);

		$sqlUpd = "UPDATE cmstree SET haschild = '{$count}', cmssitelangid = '{$cmssitelangid}', pg_urlpart_full = '{$currentUrlPart}',pg_urlpart = '{$selfurlpart}' WHERE id = '{$treeid}'";
		
		$rs = $objDB->write($sqlUpd);
		
		
		foreach ($arrNodes as $arrNode) {
			
			$currentUrlPart = tuksiTools::fixname($arrNode['name']);
			
			if($baseurl)
				$currentBaseurl = $baseurl . "/" . $currentUrlPart;
			else
				$currentBaseurl = $currentUrlPart;	
			
			self::updateUrlpart($arrNode['id'],$currentBaseurl,$currentUrlPart,$cmssitelangid,$arrNode['pg_isfrontpage']);
		}
	}
	
	
	private function getBaseUrlPart($treeid,$live = false){
		
		$objDB = tuksiDB::getInstance();
		
		if($live) {
			$table = "cmstreelive";
		} else {
			$table = "cmstree";
		}
		$url = "";
		
		$sql = "SELECT id,parentid, pg_urlpart, pg_isfrontpage,name FROM $table ";
		$sql.= "WHERE id = '$treeid'";
		
		$arrRsTree = $objDB->fetchItem($sql);
		
		if ($arrRsTree['num_rows']) {
				
			$arrTree = $arrRsTree['data'];
			
			$urlPartSelf = tuksiTools::fixname($arrTree['name']);
			
			if ($arrTree['pg_isfrontpage']) {
				
				$arrConf = tuksiConf::getPageConf($treeid);
				
				if($arrConf['urlpart_prefix']) {
					$urlpart = $arrConf['urlpart_prefix'];
				}
				
			} else {
				$arrUrlPart = self::getBaseUrlPart($arrTree['parentid']);				
				
				if($arrUrlPart['urlpart']) {
					$urlpart = $arrUrlPart['urlpart'] . "/" .  $urlPartSelf;
				} else {
					$urlpart = tuksiTools::fixname($arrTree['name']);
				}
			}
			return array('urlpart' => $urlpart,'urlpartself' => $urlPartSelf,'isfrontpage' => $arrTree['pg_isfrontpage']);
		}
		return "";
	}
	
	/**
	 * Denne funktion returnere subnoder.
	 *
	 * @param int $treeid
	 * @return array
	 */
	function getSubNodesData($treeid,$arrField = array('id','name','pg_isfrontpage')) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT ".join(",",$arrField)." FROM cmstree WHERE parentid = '$treeid'";
		$arrNodes = array();
		$rsTree = $objDB->fetch($sql);
		if($rsTree['num_rows'] > 0) {
			foreach ($rsTree['data'] as $arrTree) {
				$arrNodes[] = $arrTree;
			}
		}
		return $arrNodes;
	}
	
	
	/**
	 * Denne funktion returnere ID'ere på subnoder.
	 *
	 * @param int $treeid
	 * @return array
	 */
	function getSubNodes($treeid, $arrFilter = array()) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT id FROM cmstree WHERE parentid = '$treeid'";

		if ($arrFilter['no_deleted']) {
			$sql.= " AND isdeleted = 0 ";
		}
		
		$arrNodes = array();
		$rsTree = $objDB->fetch($sql);
		if($rsTree['num_rows'] > 0) {
			foreach ($rsTree['data'] as $arrTree) {
				$arrNodes[] = $arrTree['id'];
			}
		}
		return $arrNodes;
	}
	
	
	function getAllSubNodes($treeid,$arrNodes = array(), $arrFilter = array()) {
		
		$arr = $this->getSubNodes($treeid, $arrFilter);
		
		if(is_array($arr) && count($arr) > 0) {
			foreach ($arr as $value) {
				$arrNodes[$value] = $value;
				$this->getAllSubNodes($value, $arrNodes, $arrFilter);
			}
			return $arrNodes;
		}
	}
	
	function isLiveTable($tablename){
		
		if(isset($this->liveTables[$tablename])) {
			return $this->liveTables[$tablename];
		}
		
		$objDB = tuksiDB::getInstance();
		
		$sqlTable = "SHOW TABLES LIKE '".$tablename."live'";
		$rsTable = $objDB->fetch($sqlTable) or print mysql_error();
		
		if($rsTable['num_rows'] == 0) {
			$this->liveTables[$tablename] = false;
			return false;
		} else {
			$this->liveTables[$tablename] = true;
			return true;
		}
	}
	
	function liveTreeExists($treeid){
	
		//if(!$this->isLiveTable('cmstree')){
		//	return false;
		//}
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "SELECT id FROM cmstreelive WHERE id = '$treeid'";
		$rsTree = $objDB->fetch($sql);
		if($rsTree['num_rows'] == 0){
			return false;
		} else {
			return true;
		}
	}
	
	function setTreeFolderTypes($treeid){
	
		$arrSubs = $this->getSubNodes($treeid);
		$objDB = tuksiDB::getInstance();
		
		$arrParent = $objDB->fetchRow("cmstree", $treeid);
		
		if(is_array($arrSubs) && count($arrSubs) > 0) {
			
			if($arrParent['cmstreetypeid'] != 1) {
				$sqlUpd = "UPDATE cmstree SET cmstreetypeid = 1 WHERE id = '{$treeid}'";
				$objDB->write($sqlUpd);
			}
			
			foreach ($arrSubs as $subid) {
				$this->setTreeFolderTypes($subid);
			}
			
		} else {	
			
			if($arrParent['cmstreetypeid'] != 4) {
				$sqlUpd = "UPDATE cmstree SET cmstreetypeid = 1 WHERE id = '{$treeid}'";
				$objDB->write($sqlUpd);
			}
		
		}
	}
	function checkTreeName($treeid,$name){
		
		$objDB = tuksiDB::getInstance();
		
		if (strlen($name) > 0 &&	$arrTree = $objDB->fetchRow('cmstree',$treeid)) { 
			
			$lname = strtolower($name);
			
			$sqlChk = "SELECT * FROM cmstree ";
			$sqlChk.= "WHERE parentid = '{$arrTree['parentid']}' AND id <> $treeid AND ";
			$sqlChk.= "(LOWER(name) = '$lname' OR LOWER(pg_menu_name) = '$lname')";
			
			if($rsChk = $objDB->fetch($sqlChk)) {

				if($rsChk['num_rows'] > 0) {
					return false;
				} else {
					return true;
				}
			
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	function getCopyName($treeid,$parentid = 0) {
		
		$objDB = tuksiDB::getInstance();
		
		if ($arrTree = $objDB->fetchRow('cmstree',$treeid)) { 
			
			$baseCopyName = "Kopi af ".$arrTree['name'];
			
			if(!$parentid)
				$parentid = $arrTree['parentid'];
			
			$baseUsed = false;
				
			$sqlSel = "SELECT * FROM cmstree WHERE parentid = '$parentid'";
			$rsSel = $objDB->fetch($sqlSel);
			if ($rsSel['num_rows'] > 0) {

				$arrUsedCopies = array();
				
				foreach($rsSel['data'] as $arrNode) {
					
					if($baseCopyName == $arrNode['name']) {
						$baseUsed = true;
					} else if(preg_match("/kopi \(([0-9]+)\) af ".$arrTree['name']."/i",$arrNode['name'],$m)) {
						$arrUsedCopies[$m[1]] = $m[1];
					}
					
				}
				
				if(count($arrUsedCopies) > 0) {
					
					sort($arrUsedCopies);
					$curCopy = 1;
					$useNb = null;
					foreach ($arrUsedCopies as $nb) {
						if($nb != $curCopy) {
							$useNb = $curCopy;
							break;
						}
						$curCopy++;
					}
					
					if($useNb)
						$newNb = $useNb;
					else
						$newNb = $curCopy;	
					
					$newName = "Kopi ($newNb) af ".$arrTree['name'];
					
					return $newName;
				
				} else {
					if($baseUsed)
						return "Kopi (1) af ".$arrTree['name'];
					else	
						return $baseCopyName;
				}
			
			} else {
				return $baseCopyName;
			}
		}
	}
	
	static function shorturlRedirect($shorturl, $tableext) {
		if ($shorturl !== null) {
			$objDB = tuksiDB::getInstance();
			
			$sqlTree = "SELECT pg_urlpart_full FROM cmstree{$tableext} WHERE pg_shorturl = '{$shorturl}'";
			$arrRsTree = $objDB->fetchItem($sqlTree);
			if ($arrRsTree['ok'] && $arrRsTree['num_rows']) {
				$fullurl = $arrRsTree['data']['pg_urlpart_full'];	
				http_response_code(301);
				header('Location: /' . $fullurl);
				exit();
			}
		}
	}
	
	static function getFrontpageId($treeId) {

		$sqlTree = "SELECT t.id ";
		$sqlTree.= "FROM cmstree t ";
		$sqlTree.= "WHERE t.pg_isfrontpage = '1' AND t.isdeleted = '0' AND t.pg_isactive = '1' ";
		$sqlTree.= "AND t.cmssitelangid > 0 AND t.cmssitelangid = (SELECT c.cmssitelangid FROM cmstree c WHERE c.id = '%d') ";
		$sqlTree = sprintf($sqlTree, $treeId);

		$objDB = tuksiDB::getInstance();
		$arrRsTree = $objDB->fetchItem($sqlTree);
		if ($arrRsTree['ok'] && $arrRsTree['num_rows']) {
			return (int) $arrRsTree['data']['id'];
		}

		return 0;
	}
	
	static function pageIsActive($treeId, $tableext) {
		
		$isActive = false;
		if ($treeId) {
			$objDB = tuksiDB::getInstance();
			
			$sqlTree = "SELECT pg_isactive, pg_isfrontpage, parentid FROM cmstree{$tableext} WHERE id = '{$treeId}'";
			$arrRsTree = $objDB->fetchItem($sqlTree);
			if ($arrRsTree['ok'] && $arrRsTree['num_rows']) {
				$isActive = (int)$arrRsTree['data']['pg_isactive'] == 1;
				if ($isActive) {
					if (!$arrRsTree['data']['pg_isfrontpage'] && $arrRsTree['data']['parentid']) {
						$isActive = self::pageIsActive($arrRsTree['data']['parentid'], $tableext);
					}
				}
			}
		}
		
		return $isActive;
	}
}
?>
