<?php

/**
 * class for handling elements in pagegenerator
 *
 * @package tuksiBackend
 */

class tuksiPageGeneratorElements {
	
	public $treeid,$tabid,$areaid,$templatetype,$isBackend,$tplField;
	public $openSingle = true;
	public $openAll = false;
	public $templateLoaded = false;
	public $arrElements = array();
	public $arrPageTemplate = array();
	public $arrArea = array();
	public $arrOptions= array();
	public $moveable = false;
	

	static $arrBaseMod = array();

	public function __construct($treeid,$tabid,$areaid,$isBackend = false,$arrOptions = array()) {
		
		$this->treeid = $treeid;
		$this->tabid = $tabid;
		$this->areaid = $areaid;
		$this->isBackend = $isBackend;
		$this->arrOptions = $arrOptions;
		
		if(isset($arrOptions['openSingle'])) {
			$this->openSingle = $arrOptions['openSingle'];
		}
		if(isset($arrOptions['openAll'])) {
			$this->openAll = $arrOptions['openAll'];
		}
	}
	
	public function loadTemplate($force = false) {
		
		if(!$this->templateLoaded && !$force) {
		
			$objDB = tuksiDB::getInstance();
			
			$this->arrTree = $objDB->fetchRow("cmstree", $this->treeid);
			$this->arrTab = $objDB->fetchRow("cmstreetab", $this->tabid);
				
			//templates for backend is stored in different field
			if($this->isBackend) {
		
				//load Template
				if(($this->arrPageTemplate = $objDB->fetchRow("pg_page_template", $this->arrTab['cms_page_templateid'])) !== false) {
					$this->page_templateid = $this->arrTab['cms_page_templateid'];
				}
			} else {
				if(($this->arrPageTemplate = $objDB->fetchRow("pg_page_template", $this->arrTree['pg_page_templateid'])) !== false) {
					$this->page_templateid = $this->arrTree['pg_page_templateid'];
				}
			}
			
			if(!$this->page_templateid){
				$this->arrError[] = "No template set for page";
				return false;
			}
			
			
			// if areaid isnt set load first area
			if (!$this->areaid) {
				
				if($this->isBackend) {
					$sqlArea = "SELECT c.*, t.id AS tabid FROM pg_contentarea c, cmstreetab t ";
					$sqlArea.= "WHERE c.pg_page_templateid = t.cms_page_templateid AND t.id = '{$this->tabid}' AND c.pg_page_templateid > 0 ";
					$sqlArea.= "ORDER BY seq LIMIT 1";
				} else {
					$sqlArea = "SELECT c.*, t.id AS tabid FROM pg_contentarea c, cmstree t ";
					$sqlArea.= "WHERE c.pg_page_templateid = t.pg_page_templateid AND t.id = '{$this->treeid}' AND c.pg_page_templateid > 0 ";
					$sqlArea.= "ORDER BY seq LIMIT 1";
					
				}
				
				$arrRsArea = $objDB->fetch($sqlArea);
				
				if($arrRsArea['ok'] && $arrRsArea['num_rows'] > 0) {
					$this->arrArea = $arrRsArea['data'][0];
					$this->areaid = $arrRsArea['data'][0]['id'];
				} else {
					$this->arrError[] = "Couldn't load any area";
					return false;
				}
			} else {
				if(($this->arrArea = $objDB->fetchRow("pg_contentarea", $this->areaid)) !== false) {
					$this->areaid = $this->arrArea['id'];
				} else {
					$this->arrError[] = "Couldn't load any area";
					return false;
				}
			}
			$this->templateLoaded = true;
		}
		return true;
	}
	
	public function getAllowedElements() {
		
		$this->loadTemplate();
		
		$objDB = tuksiDB::getInstance();
		$arrAllowedModules = array();
		
		if($this->arrPageTemplate['template_type'] == 2) {
			$sqlModuleType = " AND m.moduletype = 'newsletter' ";
		} else if($this->arrPageTemplate['template_type'] == 1) {
			$sqlModuleType = " AND (m.moduletype = 'standard' OR m.moduletype = 'custom') ";
		} else {
			$sqlModuleType = "";
		}
		
		//henter alle de tilknyttede moduler for det aktuelle area
		$sqlAllow = "SELECT m.* FROM pg_module m, pg_allowedmodules a ";
		$sqlAllow.= "WHERE m.isactive = 1 AND a.pg_contentareaid = '{$this->areaid}' AND a.pg_moduleid = m.id ";
		$sqlAllow.= $sqlModuleType;
		$sqlAllow.= "ORDER BY m.name";
		$arrRsAllow = $objDB->fetch($sqlAllow);
		if ($arrRsAllow['ok'] && $arrRsAllow['num_rows'] > 0) {
			$arrAllowedModules = $arrRsAllow['data'];
		}
		
		return $arrAllowedModules;
	}
	
	public function getInsertedElements($arrOpen = array(),$arrClose = array()){
		
		$this->loadTemplate();
		
		$objDB = tuksiDB::getInstance();
		//$objPage = tuksiBackend::getInstance();
		$arrConf = tuksiConf::getConf();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$intSeqCount = 0;
		
		//get number of elements that should always be on top
		$sqlModSeq = "SELECT * FROM pg_content ";
		$sqlModSeq.= "WHERE cmstreeid = '{$this->treeid}' AND pg_contentareaid = '{$this->areaid}' AND placement > 0 ";	
		if($this->isBackend) {
			$sqlModSeq.= "AND cmstreetabid = '{$this->tabid}' AND website = 'backend' ";
		} else {
			$sqlModSeq.= "AND website = 'frontend' ";
		}
		$arrRsMod = $objDB->fetch($sqlModSeq);
		$intDiffSeq = $arrRsMod['num_rows'];
		
		//load elements
		$sqlMod = "SELECT * FROM pg_content ";
		$sqlMod.= "WHERE cmstreeid = '{$this->treeid}' AND pg_contentareaid = '{$this->areaid}' ";
		if($this->isBackend) {
			$sqlMod.= " AND cmstreetabid = '{$this->tabid}' AND website = 'backend' ";
		} else {
			$sqlMod.= "AND website = 'frontend' ";
		}

		// Hent et enkelt module ud
		if ($this->arrOptions['moduleid']) {
			$this->openAll = true;
			$sqlMod .= "AND id = '" . intval($this->arrOptions['moduleid']) . "'";
		}
		$sqlMod.= "ORDER BY seq";	

		//print $sqlMod . '<br>';
		$arrRsMod = $objDB->fetch($sqlMod);
		
		$arrModules = array();
		
		$intNumberModules = $arrRsMod['num_rows'];
		$intMax = $intNumberModules - $intDiffSeq;
		
		unset(self::$arrBaseMod[$arrMod['pg_moduleid']]);

		foreach ($arrRsMod['data'] as &$arrMod) {
			
			if(!$arrMod['placement']) {
				$intSeqCount++;				
			}
			$isOpen = false;
			
			if (in_array($arrMod['id'],$arrClose)) {
				$isOpen = false;
			} elseif(in_array($arrMod['id'],$arrOpen)) {
				$isOpen = true;
			}
			
			//Chekker om det aktuelle modul er åbnet og sætter diverse variable for html
			// Åben modul, hvis der kun er en.
			
			if ($isOpen == true || $this->openAll == true || ($intNumberModules == 1 && $this->openSingle == true)) {
				$arrMod['isopen'] = true;
			} else {
				$arrMod['isopen'] = false;
			}
		
		   //checker om modulet ikke er det første og tilføjer derefter html for pil ellers tom html
			if ($intSeqCount > 1 && !$arrMod['placement']) { 
				$arrMod['arrowUp'] = true;
				$this->moveable = true;
			} 
			//checkker om modulet det sidste tilføjer derefter tom html ellers med pil ned
			if ($intSeqCount != $intMax && !$arrMod['placement'])	{
				$arrMod['arrowDown'] = true;
				$this->moveable = true;
			}
			//formnavn for om modullet er åbent (hidden)
			//henter modulet
			if (isset(self::$arrBaseMod[$arrMod['pg_moduleid']])) {
				$objBaseMod = self::$arrBaseMod[$arrMod['pg_moduleid']];
			} else {
				$objBaseMod = $objDB->fetchRow("pg_module", $arrMod['pg_moduleid'], 'object'); //tab objektet
				self::$arrBaseMod[$arrMod['pg_moduleid']] = $objBaseMod;
			}
			$name = $objBaseMod->name;
			
			//chekker om modulet har et navn eller en overskrift og tilføjer dette til html'en
			if ($arrMod['name']) {
				$name.= " : ".$arrMod['name'];
			} elseif($arrMod['headline']) {
				$name.= " : ". $arrMod['headline'];
			}
			
			$arrMod['modname'] = $name;
			
			if(!$arrMod['not_delete']){
				$arrMod['delete'] = true;
			}
					
			// Vis kun link og luk knap hvis der er mere end 1 modul
			// Modulet bliver derefter åbnet automatisk
			if ($intNumberModules > 1) {
				$arrMod['linkname'] = true;
			} 
			

	
			//get setuplink
			if($objBaseMod->moduletype != '' && isset($arrUser['usergroup'][1])) {
				$arrMod['setuplink'] = "/{$arrConf['setup']['admin']}/?treeid=" . $arrConf['link']['moduleadmin_treeid'] ."&tabid=". $arrConf[$objBaseMod->moduletype .'_moduleadmin_tabid']."&rowid=".$objBaseMod->id;
			} 
									
			if($objPage->arrPerms['RELEASEPAGE']){
				$arrMod['release'] = true;
			} else {
				$arrMod['release'] = false;
			}
			
				//hvis modulet er åbnet skal der loades html for dette
			if ($arrMod['isopen']) {
		
				// * ---------------------------------------------------- *
				// Getting fieldtypes for current module
				// * ---------------------------------------------------- *
				$sqlFieldInModule = "SELECT distinct(fi.id),fi.id as fielditemid, ft.id as cmsfieldtypeid,ft.classname, fi.colname, fi.name, ";
				$sqlFieldInModule.= "fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, ";
				$sqlFieldInModule.= "fi.helptext,fg.name as fieldgroupname,fi.cmsfieldgroupid, txt.value_" . tuksiIni::$arrIni['setup']['admin_lang'] . " AS langname ";
				$sqlFieldInModule.= "FROM (cmsfielditem fi, cmsfieldperm fp, cmsfieldgroup fg, cmsfieldtype ft, cmsusergroup ug) ";
				$sqlFieldInModule.= "LEFT JOIN cmstext txt ON (txt.token = fi.name) ";
				$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '#MODULEID#' AND fi.cmsfieldtypeid = ft.id AND fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fg.id = fi.cmsfieldgroupid  AND fp.cmsfielditemid = fi.id ";
				$sqlFieldInModule.= "ORDER BY fg.seq,fg.id,fi.seq";
				
				$sql = str_replace("#MODULEID#", $arrMod['pg_moduleid'], $sqlFieldInModule);
			
				$arrRsField = $objDB->fetch($sql,array("type" => "object"));
		
				//returnerer vores query noget 
				if ($arrRsField['ok']) {
				
					$currentFieldGroup = 0;
					$nbFieldGroups = 0;
					
					//traveserer felttyperne for det aktuelle modul
					foreach($arrRsField['data'] as &$objFieldData) {
						
						//er feltnavnet sat
					  if (c) {
	
							//finder gruppen
							if($objFieldData->cmsfieldgroupid != $currentFieldGroup) {
								$arrMod['fields'][] = array('html' => '<h2>' . $objFieldData->fieldgroupname . '</h2>');
								$currentFieldGroup = $objFieldData->cmsfieldgroupid;
								$nbFieldGroups++;
							}

							 // Sætter standard værdier til hver felt
							if (!empty($objFieldData->langname))
								$objFieldData->name = $objFieldData->langname;
							
							//sætter de nødvendige variable for klassen
							$objFieldData->htmltagname = "module_{$arrMod['id']}_{$objFieldData->id}";
							$objFieldData->vcolname		= $objFieldData->htmltagname; 
							$objFieldData->rowid			= $arrMod['id']; // pg_contentid
							$objFieldData->tablename	= "pg_content";
							$objFieldData->value 		= $arrMod[$objFieldData->colname];
							$objFieldData->rowData 		= $arrMod;
							
							//opretter object af klassen
							$objField = new $objFieldData->classname($objFieldData);
							//henter html for klasssen
							$arrData = $objField->getHTML();
							
							if(!is_array($arrData['html'])) {
								$arrData['html'] = array($arrData['html']);
							}
							
							//Tilføjer colname til fields, så isactive kan findes ved TuksiEdit - KCH
							$arrData['colname'] = $objFieldData->colname;
							
							$arrMod['fields'][] = $arrData;
		
					   } else 
							$arrMod['modhtml'] = "Fieldname missing in moduleID {$arrMod['pg_moduleid']}<br>";
					} // END While each fieldtype in module	
				} else {
					$arrMod['modhtml'] = "Error getting fields for moduleID {$arrMod['pg_moduleid']}<br>";
				}
			} // END is open	
			
			
			//check for nbGroups
			if(isset($nbFieldGroups) && $nbFieldGroups == 1) {
				unset($arrMod['fields'][0]);
			}
			
			$arrModules[] = $arrMod;
			
		} // end while modules
		
		return $arrModules;
		
		//tilføjer moduler til template
		//$tplPG->assign('modules',$arrModules);
		
		//checkkker om det aktuelle area overholder reglerne der er sat for det
		//$errorArea = $this->checkArea($intMax,$objArea);
		
		//findes der fejl printes disse ud til brugeren
		//if($errorArea && $htmlAllowedModules != '') {
			//$this->objStdTpl->addElement("",$errorArea);
		//}
		
		//indsætter html for modulet i sidens html
		//$this->objStdTpl->addElement("",$tplPG->fetch('pagegenerator.tpl'));
	}
	
	public function getFieldsInModule($moduleid) {
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$sqlFieldInModule = "SELECT distinct(fi.id), fi.*,fi.id as fielditemid, ft.classname, fg.name as fieldgroupname, ft.special_delete, ft.special_release ";
		$sqlFieldInModule.= "FROM cmsfielditem fi, cmsfieldperm fp, cmsfieldgroup fg, cmsfieldtype ft, cmsusergroup ug ";
		$sqlFieldInModule.= "WHERE fi.itemtype = 'pg' AND fi.relationid = '". $moduleid ."' AND fi.cmsfieldtypeid = ft.id ";
		$sqlFieldInModule.= "AND fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fg.id = fi.cmsfieldgroupid AND fp.cmsfielditemid = fi.id ";
		$sqlFieldInModule.= "ORDER BY fg.seq,fi.seq";	
		
		$arrRsFieldInModule = $objDB->fetch($sqlFieldInModule,array("type" => "object"));
		if($arrRsFieldInModule['ok'] && $arrRsFieldInModule['num_rows'] > 0) {
			return $arrRsFieldInModule['data'];
		} else {
			return false;
		}
	}
	
	private function getFirstModule(){
		return $this->getSingleModule(true);
	}
	private function getLastModule(){
		return $this->getSingleModule(false);
	}
	
	private function getSingleModule($first = false){
		
		$objDB = tuksiDB::getInstance();
		
		//load elements
		$sqlMod = "SELECT * FROM pg_content ";
		$sqlMod.= "WHERE cmstreeid = '{$this->treeid}' AND pg_contentareaid = '{$this->areaid}' ";
		if($first) {
			$sqlMod.= "ORDER BY seq asc ";
		} else {
			$sqlMod.= "ORDER BY seq desc ";
		}
		$sqlMod.= "LIMIT 1";
		$arrRsMod = $objDB->fetch($sqlMod);
		if($arrRsMod['ok'] && $arrRsMod['num_rows'] > 0) {
			return $arrRsMod['data'][0];	
		}		
	}
	
	public function addElement($moduleid,$arrPlacement = array()){
		
		$objDB = tuksiDB::getInstance();
		$seq = 0;
		
		if(isset($arrPlacement['first']) && $arrPlacement['first']) {
			//find lowest seq
			$arrMod = $this->getFirstModule();
			$seq = $arrMod['seq'] - 100;
		} elseif(isset($arrPlacement['last']) && $arrPlacement['last']) {
			//find highest seq
			$arrMod = $this->getLastModule();
			$seq = $arrMod['seq'] + 100;
		}  
		
		$sqlNew = "INSERT INTO pg_content (isactive, pg_contentareaid, pg_moduleid, cmstreeid, dateadded, seq, cmstreetabid, website) ";
		
		if($this->isBackend) {
			$website = "backend";
		} else {
			$website = "frontend";
		}
		
		$sqlNew.= "VALUES (1,{$this->areaid}, '{$moduleid}', '$this->treeid',now(),'$seq','".$this->tabid."','$website')";
		$arrRsNew = $objDB->write($sqlNew);
		
		if($arrRsNew['ok']) {
			tuksiLog::treeAction('moduleadded',$this->treeid,'',$arrRsNew['insert_id'],$this->areaid,$moduleid);
			return $arrRsNew['insert_id'];
		} else {
			return false;
		}
	}
	
	public function getElementsFromArea(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlMod = "SELECT * FROM pg_content ";
		$sqlMod.= "WHERE cmstreeid = '".$this->treeid."' AND pg_contentareaid = '".$this->areaid."' ";
		if($this->isBackend) {
			$sqlMod.= "AND cmstreetabid = '".$this->tabid."' AND website = 'backend' ";
		} else {
			$sqlMod.= "AND website = 'frontend' ";
		}
		$sqlMod.= "ORDER BY seq";	
		$arrRsMod = $objDB->fetch($sqlMod,array('type' => 'object'));
		
		if($arrRsMod['ok'] && $arrRsMod['num_rows'] > 0) {
			return $arrRsMod['data'];
		} else {
			return false;
		}
	}
	
		public function getElementsFromAreaForSelect(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlMod = "SELECT c.*,m.name as modname FROM pg_content c,pg_module m ";
		$sqlMod.= "WHERE c.cmstreeid = '".$this->treeid."' AND c.pg_contentareaid = '".$this->areaid."' AND m.id = c.pg_moduleid ";
		if($this->isBackend) {
			$sqlMod.= "AND c.cmstreetabid = '".$this->tabid."' AND c.website = 'backend' ";
		} else {
			$sqlMod.= "AND c.website = 'frontend' ";
		}
		$sqlMod.= "ORDER BY c.seq";	
		
		$arrRsMod = $objDB->fetch($sqlMod);
		
		if($arrRsMod['ok'] && $arrRsMod['num_rows'] > 0) {
			return $arrRsMod['data'];
		} else {
			return false;
		}
	}
	
	
	public function deleteElementById($elementid){
		
		$objDB = tuksiDB::getInstance();
		
		$sql = "DELETE FROM pg_content WHERE id = '" . $elementid . "'";
		$objDB->write($sql);
		
		tuksiLog::treeAction('moduledeleted',$this->treeid,'',$elementid,$this->areaid);
	}
	
	public function updateElementSQL($moduleid,$sqlPart) {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlSave = "UPDATE pg_content SET $sqlPart WHERE id = '" . $moduleid . "'";
		
		$arrRsSave = $objDB->write($sqlSave);
		if($arrRsSave['ok']) {
			return true;
		} else {
			return false;
		}
	}
	
	public function arrangeElements($arrModules, $contentareaid = ''/* KCH - der skal vel noget check på om modulet må ligge her? */){
		
		$objDB = tuksiDB::getInstance();
		
		$intSeq = 100;
		foreach($arrModules as $modId){
			$sqlUpd = "UPDATE pg_content SET seq = '$intSeq' ";
			$sqlUpd.= ($contentareaid) ? ", pg_contentareaid = $contentareaid " : "";
			$sqlUpd.= "WHERE id = '$modId' ";
			$objDB->write($sqlUpd);
			$intSeq+= 100;	
		}
	}
}
?>
