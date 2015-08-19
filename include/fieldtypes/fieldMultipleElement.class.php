<?

/**
 * Felttype: fieldMultipleElement
 * 
 * @package tuksiFieldType
 */
class fieldMultipleElement extends field {

	private $newRowId = 0;
	static $arrDataFields = array();
	private $arrFieldItems;
	
	function __construct($objField) {			
		parent::field($objField);
		
		$this->getExtraFieldValues();
		
		$this->objText = tuksiText::getInstance('fieldtypes/fieldMultipleElement.tpl');
		
	}

	function LoadTableLayout($tablename) {
		
		$objDB = tuksiDB::getInstance();
		
		//if table layout not filled, lets se if we got one
		if (!empty($this->objField->fieldvalue4))
			$sqlField = "id = '{$this->objField->fieldvalue4}'";
		else
			$sqlField = "tablename = '{$tablename}'";

		$sqlLayout = "SELECT id, tablename FROM cmstablelayout WHERE $sqlField";
		$arrRsLayout = $objDB->fetchItem($sqlLayout, array('expire' => 600));
		if ($arrRsLayout['ok'] && $arrRsLayout['num_rows']) {
			$tablelayoutid = $arrRsLayout['data']['id'];
			$tablename = $arrRsLayout['data']['tablename'];
		}
		return array($tablelayoutid, $tablename);
	}

	function getDataAndFields($sql) {

		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$arrRsRow = $objDB->fetch($sql,array(	'tablename' => true,
																					'num_fields' => true,
																					'fields' => true));

		if ($arrRsRow['ok']) {

			$tablename = $arrRsRow['tablename'];
			list ($tablelayoutid, $tablename) = $this->LoadTableLayout($tablename);

			$arrFields = array();
			$arrFieldsClean = array();

			if ($tablelayoutid) {
				$arrUser = tuksiBackendUser::getUserInfo();
				$objFieldItem = new tuksiFielditemTable($tablename, $tablelayoutid);
				list($arrFields, $arrSqlFields) = $objFieldItem->getFieldsToShow($arrUser['id'], 'read');
				//$objPage->alert(print_r($arrFields,1));
			}
			
			foreach ($arrRsRow['data'] as &$arrData) {
				if (!isset($arrData['row_elem_title'])) {
					$arrData['row_elem_title'] = $arrData['name'];
				} // if
			} // foreach
			
			return array($arrRsRow, $arrSqlFields, $arrField, $tablename, $tablelayoutid); 
		} else {
			// error
			return array($arrRsRow, array(), array(), '', 0);
		}
	}


	function getHTML() {
	 	
		if ($arrReturn = $this->checkFieldvalues())
			return $arrReturn;
			
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		$arrConf = tuksiConf::getConf();
		
		$tpl = new tuksiSmarty();	
		
		$arrRows = array();
		
		$htmlForm = "";
		
		$SQL = $this->rowDataReplace($this->objField->fieldvalue1);
		//$SQL = $objPage->parsevalue($SQL);
		
		//pagegenrator HACK
		$pos = strrpos($SQL, "#ROWID#");
		if (!$pos === false) {
			$SQL = str_replace("#ROWID#",$this->objField->rowid,$SQL);
			$this->objField->id = $this->objField->rowid;
		}
		
		
		if(!empty($this->arrFieldvalues['BUTTONADD'])) {
			$btnaddtext = $this->arrFieldvalues['BUTTONADD'];
		} else {
			$btnaddtext = $objPage->cmstext("new_element");
		}
		if(!empty($this->arrFieldvalues['BUTTONDELETE'])) {
			$btndeletetext = $this->arrFieldvalues['BUTTONDELETE'];
		} else {
			$btndeletetext = $objPage->cmstext("delete_element");
		}
		if(!empty($this->arrFieldvalues['BUTTONEDIT'])) {
			$btnedittext = $this->arrFieldvalues['BUTTONEDIT'];
		} else {
			$btnedittext = $objPage->cmstext("edit_element");
		}
		$tpl->assign("new_element", $btnaddtext);
		$tpl->assign("delete_element", $btndeletetext);
		$tpl->assign("edit_element", $btnedittext);
		
		$tpl->assign("html_start", parent::getHtmlStart());
		$tpl->assign("fieldid", $this->objField->id);
		$tpl->assign("fieldcolname", $this->objField->colname);
		$tpl->assign("htmltagname",  $this->objField->htmltagname);
		
	//	error_log("Getting rows: " . $SQL);
		// Getting row data, and fields to show
		list($rsRow, $arrsqlFields, $arrField, $tablename, $tablelayoutid) = $this->getDataAndFields($SQL);

		if (!$tablelayoutid && $objPage->arrPerms['ADMIN']) {
			$strText = $objPage->cmsText('tablelayoutmissing');
			
			$strText = str_replace("#TABLENAME#","<b>$tablename</b>",$strText);
			
			$strText.= " <a href=\"/{$arrConf['setup']['admin']}/?cmstreeid={$arrConf['link']['tableadmin_treeid']}\">" . $objPage->cmstext('goto_tablelayout_setup') . "</a>"; 	
			
			$ReturnHtml.= $strText ;
			
			return parent::returnHtml($this->objField->name, $ReturnHtml);
		} elseif(!$tablelayoutid) {
			return parent::returnHtml($this->objField->name, "error");
		}
						
		// Making fieldlist for sqlCols
		if (count($arrsqlFields))
			$sqlFields = join(" OR " , $arrsqlFields);

		$sqlCols = "SELECT distinct fi.id,fi.id as fielditemid, fi.name, fi.colname, ft.id as fieldtypeid, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, ";
		$sqlCols.= "fi.listcolwidth, fi.listcolalign , ft.classname,ft.rowid_relation, ft.plain_value,ft.speciel_frontend,ft.special_delete,   ";
		$sqlCols.= "ct.value_" .tuksiIni::$arrIni['setup']['admin_lang']." as langname,SUM(fp.psave) as saveperm ";  
		$sqlCols.= "FROM (cmsfielditem fi, cmsfieldtype ft, cmsfieldperm fp, cmsusergroup ug) ";
		$sqlCols.= "LEFT JOIN cmstext ct ON (ct.token = fi.name) ";
		$sqlCols.= "WHERE fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fi.id = fp.cmsfielditemid ";
		$sqlCols.= "AND fi.itemtype = 'table' AND ft.id = fi.cmsfieldtypeid AND fi.tablename = '{$tablename}' AND ($sqlFields) AND fi.relationid = '{$tablelayoutid}' ";
		$sqlCols.= "GROUP BY fi.id ORDER BY fi.seq";	
					
		$Result = $objDB->fetch($sqlCols,array('type' => 'object')) ;
		
		$arrFieldinfo = array();
		
		if ($Result)
		foreach ($Result['data'] as &$MyField) {
			if (!empty($MyField->langname)) {
				$MyField->name= $MyField->langname;
			}
			$arrFieldinfo[$MyField->colname] = $MyField;
		}
		
		$intCount = 0;
		$intMax = $rsRow['num_rows'];
		
		foreach ($rsRow['data'] as $RowData) {
			
			$intCount++;
			
			$RowData['fieldname_isopen'] = "{$this->objField->htmltagname}_isopen_{$RowData['id']}";
						
			if (!isset($RowData['id'])) {
				$ReturnHtml = "<b>ID i SQL mangler!</b> ";
				return parent::returnHtml($this->objField->name, $ReturnHtml);
			}
			
			$fieldname_isopen = $this->objField->htmltagname . "_isopen_" . $RowData['id'];
			
			if ($_POST->getInt($fieldname_isopen) || $this->newRowId == $RowData['id']){
				$RowData['isopen'] = 1;
				$RowData['isopen_invert'] = 0;
			} else {
				$RowData['isopen'] = 0;
				$RowData['isopen_invert'] = 1;
			}
	
			if ($RowData['isopen']) {
							
				if ($this->objField->fieldvalue5) {
					$ReturnHtml .= "<tr valign=top height=05><td></td><td>Tuksi tag</td><td>[TIMG={$RowData['id']}] Kan indsættes i content felt.</td><tr>"; 
				}
									
				$seqArray = array();	
				if(is_array($rsRow['fields'])) {
					foreach ($rsRow['fields'] as $objCol) {
						$sql = "SELECT seq FROM cmsfielditem WHERE colname='{$objCol->name}' AND relationid =".$tablelayoutid;
						$rs = $objDB->fetchItem($sql);
						$seqArray[$objCol->name] = $rs['data']['seq'];	
						
					}
				}
				asort($seqArray);
				
				// Smarty var
				$arrFieldtypes = array();
				
				foreach ($seqArray as $colname => $seq){ 
				
					if ($arrFieldinfo[$colname]) {
						
						$MyField =  clone $arrFieldinfo[$colname];
						
						if ($this->arrFieldItems[$MyField->id . "-" . $RowData['id']]){
							$objField = $this->arrFieldItems[$MyField->id . "-" . $RowData['id']];
						} else {
							
							$MyField->content 		= $RowData[$colname];
							$MyField->htmltagname	= "TABLE_" . $this->objField->id."_". $RowData['id']."_". $colname;
							$MyField->vcolname		= "TABLE_" . $this->objField->id."_". $RowData['id']."_". $colname;
							$MyField->rowid 			= $RowData['id'];
							$MyField->value				= $RowData[$colname];	
							$MyField->fieldvaluetablename = $tablename;	
							$MyField->tablename = $tablename;
							$MyField->rowData = $RowData;
							
							$objField = new $MyField->classname($MyField, $objPage);
						}
						$arrData = $objField->getHTML();
						
						$arrFieldtypes[] = $arrData;	
						
					}
																		
				} // Foreach fieldtype
				$RowData['fieldtypes'] = $arrFieldtypes;
				
			} // END is open
			
			$arrRows[] = $RowData;
		} // ENd while element
	
		$tpl->assign("row", $arrRows);
			
		$arrPerms = tuksiFielditemBase::getUserPerms($this->objField->fielditemid,$arrUser['id']);
		
		if($arrPerms['padd']){
			$tpl->assign('addperm',true);
		}
		
		if($arrPerms['pdelete']){
			$tpl->assign('deleteperm',true);
		}
		
		if($arrPerms['padmin']) {
			
			
			$baseUrl = tuksiTools::getBackendUrl($arrConf['link']['tableadmin_treeid']);
			if(($pos = strpos($tablename,".")) !== false) {
				$arr = explode(".",$tablename);
				$table = $arr[1];
				$db = $arr[0];
			} else {
				$db = "";
				$table = $tablename;
			}
			
			$baseUrl.= "&db=" . $db;
			$baseUrl.= "&table=" . $table;
			$baseUrl.= "&layout=" . $tablelayoutid;
			
			$tpl->assign('tableadminurl',($baseUrl));
		}
			
		return parent::returnHtml($this->objField->name, $tpl->fetch("fieldtypes/fieldMultipleElement.tpl"),array('fullwidth' => true));
		
	}
	/**
	 * Need to load all values without any permission
	 * Works the same way as both gethtml and savedata
	 * @return unknown
	 */
	
	function getFrontendValue(){
		
		$arrReturn = array();
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();

		$SQL = $this->rowDataReplace($this->objField->fieldvalue1);
		
		$pos = strrpos($SQL, "#ROWID#");
		if (!$pos === false) {
			$SQL = str_replace("#ROWID#",$this->objField->rowid,$SQL);
			$this->objField->id = $this->objField->rowid;
		}

		$arrRsRow = $objDB->fetch($SQL,array(	'expire' => 600, 
																					'tablename' => true,
																					'num_fields' => true,
																					'fields' => true));
				
		if ($arrRsRow['ok'] && $arrRsRow['num_rows']) {
			
			list ($tablelayoutid, $tablename) = $this->LoadTableLayout($arrRsRow['tablename']);
			
			$sql = "SELECT fi.*, ft.classname ";
			$sql.= "FROM cmsfielditem{$arrConf['setup']['tableext']} fi, cmsfieldtype ft  ";
			$sql.= "WHERE fi.itemtype = 'table' AND fi.cmsfieldtypeid = ft.id AND fi.relationid = '{$tablelayoutid}' ";
			$sql.= "AND ft.speciel_frontend = 1 ";
			
			$rsField = $objDB->fetch($sql,array('type' => 'object', 'expire' => 600));
			if($rsField['ok'] && $rsField['num_rows'] > 0) {
				
				$arrSpecial = array();
				
				foreach ($rsField['data'] as $myField){
					$arrSpecial[$myField->colname] = $myField;
				}
				
				foreach($arrRsRow['data'] as &$RowData){
				
					foreach ($RowData as $colname => $value) {
						
						if (!empty($arrSpecial[$colname])){
						
							$MyField = $arrSpecial[$colname];
							
							$MyField->content 		= $RowData[$colname];
							$MyField->rowid 			= $RowData['id'];
							$MyField->value				= $RowData[$colname];	
							$MyField->fieldvaluetablename = $arrRsRow['tablename'];	
							$MyField->tablename = $arrRsRow['tablename'];
							$MyField->rowData = $RowData;
							
							$objField = new $MyField->classname($MyField);
							$RowData[$colname] = $objField->getFrontendValue();
						}
					}
					$arrReturn[] = $RowData;
				}
			}
		}
		return $arrReturn;
		
	}
	

	function saveData($actionField = 'save') {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		$SQL = $this->rowDataReplace($this->objField->fieldvalue1);

		// pagegenerator HACK
		$pos = strrpos($SQL, "#ROWID#");
		if (!$pos === false) {
			$SQL = str_replace("#ROWID#",$this->objField->rowid,$SQL);
			$this->objField->id = $this->objField->rowid;
		} // End pagegenerator HACK
		
		// Getting row data, and fields to show
		
		if ($_POST->getInt("TABLE_" . $this->objField->colname . "_" . $this->objField->id . "_newrow") && $this->objField->fieldvalue2) {

			$SQL = $this->rowDataReplace($this->objField->fieldvalue2);
			//$objPage->addparsevalue("SEQ", $seq_next);
			$pos = strrpos($SQL, "#ROWID#");
			if (!$pos === false) {
				$SQL = str_replace("#ROWID#",$this->objField->rowid,$SQL);
				$this->objField->id = $this->objField->rowid;
			}
			
			$arrRs = $objDB->write($SQL);
			if($arrRs['ok']) {
				$this->newRowId = $arrRs['insert_id'];
			} else {	
				$objPage->alert($arrRs['error']);
			}
		}
		
		
		list($rsRow, $arrsqlFields, $arrField, $tablename, $tablelayoutid) = $this->getDataAndFields($SQL);
		
		if ($tablelayoutid && count($arrsqlFields)) {
		
			// Making fieldlist for sqlCols
			$sqlFields = join(" OR " , $arrsqlFields);
			//	print_r($sqlFields);
				
			$sqlCols = "SELECT fi.name, fi.colname, fi.id, ft.id as fieldtypeid,fi.id as fielditemid, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, ";
			$sqlCols.= "fi.listcolwidth, fi.listcolalign , ft.classname,ft.rowid_relation, ft.plain_value,ft.special_delete, ft.special_copy, ";
			$sqlCols.= "ct.value_" .tuksiIni::$arrIni['setup']['admin_lang']." as langname,SUM(fp.psave) as saveperm ";  
			$sqlCols.= "FROM (cmsfielditem fi, cmsfieldtype ft, cmsfieldperm fp, cmsusergroup ug) ";
			$sqlCols.= "LEFT JOIN cmstext ct ON (ct.token = fi.name) ";
			$sqlCols.= "WHERE fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fi.id = fp.cmsfielditemid ";
			$sqlCols.= "AND fi.itemtype = 'table' AND ft.id = fi.cmsfieldtypeid AND fi.tablename = '{$tablename}' AND ($sqlFields) AND fi.relationid = '{$tablelayoutid}' ";
			$sqlCols.= "GROUP BY fi.id ORDER BY fi.seq";	

			$Result = $objDB->fetch($sqlCols,array('type' => 'object')) or print mysql_error();
			$arrFieldinfo = array();
			foreach ($Result['data'] as $MyField) {
				if (!empty($MyField->langname)) {
					$MyField->name= $MyField->langname;
				}
				$arrFieldinfo[$MyField->colname]=$MyField;
			}
			
			$intSeq = 100;
			
			if ($actionField == 'copy') {
					
				$copyName = $this->arrFieldvalues['COL_NAME'];
				
				if ($copyName) {
				
	   				$arrFields = $objDB->getArrColumns($tablename);
	                
	    			// Sætter array med feltnavne. Id skal IKKE med
					$arrCopyFields = array();
	    			if (count($arrFields)) {
						foreach ($arrFields as $arrField) {
							if ($arrField['Field'] != 'id' && $arrField['Field'] != $copyName) {
								$arrCopyFields[$arrField['Field']] = $arrField['Field'];
							}
						}
    				}
				} else {
					$actionField = '';
				}
			}
			
			foreach ($rsRow['data'] as $RowData) {
				
				$action = 'save';
				
				$formname_delete 	= "TABLE_" . $this->objField->id . "_". $RowData['id'] . "_deleterow";
				$formname_isopen	=  $this->objField->htmltagname . "_isopen_" . $RowData['id'];
				$formname_isopenold	= $this->objField->htmltagname . "_isopen_" . $RowData['id'] . "_old";
				$formname_moveup		= "TABLE_" . $this->objField->id."_". $RowData['id'] . "_moveup";
				$formname_movedown		= "TABLE_" . $this->objField->id."_". $RowData['id'] . "_movedown";
							
				if ($actionField == 'copy') {
	
					$copyName = $this->arrFieldvalues['COL_NAME'];
					
					$sqlData = "SELECT * FROM $tablename WHERE id = {$RowData['id']}";
					$arrReturnRow = $objDB->fetchItem($sqlData);
					
					$copyCols = array();
					$dataCols = array();
					
					foreach($arrCopyFields as $col) {
						$copyCols[] = $col;
						$dataCols[] = $arrReturnRow['data'][$col];
					}
				
					$sqlInsert = "INSERT INTO $tablename (" . join(", ", $copyCols) . ",$copyName) VALUES ('" . join("', '", $dataCols) . "','{$this->copyToId}') ";
					
					$rsInsert = $objDB->write($sqlInsert);
					
					$newFieldId = mysql_insert_id();
					
					$action = 'copy';
					
				}
				
				if ($_POST->getStr($formname_delete) || $actionField == 'delete') {			
					$action = 'delete';				
				}
					 
				if (isset($RowData['seq'])) {
				
					$seq = $intSeq;
					if ($_POST->getInt($formname_moveup)) 
						$seq -= 150;
					if ($_POST->getInt($formname_movedown)) 
						$seq += 150;
			
					$sql = "UPDATE $tablename SET seq = '{$seq}' WHERE id = '{$RowData['id']}'";
					$objDB->write($sql);
					
					$seq_next = $seq + 100;
				}
				
				if ($_POST->getStr($formname_isopenold) || $action == 'delete'  || $action == 'copy') {
			 
					foreach ($rsRow['fields'] as $colname) {
						$sqlPart = '';
						
						if ($arrFieldinfo[$colname->name]) {
							
							$MyField = clone $arrFieldinfo[$colname->name];
							
							$MyField->content     = $RowData[$colname->name];
							$formname = 'TABLE_' . $this->objField->id."_". $RowData['id']."_". $colname->name;
							$MyField->value 	  = $_POST->getStr($formname); 
							$MyField->htmltagname = $formname; 
							$MyField->vcolname    = "TABLE_" . $this->objField->id."_". $RowData['id']."_". $colname->name;
							$MyField->rowid       = $RowData['id'];
							$MyField->fieldvaluetablename = $tablename;	
							$MyField->tablename = $tablename;	
							$MyField->rowData = $RowData;
							
							$this->arrFieldItems[$MyField->id . "-" .$MyField->rowid] = new $MyField->classname($MyField, $objPage);
							$objField = &$this->arrFieldItems[$MyField->id . "-" .$MyField->rowid];
							
							if ($action == 'save') 
								$sqlPart  = $objField->saveData();
								
							if ($action == 'delete' && $MyField->special_delete) {
								$objField->deleteData(); 
								$sqlPart  =  $colname->name . " = ''"; 
							}
							if ($action == 'copy' && $MyField->special_copy) {
								$MyField->value 	  = $RowData[$colname->name]; 
								//$objPage->alert('copying field : '.$colname->name .' '. $MyField->classname);
								$objField->copyData($newFieldId);
							}
								
							if ($sqlPart) {
								
								$sqlSave	= "UPDATE $tablename SET $sqlPart WHERE id = '{$RowData['id']}'";
								$rs = $objDB->write($sqlSave) or $objPage->debug("Error saving:" . mysql_error());
							
							}
						} // END for each field in table
					} // END For update if open
					
					if ($action == 'delete') {
						$this->deleteRow($RowData['id']);
					}
				} // END Update IF
			
				$intSeq += 100;
			} // END While
			
			if ($_POST->getInt("TABLE_" . $this->objField->colname . "_" . $this->objField->id . "_newrow") && $this->objField->fieldvalue2 && $newRowId) {
				$sqlUpd = "UPDATE ".$tablename." SET seq = '$seq_next' WHERE id = '$newRowId'";	
				$objDB->write($sqlUpd);
			} 
			
			// include("tables_onsave.lib");
		}
		return $ReturnSQL;
	}
	
	function microtime_float()
	{
   	list($usec, $sec) = explode(" ", microtime());
   	return ((float)$usec + (float)$sec);
	}
	
	function deleteRow($rowid) {
		
		$objDB = tuksiDB::getInstance();
		
		$sql = $this->objField->fieldvalue3;
		$sql = str_replace("#ROWID#", $rowid, $sql);
		
		$objDB->write($sql);
	}
	
	function deleteData() {
		// Skal ikke slette data, hvis NORELATION er sat. Dette betyder at data er global brugt, men kan rettes her.
		if (!$this->arrFieldvalues['NORELATION']) {
			$this->saveData('delete');
		}
	}
	
	function releaseData() {
		if ($this->arrFieldvalues['NORELEASE']) return;

		$objPage = tuksiBackend::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$SQL = $this->rowDataReplace($this->objField->fieldvalue1);

		if(empty($SQL))
			return false;
		
		if(!$this->objField->rowid)
			return false;
		
		$pos = strrpos($SQL, "#ROWID#");
		if (!$pos === false) {
			$SQL = str_replace("#ROWID#",$this->objField->rowid,$SQL);
			$this->objField->id = $this->objField->rowid;
		} 
		
		$arrWhere = preg_split('/where/i', $SQL);
		if (count($arrWhere)) {
			$sqlAppend = " WHERE " . array_pop($arrWhere);
		}
		
		list($rsRow, $arrsqlFields, $arrField, $tablename, $tablelayoutid) = $this->getDataAndFields($SQL);
		
		if(tuksiRelease::releaseTable($tablename,$tablelayoutid,$sqlAppend)) {
			tuksiLog::add(7,0,$tablename,$SQL);
		}else {
			//handle errors
			$objPage->addDebug("Error releasing $tablename in multipleElement");
			$objPage->alert($objPage->cmstext("releaseerror"));	
		}
	}
	
	function copyData($toid) {
		
		if (!$this->arrFieldvalues['NORELATION']) {
			$this->copyToId = $toid;
			$this->saveData('copy');
		}	
		
	}
	
	function getlisthtml() {
		return "";
	}
} // End klasse fieldMultipleElement
?>
