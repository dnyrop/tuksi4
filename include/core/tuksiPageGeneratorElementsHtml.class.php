<?php

/**
 * class for handling elements in pagegenerator
 *
 * @package tuksiBackend
 */

class tuksiPageGeneratorElementsHtml extends tuksiPageGeneratorElements {
	
	public $newModuleID;
	
	public function __construct($treeid,$tabid,$areaid,$isBackend = false,$arrOptions = array()) {
		parent::__construct($treeid,$tabid,$areaid,$isBackend,$arrOptions);
	}
	
	/**
	 * get inserted elements for the current area 
	 */
	
	public function getInsertedElementsHtml() {

		$tpl = new tuksiSmarty();

		if($this->loadTemplate()) {

			// Used when module is loaded via ajax and we only need one module to be shown
			if ($this->arrOptions['moduleid']) {
				$tpl->assign('singelmodule', true);
			}
			
			if ($this->arrOptions['editmodule']) {
				$tpl->assign('editmodule', true);
			}

			$arrAllowedElements = $this->getAllowedElements();			
			
			$tpl->assign('allowedmodules',$arrAllowedElements);
			
			//set muliple open/close
			$arrOpenSelected = array();
			$arrCloseSelected = array();

			$arrOpen = $_POST->getArray("module_isopen");
			
			foreach ($arrOpen as &$moduleid) {
				if($moduleid > 0) {
					$arrOpenSelected[] = $moduleid;
				}
			}
			
			if($_POST->getStr('openselected')) {
				$arrSelected = $_POST->getArray('module_selected');
				if(is_array($arrSelected)) {
					$arrOpenSelected = $arrSelected;
				} 
			} else if($_POST->getStr('closeselected')) {
				$arrSelected = $_POST->getArray('module_selected');
				if(is_array($arrSelected)) {
					$arrCloseSelected = $arrSelected;
				}
			}
			
			$opento = $_POST->getInt('opento');
			
			if($opento) {
				$tpl->assign('opento',$opento);
			}
			
			if($this->newModuleID) {
				$arrOpenSelected = array();
				$arrOpenSelected[] = $this->newModuleID;
				$opento = $this->newModuleID;
			}
			
			$tpl->assign('opento',$opento);
			
			$arrModules = $this->getInsertedElements($arrOpenSelected,$arrCloseSelected);
			
			$tpl->assign("modules",$arrModules);
			
			$tpl->assign('moveable',$this->moveable);
			
			$tpl->assign('areaid',$this->areaid);
			
			//get current tree
			return $tpl->fetch("tuksiPageGeneratorElementsHtml.tpl");
		} else {
			return print_r($this->arrError,1);
		}
	}
	
	public function save(){
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		
		$this->loadTemplate();
		
		$insertNewModule = false;
		$arrDeleteModules = array();
		
		$errorOnDelete = false;
		
		//add new module to area 
		if ($_POST->getInt('newmodule')) {
			
			$arrPlacement = array('last' => true);
			
			if($_POST->getStr('newmoduleplacement')) {
				if($_POST->getStr('newmoduleplacement') == 'first') {
					$arrPlacement = array('first' => true);
				} 
			}
			$this->newModuleID = $this->addElement($_POST->getInt('newmodule'),$arrPlacement);
		
		}
		
		
		$arrNewSeq = array();
		if($_POST->getStr('sortorder')) {
			$arrSeq = tuksiTools::jarray($_POST->getStr('sortorder'));
			$seq = 100;
			$arrNewSeq = array();
			foreach ($arrSeq as $moduleid) {
				$arrNewSeq[$moduleid] = $seq;
				$seq+= 100;
			}
		}
		
		if($_POST->getStr('deletemodule')) {
			$arrDeleteModules = explode(",",$_POST->getStr('deletemodule'));
		}
			
		
		// * ---------------------------------------------------------------------------------- *
		// Henter de tilknyttede moduler for det aktuelle area
		// * ---------------------------------------------------------------------------------- *
		$intSeq = 100;//counter for the sequens of modules
				
		if(($arrMods = $this->getElementsFromArea()) !== false) {
			
			foreach($arrMods as &$objMod) {
	
				//sætter formnavne for det aktuelle modul med areaid og moduleid (slet,åben,op og ned)
			  $formname_delete     = "TABLE_" . $objMod->id . "_deleterow";
				$formname_isopenold  = "module_isopen_" . $objMod->id . "_old";
				$formname_up			= "TABLE_" . $objMod->id . "_op";
				$formname_down			= "TABLE_" . $objMod->id . "_ned";
				$formname_moverow_to = "TABLE_" . $objMod->id . "_moverow_to";

				// Skal det aktuelle modul slettes
				// Hvis dette er tilfældet slettes modulet og der forsættes til næste modul
				if (is_array($arrDeleteModules) && in_array($objMod->id,$arrDeleteModules) && $objMod->not_delete) {
					$errorOnDelete = true;
				} elseif (is_array($arrDeleteModules) && in_array($objMod->id,$arrDeleteModules)) {
					
					// Henter alle fieldtypes
					if(($arrFieldItems = $this->getFieldsInModule($objMod->pg_moduleid)) !== false) {
					
						//traveser de forskellige felttyper for det pågældende modul
						foreach($arrFieldItems as &$objFieldItem) {
							
							if ($objFieldItem->special_delete) {
						    //sætter de nødvendige variable for klassen
								
								$objFieldItem->htmltagname  = "module_". $objMod->id . "_" . $objFieldItem->id;
		
								// Når der slettes tages værdien skal databasen, og IKKE fra _POST (Da POST værdien aldrig vil blive gemt)
								
								$objFieldItem->value		= $objMod->{$objFieldItem->colname};
		
								$objFieldItem->vcolname 	= $objFieldItem->htmltagname;							
								$objFieldItem->rowid 		=$objMod->id; // pg_contentid
										
								$objFieldItem->rowData = $objMod;
								
								//opretter og gemmer værdierne i feltet
								$objNewField = new $objFieldItem->classname($objFieldItem);
								$sqlPart  = $objNewField->deleteData();
							}
						}
					}
					$this->deleteElementById($objMod->id);
				
				} else {
					
					if ($_POST->getInt($formname_isopenold)){ 
						
						// * ---------------------------------------------------- *
						// Getting fieldtypes for current module
						// * ---------------------------------------------------- *
						if(($arrFieldItems = $this->getFieldsInModule($objMod->pg_moduleid)) !== false) {
							
							//traveser de forskellige felttyper for det pågældende modul
							foreach($arrFieldItems as &$objField) {
							
								//er der indholdt i objektet
								if ($objField->colname) {
									
							     //sætter de nødvendige variable for klassen
									$objField->htmltagname  = "module_{$objMod->id}_{$objField->id}";
									$objField->value		= $_POST->getStr($objField->htmltagname);
									
									$objField->vcolname 	= $objField->htmltagname;								
									$objField->rowid 		= $objMod->id; // pg_contentid
									
									$objField->rowData = $objMod;
									
									//opretter og gemmer værdierne i feltet
									$objField = new $objField->classname($objField);
									$sqlPart  = $objField->saveData();
									
									//Er der foretaget ændringer så opdater modulet med den nye værdi
									if ($sqlPart) {
										$this->updateElementSQL($objMod->id,$sqlPart);
									}
								} // END if fieldname
							
							} // END While each fieldtype in module
				  	}
						
						// Flytning af modul
						
						list($tmp_treeid, $tmp_areaid) = explode(":", $_POST->getStr($formname_moverow_to));
						
						$safe_treeid = tuksiValidate::secField($tmp_treeid, INT);
						$safe_areaid = tuksiValidate::secField($tmp_areaid, INT);
						
						$objDebug = tuksiDebug::getInstance();
						$objDebug->log("Flyt til" , $formname_moverow_to . "->" . $safe_treeid . "/" . $safe_areaid);
						
						if ($safe_treeid && $safe_areaid) {
												
							$sqlMoveTo = "UPDATE pg_content ";
							$sqlMoveTo.= "SET cmstreeid = '{$safe_treeid}', pg_contentareaid = '{$safe_areaid}' ";
							$sqlMoveTo.= "WHERE id = '{$objMod->id}'";
							
							$objDB->write($sqlMoveTo);
						}
					}//end if open
					
					
					if($objMod->id == $_POST->getInt('activatemodule')) {
							$arrRows = array('isactive' => 1);
							$arrRS = $objDB->update("pg_content",$arrRows,array(),"id = '{$objMod->id}'");
					}
					
					if($objMod->id == $_POST->getInt('deactivatemodule')) {
							$arrRows = array('isactive' => 0);
							$arrRS = $objDB->update("pg_content",$arrRows,array(),"id = '{$objMod->id}'");
					} 
					
					if(($objMod->id == $_POST->getInt('releasemodule')) && $objPage->arrPerms['RELEASEPAGE']) {
						$objPageGen = tuksiPageGenerator::getInstance();
						$objPageGen->releaseModule($objMod->id);
						$objPage->status($objPage->cmstext('module_released'));
					}
					
					
					// * ---------------------------------------------------- *
					// opdaterer rækkefølgen for modulet
					// * ---------------------------------------------------- *
						
					$seq = $intSeq;
					
					if(count($arrNewSeq) > 0 ) {
						if(!$arrNewSeq[$objMod->id]){
							$seq = $intSeq;
						} else {
							$seq = $arrNewSeq[$objMod->id];
						}
					} else {
						//skal modulet rykkes op trækkes 150 fra
						if ($_POST->getInt($formname_up)) 
							$seq -= 150;
					   //skal modulet rykkes ned lægges der 150 til
						if ($_POST->getInt($formname_down)) 
							$seq += 150;
					}
					if($objMod->seq != $seq) {
						//opdater modulet med den nye rækkefølge
						$sql = "UPDATE pg_content SET seq = '{$seq}' WHERE id = '{$objMod->id}'";
						$objDB->write($sql, array('name' => 'Update Seq on PG save'));
					}
				
					$intSeq+= 100;
				}//end else delete
			}//end while module 
			
			if($errorOnDelete) {
				$objPage->alert($objPage->cmsText('couldnotdeletemodule'));
			}
		}
	
		$sqlChanged = "UPDATE cmstree SET datechanged = now() ";
		$sqlChanged.= "WHERE id = '".$this->treeid."'";
		$objDB->write($sqlChanged);
		
		$objSearch = new tuksiPageGeneratorSearch();
		$objSearch->saveTreeData($this->treeid);
	}
}
?>
