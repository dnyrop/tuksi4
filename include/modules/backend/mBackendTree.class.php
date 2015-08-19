<?
/**
 * Tree administration
 * 
 * @uses tuksiSmarty
 * @uses tuksiDB
 * @uses tuksiBackend
 * @uses tuksiPerm
 * @uses tuksiJSON
 * @uses tuksiTree
 * @uses tuksiPageSetttings
 * @uses tuksiStandardTemplateControl
 * @uses tuksiPageGeneratorElementsHtml
 * @package tuksiBackendModule
 */

class mBackendTree extends mBackendBase {
	
	private $currentNodeId = 0;
	private $currentTabId;
	private $areaid = 0;
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		
		$objPage = tuksiBackend::getInstance();
		
		if($_POST->getInt('changeNode_'.$objMod->id)) {
			$this->currentNodeId = $_POST->getInt('changeNode_'.$objMod->id);
		} elseif($_POST->getInt('currenttreeid')){
			$this->currentNodeId = $_POST->getInt('currenttreeid');
		} else if($_GET->getInt('nodeid')){
			$this->currentNodeId = $_GET->getInt('nodeid');
			$fromGet = true;
		}
		
		if($this->currentNodeId == $_POST->getInt('currenttreeid') || $fromGet) {
			
			if($fromGet) {
				if($_GET->getInt('nodetabid')){
					$this->currentTabId = $_GET->getInt('nodetabid');
				} else {
					$this->currentTabId = 0;
				}
				if ($_GET->getInt('areaid')) {
					$this->areaid = $_GET->getInt('areaid');
				} else {
					$this->areaid = 0;
				}
				
			} else {

				if($_POST->getInt('changeTab_'.$objMod->id)) {
					$this->currentTabId = $_POST->getInt('changeTab_'.$objMod->id);
				} elseif($_POST->getInt('currenttabid')) {
					$this->currentTabId = $_POST->getInt('currenttabid');
				}
				
				if($this->currentTabId && ($this->currentTabId == $_POST->getInt('currenttabid'))) {
					
					if($_POST->getInt('changeArea_'.$objMod->id)) {
						$this->areaid = $_POST->getInt('changeArea_'.$objMod->id);
					} elseif ($_POST->getInt('currentareaid')){
						$this->areaid = $_POST->getInt('currentareaid');;
					}
				} else {
					$this->areaid = 0;
				}
			}
		}	else {
			$this->currentTabId = 0;
			$this->areaid = 0;
		}
		
		if($this->userActionIsSet("GOTOTREE") && $objPage->arrPerms["SAVE"]) {
			header("Location: ".$objPage->getUrl($this->currentNodeId,$this->currentTabId));
			exit();
		}
	}
	
	function getHtml(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		if($objPage->bookmarkSet()){
			$this->addButton("BTNBACK","","READ");
		}
		
		if($this->userActionIsSet('ADD') && $objPage->arrPerms["ADD"]) {
			if($_POST->getStr('json')) {
				$strJSON = $_POST->getStr('json');
				$json = new tuksiJSON();
				$arrValues = $json->parse($strJSON);
				if($arrValues->addNode) {
					if(is_numeric($arrValues->addNodeParent) && strlen($arrValues->addNodeName) > 0) {
						$arrReturnTree = tuksiTree::addTreeNode($arrValues->addNodeParent,$arrValues->addNodeName);
						if($arrReturnTree[0] > 0) {
							$this->currentNodeId = $arrReturnTree[0];
							if($arrValues->addNodeTab) {
								$arrReturnTab = tuksiTree::addTab($this->currentNodeId,'Default');
								if($arrReturnTab[0] > 0) {
									$this->currentTabId = $arrReturnTab[0];
								}
							}
						}
					}
				}
			}
		}
		
		if($objDB->fetchRow("cmstree",$this->currentNodeId) !== false) {
				
			$objSettings = new tuksiPageSetttings($this->currentNodeId);
			
			if($this->userActionIsSet('SAVE') && $objPage->arrPerms["SAVE"]) {
				$objPage->status($objPage->cmsText('treesaved'));
				$objSettings->save();
				$this->saveData();
				if($_POST->getStr('json')) {
					$strJSON = $_POST->getStr('json');
					$json = new tuksiJSON();
					$arrValues = $json->parse($strJSON);
					if($arrValues->addTab) {
						$newName = $arrValues->addTabName;
						$newTemplateId = $arrValues->addTabTemplate;
						$objTree = tuksiTree::getInstance();
						$arrReturn = $objTree->addTab($this->currentNodeId,$newName,array('cms_page_templateid' => $newTemplateId));
						if($arrReturn[0] > 0) {
							$this->currentTabId = $arrReturn[0];
						}
					}
					if($arrValues->deleteTab) {
						$objTree = tuksiTree::getInstance();
						$objTree->deleteTab($this->currentTabId);
						$this->currentTabId = 0;
					}
				}
			}
			
		}
		
		if($this->userActionIsSet('DELETE') && $objPage->arrPerms["DELETE"]) {
				if($this->currentNodeId > 0) {
						tuksiTree::deleteTreeNode($this->currentNodeId);
						$this->currentNodeId = 0;
						$this->currentTabId = 0;
						$this->areaid = 0;
						$objPage->status($objPage->cmsText('treedeleted'));
				}
		}
		
		
		
		$objStdTpl = new tuksiStandardTemplateControl();
		$objStdTpl->addHeadline($objPage->cmstext('node_administration'));
		
		$arrNodes = $this->getNodes(0);
		
		$objStdTpl->addSelectElement("Node",array("name" 			=> "nodes_".$this->objMod->id, 
																							"class" 		=> "forminput600",
																							"options" 	=> $arrNodes,
																							"width" 	=> 500,
																							"selected"	=> $this->currentNodeId,
																							"onchange"	=> "document.getElementById('changeNode_{$this->objMod->id}').value = this.options[this.options.selectedIndex].value;doAction('".$this->getActionName("UPDATE")."');"));
																							
		if($this->currentNodeId > 0) {
			
			if($objDB->fetchRow("cmstree",$this->currentNodeId) !== false) {
				
				if(!isset($objSettings)) {
					$objSettings = new tuksiPageSetttings($this->currentNodeId);
				}
				
				//henter html for indstillingerne igennem tableview
				$arrHtml = $objSettings->getHTML() ;
				
				if (is_array($arrHtml)){
					//check if contextid is set and don't show anything if now
					foreach($arrHtml as $arrData) {
			   		$objStdTpl->addElement($arrData['name'], $arrData['html']);
					}
				}
				
				$this->addButton("SAVE",$objPage->cmstext('btnsave'),"SAVE");
				
				$this->addButton("DELETE",$objPage->cmstext('btndelete'),"DELETE","","tuksi.pagegenerator.deleteNodeDialog(); return false;");
				
				$this->addButton("GOTOTREE",$objPage->cmstext('btngototree'),"SAVE","","window.location='?treeid=".$this->currentNodeId."&tabid=".$this->currentTabId."'");
				
				$strHeadline = $objPage->cmsText('headline_edit');
				
				$objStdTpl->addHeadline($strHeadline . "<a name=\"top\"></a>");
				
				$arrTabs = array();
				
				$sqlTabs = "SELECT * FROM cmstreetab WHERE cmstreeid = '{$this->currentNodeId}' ORDER BY seq";
				$arrRsTabs = $objDB->fetch($sqlTabs);
				if($arrRsTabs['num_rows'] > 0) {
					foreach($arrRsTabs['data'] as $arrTab) {
						if(!$this->currentTabId) {
							$this->currentTabId = $arrTab['id'];
							$this->arrTab = $arrTab;
						} elseif($this->currentTabId == $arrTab['id']){
							$this->arrTab = $arrTab;
						}
						$arrTabs[] = array(	'name' => $arrTab['name'],
																'value' => $arrTab['id']);
					} 
				}
				//get tabs
				$objStdTpl->addSelectElement("Tab",array(	"name" 			=> "tabs_".$this->objMod->id, 
																									"options" 	=> $arrTabs,
																									"width" 	=> 500,
																									"selected"	=> $this->currentTabId,
																									"onchange"	=> "document.getElementById('changeTab_{$this->objMod->id}').value = this.options[this.options.selectedIndex].value;doAction('".$this->getActionName("UPDATE")."');"));
				
				
				
				$objField = new stdClass;																					
				$objField->htmltagname = 'tabname_'.$this->objMod->id;
				$objField->value = $this->arrTab['name'];														
				$objField->readonly = false;														
				$objField->id = $this->currentTabId;														
				$objField->row = $this->currentTabId ;														
				$objFieldSuggest = new fieldTextSuggest($objField);

				$arrHtml = $objFieldSuggest->getHTML();			
								
				$objStdTpl->addElement($objPage->cmstext('tabname'),$arrHtml['html']);
				
				$objStdTpl->addInputElement($objPage->cmstext('seq'),array(	'id' => 'tabseq_' . $this->objMod->id,
																																		'value' => $this->arrTab['seq']));

				$objStdTpl->addInputElement($objPage->cmstext('backendhook'),array(	'id' => 'tabbackendhook_' . $this->objMod->id,
				'value' => $this->arrTab['backendhook'], 'help' => $objPage->cmstext('backendhook_help')));
																									
				$objStdTpl->addButtonElement("",array('color' => 'white',
																							'value' => $objPage->cmstext('addtab'),
																							'icon' => 'add',
																							'customaction' => "tuksi.pagegenerator.addTabDialog(); return false;"),
																				array('fullwidth' => true,
																							'align' => 'right'));
																							
				$objStdTpl->addButtonElement("",array('color' => 'white',
																							'value' => $objPage->cmstext('deletetab'),
																							'icon' => 'del',
																							'customaction' => "tuksi.pagegenerator.deleteTabDialog(); return false;"),
																				array('fullwidth' => true,
																							'align' => 'right'));																							
				
				$arrTemplates = array();
				$arrTemplates[] = array('value' => '','name' => 'Vælg template');
				$sqlTemplate = "SELECT * FROM pg_page_template WHERE isactive = 1 AND template_type = 3";
				$rsTemplate = $objDB->fetch($sqlTemplate);
				if($rsTemplate['num_rows'] > 0) {
						foreach ($rsTemplate['data'] as $arrTemplate) {
							$arrTemplates[] = array('value' => $arrTemplate['id'],'name' => $arrTemplate['name']);
						}
				}
				
				if($_POST->getInt('currenttemplateid') != $this->arrTab['cms_page_templateid']) {
					$this->areaid = 0;
				}
				
				
				//get tabs
				$objStdTpl->addSelectElement($objPage->cmstext('template'),array(	"name" 			=> "template_".$this->objMod->id, 
																												"options" 	=> $arrTemplates,
																												"width" 	=> 300,
																												"selected"	=> $this->arrTab['cms_page_templateid'],
																												"onchange"	=> "doAction('".$this->getActionName("SAVE")."');"));
				
				if($this->currentTabId && $this->arrTab['cms_page_templateid']) {
				
					if($objSettings->arrTree['name']) {
						$strHeadline.= " : " . $objSettings->arrTree['name'];
					}
					
					
					$arrOption = array(	"openSingle" => true,
															"openAll" => false);
					
					//if backend load all areas
					if(($arrAreas = $this->getContentAreas()) !== false) {
							
							if(!$this->areaid) {
								$this->areaid = $arrAreas[0]['id'];
								$this->arrArea = $arrAreas[0];
							}
							
							foreach ($arrAreas as $arrTab) {
								$arrAreasSel[] = array('value' => $arrTab['id'],'name' => $arrTab['name']);
							}
							
							$objStdTpl->addSelectElement($objPage->cmstext('area'),array("name" 			=> "areas_".$this->objMod->id, 
																													"options" 	=> $arrAreasSel,
																													"width" 	=> 300,
																													"selected"	=> $this->areaid,
																													"onchange"	=> "document.getElementById('changeArea_{$this->objMod->id}').value = this.options[this.options.selectedIndex].value;doAction('".$this->getActionName("SAVE")."');"));
							
							
							$arrOption = array(	"openSingle" => true,
																	"openAll" => false);
					}
					
															
					$objPageGenElements = new tuksiPageGeneratorElementsHtml($this->currentNodeId,$this->currentTabId,$this->areaid,true,$arrOption);
					
					if($this->userActionIsSet("SAVE") && $objPage->arrPerms["SAVE"]) {
						$objPageGenElements->save();
						$objSettings->save();
						$this->saveData();
					}
					
					$htmlElements = $objPageGenElements->getInsertedElementsHtml();
				
					$objStdTpl->addElement("",$htmlElements,array('fullwidth' => true));
					
					//$html = $objPageGen->getHtml();
					//$objStdTpl->addElement("",$html);
				}
				if($this->currentTabId) {
					//add perm
					$objStdTpl->addHeadline("Permissions");
					$objStdTpl->addElement("", $this->getPermHTML(),array('fullwidth' => true));
				}
			}
		}
		
		//add tab dialog html
		$tplDialog = new tuksiSmarty();
		$tplDialog->assign("templates",$arrTemplates);
		$tplDialog->assign("nodes",$arrNodes);
		$tplDialog->assign("currentnode",$this->currentNodeId);
		$htmlDialog = $tplDialog->fetch('modules/backend/mBackendTreeDialogs.tpl');
		$objStdTpl->addHtml($htmlDialog);
		
		$this->addButton("ADD",$objPage->cmstext('btnadd'),"ADD","","tuksi.pagegenerator.addNodeDialog(); return false;");	
		
		$objStdTpl->addHiddenField(array(	"NAME" 	=> "changeNode_".$this->objMod->id, 
																			"VALUE" => ""));
		$objStdTpl->addHiddenField(array(	"NAME" 	=> "changeTab_".$this->objMod->id, 
																			"VALUE" => ""));
		$objStdTpl->addHiddenField(array(	"NAME" 	=> "changeArea_".$this->objMod->id, 
																			"VALUE" => ""));																			
		$objStdTpl->addHiddenField(array("NAME" => "currenttreeid", "VALUE" => $this->currentNodeId));
		$objStdTpl->addHiddenField(array("NAME" => "currenttabid", "VALUE" => $this->currentTabId));
		$objStdTpl->addHiddenField(array("NAME" => "currentareaid", "VALUE" => $this->areaid));
		$objStdTpl->addHiddenField(array("NAME" => "currenttemplateid", "VALUE" => $this->arrTab['cms_page_templateid']));
		
		return $objStdTpl->fetch();		
	}
	
	function getPermHTML(){
		
		$tplPerm = new tuksiSmarty();
		
		//get extra perm types
		$arrExtraPerms = tuksiPerm::getExtraPermTypes();
		
		$tplPerm->assign("extraperm",$arrExtraPerms);
		
		//get all perms
		$arrGroupPerms = tuksiPerm::getTreeTabAllGroupsPerms($this->currentNodeId,$this->currentTabId);
		
		$tplPerm->assign("group",$arrGroupPerms);
		
		return $tplPerm->fetch("backend/elementPerms.tpl");
	}
	
	
	function saveData() {
		
		tuksiPerm::prepareTreePerm(	$this->currentNodeId,$this->currentTabId );
		
		$arrGroups = tuksiPerm::getTreeTabAllGroupsPerms( $this->currentNodeId,$this->currentTabId );
		
		foreach ($arrGroups as &$arrGroup) {
			
			$arrPerm = array();
			
			$arrPerm['add'] = $_POST->getStr('add_'.$arrGroup['id']) ? 1 : 0;
			$arrPerm['read'] = $_POST->getStr('read_'.$arrGroup['id']) ? 1 : 0;
			$arrPerm['save'] = $_POST->getStr('save_'.$arrGroup['id']) ? 1 : 0;
			$arrPerm['delete'] = $_POST->getStr('delete_'.$arrGroup['id']) ? 1 : 0;
			$arrPerm['admin'] = $_POST->getStr('admin_'.$arrGroup['id']) ? 1 : 0;
			
			tuksiPerm::addTreeTabPerm($this->currentNodeId, $this->currentTabId, $arrGroup['id'], $arrPerm);
			
			if ($_POST->getStr('newpermid_' . $arrGroup['id'])) {
				tuksiPerm::setTreeTabExtraPerm($this->currentNodeId,$this->currentTabId,$arrGroup['id'],$_POST->getStr('newpermid_' . $arrGroup['id']));
			}
			
			// Deleting extra perm
			$arrDelExtraPerm = $_POST->getArray("extraperm_" . $arrGroup['id']);
			
			if(is_array($arrDelExtraPerm)) {
				foreach ($arrDelExtraPerm as $extrapermid) {
					tuksiPerm::deleteExtraPerm($this->currentNodeId,$this->currentTabId, $arrGroup['id'], $extrapermid);
				}
			}
		}
		tuksiPerm::cleanupTreePerm( $this->currentNodeId,$this->currentTabId );
		
		if($_POST->getInt('copyperm')) {
			tuksiPerm::copyPermToChildren($this->currentNodeId,$this->currentTabId,$_POST->getInt('copyperm'));
		}
		if($this->currentTabId) {
			
			$objDB = tuksiDB::getInstance();
			
			$templateId = $_POST->getInt("template_".$this->objMod->id);
			$name = $_POST->getStr("tabname_".$this->objMod->id);
			
			$objField->htmltagname = 'tabname_'.$this->objMod->id;
			$objField->value = $name;														
			$objField->readonly = false;														
			$objField->id = $this->currentTabId;														
			$objField->row = $this->currentTabId ;														
			$objFieldSuggest = new fieldTextSuggest($objField);
			$objFieldSuggest->saveData();
			
			$seq = $_POST->getStr("tabseq_".$this->objMod->id);
			$backendhook = $_POST->getStr("tabbackendhook_".$this->objMod->id);
			$sqlUpd = "UPDATE cmstreetab ";
			$sqlUpd.= "SET cms_page_templateid = '{$templateId}', ";
			$sqlUpd.= "seq = '".$objDB->realEscapeString($seq)."', ";
			$sqlUpd.= "name = '".$objDB->realEscapeString($name)."', ";
			$sqlUpd.= "backendhook = '".$objDB->realEscapeString($backendhook)."' ";
			$sqlUpd.= "WHERE id = {$this->currentTabId}";
			$r = $objDB->write($sqlUpd);
		}
	}
	
	/**
	 * Henter nodestruktur og lave select options.
	 *
	 * @param int $treeid
	 * @param string $curpage
	 * @param int $selectedid
	 * @return array
	 */
	function getNodes($treeid, $curpage = "",$level = 0,$arrUsed = array()) {
		
		$objDB = tuksiDB::getInstance();
		$objText = tuksiText::getInstance('table');
		$arrNodes = array();
		
		$arrConf = tuksiConf::getConf();
		
		$level++;
		
		$sqlNode = "SELECT t.parentid, t.id, t.name, t.cmssitelangid,  ";
		$sqlNode.= "txt.value_{$arrConf['setup']['admin_lang']} AS namelang ";
		$sqlNode.= ", (SELECT count(*) FROM cmstree tt WHERE tt.parentid = t.id AND tt.isdeleted = 0) as haschild ";
		$sqlNode.= "FROM cmstree t ";
		$sqlNode.= "LEFT JOIN cmstext txt ON (t.name = txt.token) ";
		$sqlNode.= "WHERE t.parentid = '$treeid' AND isdeleted = 0 order by t.seq, t.name";

		$arrRsNode = $objDB->fetch($sqlNode);
		
		if($arrRsNode['ok'] && $arrRsNode['num_rows'] > 0) {
			foreach ($arrRsNode['data'] as $arrNode) {
				if(!$arrUsed[$arrNode['id']]) {
					$arrUsed[$arrNode['id']] = true;
					
					if (!$arrNode['cmssitelangid'] && $arrNode['namelang']) {
						$arrNode['name'] = $arrNode['namelang'];
					}
					
					$name = $curpage != "/" ? $curpage . "/" . $arrNode['name'] : $arrNode['name'];
					$sel = $this->currentNodeId == $arrNode['id'] ? true : false;

					$arrNodes[] = array('name' => $name,'value' => $arrNode['id'],'sel' => $sel);
					if ($arrNode['haschild']) {
						$arrChildNodes = $this->getNodes($arrNode['id'], $name, $level, $arrUsed);
						$arrNodes = array_merge($arrNodes,$arrChildNodes);
					}
				}
			}
		} 
		return $arrNodes;
	}
	private function getContentAreas(){
		
		$objDB = tuksiDB::getInstance();
		
		$sqlArea = "SELECT c.*, t.id AS tabid FROM pg_contentarea c, cmstreetab t ";
		$sqlArea.= "WHERE c.pg_page_templateid = t.cms_page_templateid AND t.id = '{$this->currentTabId}' AND c.pg_page_templateid > 0 ";
		
		$arrRsArea = $objDB->fetch($sqlArea);
		
		if($arrRsArea['ok'] && $arrRsArea['num_rows'] > 0) {
			return $arrRsArea['data'];
		} else {
			return false;
		}
	}
}
?>
