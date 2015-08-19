<?

class tuksiTableview {

	var $tablename;
	var $rowid;
	var $objFields;
	var $objData;


	function __construct($rowid, $tablename,$htmltagname="") {

		$objPage = tuksiBackend::getInstance();
		
		$this->tablename = $tablename;
		$this->rowid = $rowid;
		$this->htmltagname = $htmltagname;
		$this->objFields = array();
		$this->objData = new stdClass;
	}

	function setfield($fieldtype, $name, $colname, $fieldvalue1 = "", $fieldvalue2 = "", $fieldvalue3 = "", $fieldvalue4 = "", $fieldvalue5 = "",$arrExtraFields = array()) {
		$objDB = tuksiDB::getInstance();
		
		$this->objFields[$colname] = new stdClass;
		$this->objFields[$colname]->fieldtype	=	$fieldtype; 
		$this->objFields[$colname]->name = $name;
		$this->objFields[$colname]->rowid	= $this->rowid;
		$this->objFields[$colname]->id = $this->rowid;
		$this->objFields[$colname]->tablename	= $this->tablename;
		$this->objFields[$colname]->tablename	= $this->tablename;
		$this->objFields[$colname]->fieldvalue1 = $fieldvalue1;
		$this->objFields[$colname]->fieldvalue2 = $fieldvalue2;
		$this->objFields[$colname]->fieldvalue3 = $fieldvalue3;
		$this->objFields[$colname]->fieldvalue4 = $fieldvalue4;
		$this->objFields[$colname]->fieldvalue5 = $fieldvalue5;
		$this->objFields[$colname]->colname = $colname;
		$this->objFields[$colname]->vcolname = $colname;
		$this->objFields[$colname]->cmsfieldtypeid 	= $this->getFieldTypeId($fieldtype);
		$this->objFields[$colname]->rowData = get_object_vars($objDB->fetchRow($this->tablename, $this->rowid, 'object'));
		
		if(count($arrExtraFields) > 0) {
			foreach ($arrExtraFields as $key => $value) {
				$this->objFields[$colname]->$key = $value;
			}
		}
		
		$this->objFields[$colname]->htmltagname = $colname."_".$this->rowid;
		
		if ($this->htmltagname) 
			$this->objFields[$colname]->htmltagname=$this->htmltagname."_".$this->objFields[$colname]->htmltagname;
	}
	
	function getFieldTypeId($classname){
		
		$objDB = tuksiDB::getInstance();		
		
		if (empty($this->arrIds[$classname])){
			$sql = "SELECT id FROM cmsfieldtype WHERE classname = '{$classname}'";
			$rs = $objDB->fetch($sql);
			
			if ($rs['num_rows'] !=1) return false;
			
			$this->arrIds[$classname] = $rs['data'][0]['id'];
		}
		
		return $this->arrIds[$classname];	
		
	}

	function addData($arrData) {
		foreach ($arrData as $key => $val) {
			$this->objData->{$key} = $val;
		}
	}
	function getFields(){
		if(@count($this->objFields)){
			$arrNames = array_keys($this->objFields);
			foreach($arrNames as $name){
				$arrData[$name] = $this->getField($name);
			}
		}
		return $arrData;
	}
	
	function getField($colname) {
		
		$objPage = tuksiBackend::getInstance();
		
		$this->objFields[$colname]->value	= $this->objData->{$colname};

		if (class_exists($this->objFields[$colname]->fieldtype)) {
			$field = new $this->objFields[$colname]->fieldtype($this->objFields[$colname], $objPage);
			$arr = $field->getHTML();
			$arr['fieldtype'] = $this->objFields[$colname]->fieldtype;
			return $arr;
		} else {
			return array("html" => "No class called " . $this->objFields[$colname]->fieldtype . "({$colname})");
		}
		
	}
	
	function getHtml(){
		
$template = <<<EOH
<TABLE width="80%">
	##ROW_START_LIST##
	<TR>
		<TD width="150" valign="##ROWDATA_VALIGN##">##ROWDATA_NAME##</TD>
		<TD>##ROWDATA_HTML##</TD>
	</TR>
	##ROW_END_LIST##
</TABLE>
EOH;
	
		$tpl = new tuksiSmarty($template);
		$arrFields = $this->getFields();
		if(is_array($arrFields)) {
			foreach($arrFields as $data){
				
				if($data['fieldtype'] == 'fieldHTMLEditor' || $data['fieldtype'] == 'fieldMultipleElement')
					$valign = "top";
				else
					$valign = "top";
					
				$arrList[] = array("NAME" => $data['name'],"HTML" => $data['html'],"VALIGN" => "top");
			}
		}
	
	//return $tpl->fetch();
	}

	function savefield($colname, $type = 0) {
		// SEtting type == 1 while add sqlpart to $arrSqlSave directly
		$objPage = tuksiBackend::getInstance();
		
		$this->objFields[$colname]->value = $_POST->getStr($this->objFields[$colname]->htmltagname);
		
		if (class_exists($this->objFields[$colname]->fieldtype)) {
			$field = new $this->objFields[$colname]->fieldtype($this->objFields[$colname], $objPage);
			if ($type == 1) {
				$this->sqlSaveAdd($field->savedata());
			} else {
				return $field->savedata();
			}
		} else {
		  return ""; 
		}
	}
	
	function saveFields(){
		
		if(@count($this->objFields)){
			$arrNames = array_keys($this->objFields);
			foreach($arrNames as $name){
				$this->savefield($name,1);
			}
		}		
		$this->sqlSaveExec();
	}
	
	function sqlSaveNew() {
		$this->sqlSave = array();
	}
	function sqlSaveAdd($sqlPart) {
		if ($sqlPart) {
			$this->arrSqlSave[] = $sqlPart;
		}
	}
	function sqlSaveExec() {

		$objDB = tuksiDB::getInstance();
		if($this->arrSqlSave){
			$this->sqlSaveUpdate = "UPDATE {$this->tablename} SET " . join(", ", $this->arrSqlSave) . " WHERE id = '{$this->rowid}'";
			$objDB->write($this->sqlSaveUpdate);
		}	
	}
} // End klasse tableview
?>
