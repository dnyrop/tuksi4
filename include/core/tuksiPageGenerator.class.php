<?
/**
 * Mandatory operations for the pagegenerator system
 * 
 * 
 * @package tuksiBackend
 */
class tuksiPageGenerator {

	static private $instance;
	
	function __construct() {
	}
	
	static function getInstance() {
		if (!self::$instance){
			self::$instance = new tuksiPageGenerator();
		}
		return self::$instance;
	}
	
	public function copyPage($treeid,$copytotreeid,$newname = "",$placement = 1,$copyFrontedModules = true) {
	
		$tuksiTree = tuksiTree::getInstance();
		$objDB = tuksiDB::getInstance();
		
		if($objDB->fetchRow('cmstree',$treeid)) {
			if($arrNew = $tuksiTree->copyTreeNode($treeid,$copytotreeid, 1, 1)) {
		
				if($placement == 2 || $placement == 3) {
					
					switch ($placement) {
						case 2:$action = "before";break;
						case 3:$action = "after";break;
						default:$action = "before";break;
					}
					
					$tuksiTree->moveNode($arrNew['NEWTREEID'],$copytotreeid,$action);
					//get parent
					$arrCopyTree = $objDB->fetchRow('cmstree',$copytotreeid);
					$parentid = $arrCopyTree['parentid'];
				} else {
					$parentid = $copytotreeid;
				}
				
				//sikrer preview
				$sqlIns = "INSERT INTO cmstreeelement (cmsvariableid,value,cmstreeid,cmstreetabid) ";
				$sqlIns.= "VALUES (45,'Preview','{$arrNew['NEWTREEID']}','{$arrNew['NEWTABID'][0]}')";
				$objDB->write($sqlIns);
				
				//checking name
				if($newname) {
					
					if($tuksiTree->checkTreeName($arrNew['NEWTREEID'],$newname)) {
						$newname = $newname;
					} else {
						$newname = $tuksiTree->getCopyName($treeid,$parentid);
					}
				} else {
					$newname = $tuksiTree->getCopyName($treeid,$parentid);
				}
					
				$arrValues = array(	'name' => $newname,
														'pg_browser_title' => $newname,
														'pg_menu_name' => $newname,
														'pg_urlpart' => tuksiTools::fixname($newname),
														'pg_show_settings' => 1);
				
				$strWhere = "id = '{$arrNew['NEWTREEID']}'";

				$objDB->update("cmstree",$arrValues,array(),$strWhere);
				if($copyFrontedModules) {
					$this->copyModules($treeid,$arrNew['NEWTREEID'],$arrNew['tabs']);
				} else {
					$this->copyModules($treeid,$arrNew['NEWTREEID'],$arrNew['tabs'],'backend');
					$this->insertDefaultModules($arrNew['NEWTREEID'],$arrNew['NEWTABID']);
				}
				
				tuksiLog::add(2,$arrNew['NEWTREEID'],'cmstree');
				
				return $arrNew;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
	}
	public function copySubPages($parent,$totree,$arrToCopy) {
		
		$totree = intval($totree);
		$parent = intval($parent);
		
		if($parent > 0 && $totree > 0 && $parent != $totree) {
		
			$tuksiTree = tuksiTree::getInstance();
			
			$arrSubs = $tuksiTree->getSubNodes($parent, array('no_deleted' => true));
			
			if(is_array($arrSubs)) {
				
				foreach ($arrSubs as $subid) {
					
					if($subid != $parent && $subid != $totree && in_array($subid,$arrToCopy)) {
					
						$objDB = tuksiDB::getInstance();
						
						if($arrTree = $objDB->fetchRow("cmstree", $subid)) {
						
							if($arrNew = $this->copyPage($subid,$totree,$arrTree['name'])) {
							
								$sqlUpdate = "UPDATE cmstree SET seq = '{$arrTree['seq']}' WHERE id = '{$arrNew['NEWTREEID']}'";
								$objDB->write($sqlUpdate);
							
								unset($arrToCopy[$subid]);
								
								$this->copySubPages($subid,$arrNew['NEWTREEID'],$arrToCopy);
							}
						}
					}
				}
			}
		}
	}
	public function copyModules($from,$to,$arrTabs,$website = ''){
	
		$objDB = tuksiDB::getInstance();
		
		$from = intval($from);
		$to = intval($to);
		
		if($from > 0 && $to > 0) {
		
			$arrColumns = $objDB->getArrColumns('pg_content');

			foreach($arrColumns as $arrField) {
				if ($arrField['Field'] != 'id' && $arrField['Field'] != 'cmstreeid' && $arrField['Field'] != 'cmstreetabid') {
					$arrFields[] = $arrField['Field'];
				}
			}
			
			$sqlTplInsert = "INSERT INTO pg_content (" . join(", ", $arrFields) . ",cmstreeid,cmstreetabid) ";
			$sqlTplInsert.= "SELECT " . join(", ", $arrFields) . ",$to,'##TABID##' FROM pg_content WHERE id = '##ID##'";
			
			// * ---------------------------------------------------------------------------------- *
			// Henter de tilknyttede moduler for det aktuelle area
			// * ---------------------------------------------------------------------------------- *
			$sqlMod = "SELECT * FROM pg_content ";
			$sqlMod.= "WHERE cmstreeid = '{$from}' ";
			if($website){
				$sqlMod.= " AND website = '$website' ";
			}

			$sqlMod.= "ORDER BY seq";
			$rsMod = $objDB->fetch($sqlMod,array('type' => 'object'));
			
			if($rsMod['ok']) {
				
				foreach ($rsMod['data'] as $objMod) {
			
					$sqlInsert = str_replace("##ID##",$objMod->id,$sqlTplInsert);
					$sqlInsert = str_replace("##TABID##",$arrTabs[$objMod->cmstreetabid],$sqlInsert);
					
					$arrRsIns = $objDB->write($sqlInsert);
				
					$newrowid = $arrRsIns['insert_id'];
						
					$sqlFieldInModule = "SELECT distinct(fi.id), ft.classname, fi.tablename, fi.colname, fi.name, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, fi.helptext ";
					$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldtype ft ";
					$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '".$objMod->pg_moduleid."' AND fi.cmsfieldtypeid = ft.id ";
					$sqlFieldInModule.= "AND ft.special_copy = 1 ";
					$sqlFieldInModule.= "ORDER BY fi.seq";
					
					$rsField = $objDB->fetch($sqlFieldInModule,array('type' => 'object'));
					//traveser de forskellige felttyper for det pågældende modul
					foreach ($rsField['data'] as $objField) {
						
						//sætter de nødvendige variable for klassen
						$objField->htmltagname  = "module_{$objMod->id}_{$objField->id}";
						$objField->value			= $objMod->{$objField->colname};
						$objField->vcolname 		= $objField->htmltagname;
						$objField->rowid 			= $objMod->id;
						
						$this->copyField($objField->classname,$objMod->{$objField->colname},$objField,$newrowid);
					}
				}
			}
		}
	}
	
	
	public function deletePage($treeid){
		
		$objDB = tuksiDB::getInstance();

		$arrValue = array('isdeleted' => '1');
		$arrRaw = array('datedeleted' => 'now()');

		tuksiLog::add(16,$treeid,'cmstree');
		
		$objDB->update('cmstree',$arrValue,$arrRaw,"id='$treeid'");

		$this->deleteSubPages($treeid);
	}

	public function deleteSubPages($parent) {
		
		$parent = intval($parent);
		
		if($parent > 0) {
		
			$tuksiTree = tuksiTree::getInstance();
			
			$arrSubs = $tuksiTree->getSubNodes($parent);
			
			if(is_array($arrSubs)) {
				
				foreach ($arrSubs as $subid) {

					if($subid != $parent) {
					
						$objDB = tuksiDB::getInstance();
						
						if($arrTree = $objDB->fetchRow("cmstree", $subid)) {
		
								$arrValue = array('isdeleted' => '1');
								$arrRaw = array('datedeleted' => 'now()');
								$objDB->update('cmstree', $arrValue, $arrRaw,"id='$subid'");
				
								tuksiLog::add(16, $subid, 'cmstree');
								
								$this->deleteSubPages($subid);
						}
					}
				}
			}
		} // End parent > 0
	}
	
	
	public function releasePage($treeid,$forcerelease = false){
		
		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();

		$objDB = tuksiDB::getInstance();
		
		$treeStatus = $this->checkTreeParents($treeid);
		
		if(!$treeStatus['ok'] && !$forcerelease){
				
			return array('ok' => false, 'errorText' => $treeStatus['errorText']);
	
		} else {
			
			$arrTree = $objDB->fetchRow('cmstree',$treeid);
			
			$sqlFieldInModule = "SELECT distinct(fi.id), fi.*, ft.classname, fg.name as fieldgroupname, ft.special_delete, ft.special_release ";
			$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldperm fp, cmsfieldgroup fg, cmsfieldtype ft, cmsusergroup ug ";
			$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '#MODULEID#' AND fi.cmsfieldtypeid = ft.id AND fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fg.id = fi.cmsfieldgroupid ";
			$sqlFieldInModule.= "ORDER BY fg.seq,fi.seq";
			
			$sqlModules = "SELECT c.* FROM pg_content c, pg_contentarea a ";
			$sqlModules .= "WHERE c.cmstreeid = '{$treeid}' AND a.pg_page_templateid = '".$arrTree['pg_page_templateid'] ."' AND a.id = pg_contentareaid";
			
			$rsModules = $objDB->fetch($sqlModules);
			
			$arrModIds = array();
			
			//logger at siden releases
			tuksiLog::add(4,$treeid,'cmstree');
	
			foreach($rsModules['data'] as $arrMod) {

				// Henter alle fieldtypes
				$sql = str_replace("#MODULEID#", $arrMod['pg_moduleid'], $sqlFieldInModule);
				$arrRsFieldItems = $objDB->fetch($sql,array('type' => 'object'));
		
				//traveser de forskellige felttyper for det pågældende modul
				foreach ($arrRsFieldItems['data'] as $objFieldItem) {
					$arrFieldTypes[$objFieldItem->colname] = $objFieldItem;
				}
				
				//releaser alle fieldtypes 
				tuksiRelease::releaseTableRowFields($arrMod,$arrMod['id'],$arrFieldTypes);
				
				$arrModIds[] = $arrMod['id'];
				// release række
				tuksiRelease::releaseTableRowRaw('pg_content',$arrMod['id']);
			}
			
			// Sletter gamle moduler som var slettet i DEv
			$sqlDeleteOld = "DELETE FROM pg_contentlive ";
			$sqlDeleteOld.= "WHERE cmstreeid = '{$treeid}'";
			if(count($arrModIds) > 0) {
				$sqlDeleteOld.= "AND id NOT IN(" . join(',', $arrModIds) . ")";	
			}
			 
			$objDB->write($sqlDeleteOld);
			
			tuksiRelease::releaseTableRowRaw('cmstree',$treeid);
			tuksiRelease::setTableRowReleaseInfo('cmstree',$treeid);
			
			return array('ok' => true);;
		}	
	}
	
	
	function releaseModule($moduleId){
		
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$arrFieldTypes = array();
		
		$sqlModules = "SELECT * FROM pg_content WHERE id = '$moduleId'";
		$rsModules = $objDB->fetchItem($sqlModules);
		if(!$rsModules['ok'] || $rsModules['num_rows'] == 0) {
			return false;
		}
		
		$arrMod = $rsModules['data'];
		
		$sqlFieldInModule = "SELECT distinct(fi.id), fi.*, ft.classname, fg.name as fieldgroupname, ft.special_delete, ft.special_release ";
		$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldperm fp, cmsfieldgroup fg, cmsfieldtype ft, cmsusergroup ug ";
		$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '{$arrMod['pg_moduleid']}' AND fi.cmsfieldtypeid = ft.id AND fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fg.id = fi.cmsfieldgroupid ";
		$sqlFieldInModule.= "ORDER BY fg.seq,fi.seq";
		
		$arrRsFieldItems = $objDB->fetch($sqlFieldInModule,array('type' => 'object'));
		
		//traveser de forskellige felttyper for det pågældende modul
		foreach ($arrRsFieldItems['data'] as $objFieldItem) {
			$arrFieldTypes[$objFieldItem->colname] = $objFieldItem;
		}
		
		//releaser alle fieldtypes 
		tuksiRelease::releaseTableRowFields($arrMod,$arrMod['id'],$arrFieldTypes);
		
		$arrModIds[] = $arrMod['id'];
		
		// release række
		tuksiRelease::releaseTableRowRaw('pg_content',$arrMod['id']);
	}
	
	function releaseSubPages($treeid) {
	
		$objDB = tuksiDB::getInstance();
		
		$tuksiTree = tuksiTree::getInstance();
		
		$arrSubs = $tuksiTree->getSubNodes($treeid);
		
		if(is_array($arrSubs)) {
	
			foreach ($arrSubs as $subid) {
				
				$arrTree = $objDB->fetchRow("cmstree", $subid);
				
				$arrNew = $this->releasePage($subid,true);
				
				$this->releaseSubPages($subid);			
				
			}
		}	
	}
	
	function checkTreeParents($childId){
	
		$objDB = tuksiDB::getInstance();
		$objText = tuksiText::getInstance();
		
		$sqlTable = "SHOW TABLES LIKE 'cmstreelive'";
		$arrRsTable = $objDB->fetch($sqlTable);
		if($arrRsTable['num_rows'] == 0) {
						return array('ok' => false,'errorText' => $objText->getText('error_release_setup'));
		}
		
		$arrConf = tuksiConf::getPageConf($childId);
		
		if($arrConf['rootid'] != $childId) {
			$currentId = $childId;
			$live = false;
			for($i = 0;$i < 15;$i++) {
				if($currentId = $this->getParent($currentId,$live)) {
					if($currentId == $arrConf['rootid']) {
						return array('ok' => true);
					}
				} else {
								return array('ok' => false,'errorText' => $objText->getText('error_release_parent_pages'));
				}
				$live = true;
			}
		} else {
			return array('ok' => true);
		}
	}
	
	function getParent($treeid,$live = false) {
		
		$objDB = tuksiDB::getInstance();
		
		if($live)
			$table = "cmstreelive";
		else	
			$table = "cmstree";
		
		if($arrTree = $objDB->fetchRow($table,$treeid)) {
			return $arrTree['parentid'];
		} else {
			return false;
		}
	}

	private function copyField($fieldType,$value,$objField,$newrowid){
		if(is_callable(array($fieldType,'copyData'))) {
			$objNewField = new $objField->classname($objField);
			$objNewField->copyData($newrowid);
		} 
	}
	
	
	public function getTreeForSelect($treeid, $curpage = "",$level =0,$arrFolder = array(),$useperm = true, $haschild = false) {
		
		$objDB = tuksiDB::getInstance();
		
		if ($curpage != "") {
			$arrFolder[] = array(
				'id' => $treeid,
				'name' => $curpage,
				'selectname' => str_repeat('&nbsp;&nbsp;&nbsp;',$level) . $curpage
			);
			$level++;
		}
		
		if (is_numeric( $haschild) && $haschild ==  0) {
			return $arrFolder;	
		}

		$arrConf = tuksiConf::getConf();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		if($useperm) {
			$sqlNodes = "SELECT DISTINCT t.id,t.name ";
			$sqlNodes.= ", txt.value_{$arrConf['setup']['admin_lang']} AS namelang, t.cmssitelangid ";
			$sqlNodes.= ", t.haschild ";
			$sqlNodes.= "FROM (cmstree t, cmsperm p, cmsusergroup ug) ";
			$sqlNodes.= "LEFT JOIN cmstext txt ON (t.name = txt.token) ";
			$sqlNodes.= "WHERE t.parentid = '{$treeid}' AND ug.cmsuserid = '" . $arrUser['id'] . "' AND p.itemtype = 'tree' AND t.isdeleted = 0 AND show_inmenu = 1 ";
			$sqlNodes.= "AND p.cmsgroupid = ug.cmsgroupid AND p.rowid = t.id AND p.pread = 1 ";
			$sqlNodes.= "ORDER BY t.seq";
		} else {
			$sqlNodes = "SELECT DISTINCT t.id,t.name ";
			$sqlNodes.= ", txt.value_{$arrConf['setup']['admin_lang']} AS namelang, t.cmssitelangid ";
			$sqlNodes.= ", t.haschild ";
			$sqlNodes.= "FROM (cmstree t) ";
			$sqlNodes.= "LEFT JOIN cmstext txt ON (t.name = txt.token) ";
			$sqlNodes.= "WHERE t.parentid = '{$treeid}' AND t.isdeleted = 0 AND show_inmenu = 1 ";
			$sqlNodes.= "ORDER BY t.seq";
		}
		
		$rs = $objDB->fetch($sqlNodes);
		
		if($rs['num_rows'] > 0) {
			foreach ($rs['data'] as $arrData) {
				if($arrData['namelang'])
					$arrData['name'] = $arrData['namelang'];
				
				if($curpage != ""){
					$arrFolder = $this->getTreeForSelect($arrData['id'], $arrData['name'],$level,$arrFolder,$useperm, $arrData['haschild']);
				}else{
					$arrFolder = $this->getTreeForSelect($arrData['id'], $arrData['name'], $level,$arrFolder,$useperm, $arrData['haschild']);	
				}
			}
		} 
		return $arrFolder;	
	}
	public function insertDefaultModules($treeid,$tabid,$templateid = 0){
		
		$objDB = tuksiDB::getInstance();
		
		if($templateid == 0) {
			$sqlTemplate = "SELECT pg_page_templateid FROM cmstree WHERE id = '$treeid'";
			$rsTemplate = $objDB->fetchItem($sqlTemplate);
			if($rsTemplate['ok'] && $rsTemplate['num_rows'] > 0) {
				$templateid =  $rsTemplate['data']['pg_page_templateid'];
			} else {
				return false;
			}
		}
		
		$sqlArea = "SELECT * FROM pg_contentarea ";
		$sqlArea.= "WHERE pg_page_templateid = '" . $templateid . "' ORDER BY seq";
		$rsArea 	= $objDB->fetch($sqlArea);
		
		if($rsArea['ok'] && $rsArea['num_rows'] > 0) {
			foreach ($rsArea['data'] as $arrArea) {
				
				//henter alle default moduler for det aktuelle area
				$sqlDefault = "SELECT m.id,d.not_delete,d.seq,d.placement ";
				$sqlDefault.= "FROM pg_module m, pg_defaultmodules d ";
				$sqlDefault.= "WHERE d.pg_contentareaid = '{$arrArea['id']}' AND d.pg_moduleid = m.id ORDER BY d.seq";
				$rsDefault = $objDB->fetch($sqlDefault);
				
				if($rsDefault['ok'] && $rsDefault['num_rows'] > 0) {
					foreach ($rsDefault['data'] as $arrDefault) {
						$sqlNew = "INSERT INTO pg_content (pg_contentareaid, pg_moduleid, cmstreeid,cmstreetabid, not_delete,placement,seq) VALUES ";
						$sqlNew.= "({$arrArea['id']}, '{$arrDefault['id']}', '{$treeid}','{$tabid}','{$arrDefault['not_delete']}','{$arrDefault['placement']}','{$arrDefault['seq']}')";
						$rsNew = $objDB->write($sqlNew);
					}
				}
			}
		}
	}
	
	
	function resetModules($treeid,$tabid){
	
		$objDB = tuksiDB::getInstance();	
		
		// * ---------------------------------------------------------------------------------- *
		// Henter de tilknyttede moduler for det aktuelle area
		// * ---------------------------------------------------------------------------------- *
		$sqlMod = "SELECT * FROM pg_content WHERE cmstreeid = '{$treeid}' AND cmstreetabid = '{$tabid}' AND website = 'frontend' ORDER BY seq";
		$rsMod = $objDB->fetch($sqlMod,array('type' => 'object'));
		foreach($rsMod['data'] as $objMod){
				
			$sqlFieldInModule = "SELECT distinct(fi.id), ft.classname,fi.tablename, ft.libraryfile, fi.colname, fi.name, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, fi.helptext ";
			$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldtype ft ";
			$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '".$objMod->pg_moduleid."' AND fi.cmsfieldtypeid = ft.id ";
			$sqlFieldInModule.= "ORDER BY fi.seq";
			
			$rsField = $objDB->fetch($sqlFieldInModule,array('type' => 'object'));
			
			//traveser de forskellige felttyper for det pågældende modul
			foreach ($rsField['data'] as $objField) {
				
				//sætter de nødvendige variable for klassen
				$objField->htmltagname  = "module_{$objMod->id}_{$objField->id}";
				$objField->value				= $objMod->{$objField->colname};
				$objField->vcolname 		= $objField->htmltagname;
				$objField->rowid 				= $objMod->id;
				$objField->rowData 			= $objMod;
				
				$this->deleteItem($objField->classname,$objMod->{$objField->colname},$objField);
			}
		}
		$sqlDel = "DELETE FROM pg_content WHERE cmstreeid = '$treeid' AND cmstreetabid = '{$tabid}' AND website = 'frontend'";
		$objDB->write($sqlDel);
	}
	
	function deleteItem($fieldType,$value,$objField){
	
		if(is_callable(array($fieldType,'deleteData'))) {
			$objNewField = new $objField->classname($objField);
			$objNewField->deleteData();
		} 
	}
	
}
?>
