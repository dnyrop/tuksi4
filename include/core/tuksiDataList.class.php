<?php
/**
 * Enter description here...
 *
 * @package tuksiBackend
 */
class tuksiDataList {
	
	private $objPaging,$customUrl;
	private $idFieldName,$sql,$tablename,$tablelayoutid;
	
	private $arrError = array();
	
	public function __construct($tablename,$sql = "",$tablelayoutid = 0,$idFieldName = '') {
		
		$this->tablename = $tablename;
		if(!$this->tablename)
			$this->arrError[] = "No tablename set";
		
		$this->sql = $sql;
			
		if(!$idFieldName)
			$this->idFieldName = 'id';
		else
			$this->idFieldName = $idFieldName;
		
		$this->tablelayoutid = $tablelayoutid;
			
		if(!$this->tablelayoutid) {
			if($this->setDefaultTablelayout() === false) {
				$this->arrError[] = 'No tablelayout found';
				return false;
			}
		}
		
	}	
		
	function getErrors() {
		return $this->arrError;
	}
	
	
	public function setCustomUrl($url){
		$this->customUrl = $url;
	}
	
	public function getNavigation(){
		if ($this->objPaging instanceof tuksiPaging) {
			return $this->objPaging->getNavigation();
		}
	}
	
	public function getPages($page){
		if ($this->objPaging instanceof tuksiPaging) {
			return $this->objPaging->getPages($page);
		}
	}
	
	public function getCurrentPage(){
		if ($this->objPaging instanceof tuksiPaging) {
			return $this->objPaging->getCurrentPage();
		}
	}
	
	public function getRows($pagesize = 0,$page = 0) {
		
		$arrReturn = array();
		
		$objPage = tuksiBackend::getInstance();
		$objDB = tuksiDB::getInstance();
		$arrUser = tuksiBackendUser::getUserInfo();
		
		$objFieldItem = new tuksiFielditemTable($this->tablename, $this->tablelayoutid);
	
		list($arrFields, $arrSqlFields) = $objFieldItem->getFieldsToShow($arrUser['id']);

		if(count($arrFields) == 0) {
			return false;
		}
		
		if(!in_array($this->idFieldName,$arrFields)) {
			$arrFields[] = $this->idFieldName;
		}
		
		if (preg_match('/([0-9,a-z,A-Z$_]+\.)?\*/', $this->sql, $matches)) {
			$sqlFields = str_replace("*", $matches[1], "*" . join(', *', $arrFields));
			$sqlList = str_replace($matches[0], $sqlFields, $this->sql);
		} else {
			$sqlList = $this->sql;
		}
		
		$sqlList = $objDB->validateSelectSQL($sqlList);
		
		$arrRsList = $objDB->fetch($sqlList);
		
		if ($arrRsList['ok'] && $arrRsList['num_rows'] > 0) {
			
			$this->objPaging = new tuksiPaging($arrRsList['num_rows'],$pagesize,$page);
			
			$arrRecords = $this->objPaging->getRecords();			
			$nbRecords = $this->objPaging->getNbRecords();
			
			$arrData = array();
			// Making fieldlist for sqlCols
			$sqlFields = join(" OR " , $arrSqlFields);

			$sqlCols = "SELECT fi.name, fi.colname, ft.id, fi.fieldvalue1, fi.fieldvalue2, fi.fieldvalue3, fi.fieldvalue4, fi.fieldvalue5, ";
			$sqlCols.= "fi.listcolwidth, fi.listcolalign , ft.classname,ft.rowid_relation, ft.plain_value, ";
			$sqlCols.= "ct.value_" .tuksiIni::$arrIni['setup']['admin_lang']." as langname,SUM(fp.psave) as saveperm ";  
			$sqlCols.= "FROM (cmsfielditem fi, cmsfieldtype ft, cmsfieldperm fp, cmsusergroup ug) ";
			$sqlCols.= "LEFT JOIN cmstext ct ON (ct.token = fi.name) ";
			$sqlCols.= "WHERE fp.cmsgroupid = ug.cmsgroupid AND ug.cmsuserid = '{$arrUser['id']}' AND fi.id = fp.cmsfielditemid ";
			$sqlCols.= "AND fi.itemtype = 'table' AND ft.id = fi.cmsfieldtypeid AND fi.tablename = '{$this->tablename}' AND ($sqlFields) AND fi.relationid = '{$this->tablelayoutid}' GROUP BY fi.id ORDER BY fi.seq";

			$rsCols = $objDB->fetch($sqlCols) or print mysql_error();

			foreach ($rsCols['data'] as $arrCol) {
				// check if lang text is set, else use default text from cmstree
				if (!empty($arrCol['langname']))
					$arrCol['name'] = $arrCol['langname'];

				$arrHeaders[] = array('name' => $arrCol['name'],"width" => $arrCol['listcolwidth'] );	
				$arrCols[] = $arrCol;
			}
			$arrReturn['headers'] = $arrHeaders;
			
			for($offset = $arrRecords['start'];$offset <= $arrRecords['stop'];$offset++) {
			
				if($offset == 1 && $nbRecords > 1) {
					//print "first";
				}
				
				
			//foreach($arrRsList['data'] as &$arrRow) {
				$arrRow = $arrRsList['data'][$offset-1];
			
				$arrCol = array();
				
				for ($i = 0; $i < count($arrCols); $i++) {
					
					if($arrCols[$i]['plain_value']) {

						$FieldValue = $arrRow[$arrCols[$i]['colname']];
					
					} else {
						
						if(!isset($arrCols[$i]['rowid_relation']) &&!$arrRow[$arrCols[$i]['colname']] && $arrCache[$arrCols[$i]['colname']][$arrRow[$arrCols[$i]['colname']]]) {
							
							$FieldValue = $arrCache[$arrCols[$i]['colname']][$arrRow[$arrCols[$i]['colname']]];

						} else {
							
							$MyField = new stdClass;
							$MyField->rowid 		= $arrRow['id'];
							$MyField->value 		= $arrRow[$arrCols[$i]['colname']];
							$MyField->cmsrowfieldtypeid = $arrCols[$i]['id'];
							$MyField->fieldvalue1 = $arrCols[$i]['fieldvalue1'];
							$MyField->fieldvalue2 = $arrCols[$i]['fieldvalue2'];
							$MyField->fieldvalue3 =  $arrCols[$i]['fieldvalue3'];
							$MyField->fieldvalue4 =  $arrCols[$i]['fieldvalue4'];
							$MyField->fieldvalue5 = $arrCols[$i]['fieldvalue5'];
							$MyField->tablename		= $this->tablename;
							$MyField->rowData = get_object_vars($objDB->fetchRow($this->tablename, $arrRow['id'], 'object'));

							$objField = new $arrCols[$i]['classname']($MyField, $objPage);
							$FieldValue	= $objField->getListHtml();

							//gem cache
							if(!isset($arrCols[$i]['rowid_relation'])) {
								 $arrCache[$arrCols[$i]['colname']][$arrRow[$arrCols[$i]['colname']]] = $FieldValue;
							}
						}
					}	
					$arrCol[] = array("align" =>$arrCols[$i]['listcolalign'], 
														"value" => $FieldValue);
				}
				if($this->customUrl) {
					$link = "document.location = '".str_replace("#ROWID#", $arrRow[$this->idFieldName],$this->customUrl) . "';";
				} else {
					$link = "editRow('{$objPage->treeid}','{$objPage->tabid}','{$this->objMod->id}','{$arrRow[$this->idFieldName]}');";
				}
				
				$arrData[] = array(	'col' => $arrCol,
														'rowid' => $arrRow[$this->idFieldName],
														'link' => $link);
			}
			$arrReturn['data'] = $arrData;
		}
		return $arrReturn;
		
	}
	
	public function setRow($rowid) {
		$this->rowid = $rowid;
		$this->objRowEdit = new tuksiTableEdit($this->tablename,$this->rowid,$this->tablelayoutid);
	}
	
	public function saveRow() {
		return $this->objRowEdit->saveData();
	}
	
	public function deleteRow(){
		$t = $this->objRowEdit->deleteData();
		return $t;
	}
	
	public function getRowHtml(){
		return $this->objRowEdit->getHTML();
	}
	
	private function setDefaultTablelayout(){
		
		$objDB = tuksiDB::getInstance();
		
		// if table layout notfilled, lets see if we got one
		$sqlLayout = "SELECT id FROM cmstablelayout WHERE tablename = '{$this->tablename}'";
		$arrRsLayout = $objDB->fetch($sqlLayout);
		if ($arrRsLayout['ok'] && $arrRsLayout['num_rows'] > 0) {
			$this->tablelayoutid = $arrRsLayout['data'][0]['id'];
		} else {
			return false;
		}
	}
	
	public function addRow($sqlNew) {
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		if ($sqlNew) {
	    
			$sql = $objDB->validateInsertSQL($sqlNew);
			
			$arrResult = $objDB->write($sql);
			
      if ($arrResult['ok']) {
      	
					$this->rowid = $arrResult['insert_id'];
          if ($this->sqlNew2) {
              $objPage->debug->log($this->sqlNew2);
              $sql = $objDB->validateInsertSQL($this->sqlNew2);
              $objPage->debug->log($sql);
              $result = $objDB->query($sql) or $objPage->debug("Cant add row:" . $sql . ":". mysql_error());
          }
          return $this->rowid;
      } else {
      	$objDebug = tuksiDebug::getInstance();
				$objDebug->error("Error adding element: $sql <br>[" . mysql_error() . "]");
				return false;
      }
		
		} else {
			$objDebug = tuksiDebug::getInstance();
			$objDebug->error("SQLNEW variable is missing");
			return false;
		}
	}

	function releaseTable($sqlAppend = '') {
		if (tuksiRelease::releaseTable($this->tablename, $this->tablelayoutid, $sqlAppend)) {
			$arrReturn['ok'] = true;
		} else {
			$arrReturn['ok'] = false;
		}
		return $arrReturn;
	}
}
?>
