<?php


/**
 * Selectbox from other table
 * Fieldvalues:
 * [FIELDVALUE1] = 	SQL string with id, name output
 * [FIELDVALUE2] = 	'Choose value' text
 * [FIELDVALUE3] =	Input width [200,400,300,600]
 * [FIELDVALUE4] =	If you need to disable and use it for showing a selection [1]
 *
 * @package tuksiFieldType
 */

class fieldSelectFromTable extends field{

	function fieldSelectFromTable($objField){
		parent::field($objField);
	}
	
	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
		
		$htmlReturn = "";
		
		// Checking is width should be changed
		if (!$this->objField->fieldvalue3) {
			$this->objField->fieldvalue3 = "200";	
		}
		
		$sqlOptions = $objDB->validateSelectSQL($this->objField->fieldvalue1);
		$sqlOptions = $this->rowDataReplace($sqlOptions);
		$sqlOptions = str_replace('#ROWID#', $this->objField->rowid, $sqlOptions);		
		
		if (tuksiIni::$arrIni['setup']['system'] == 'backend') {
			$objPage = tuksiBackend::getInstance();
			$arrConf = tuksiConf::getPageConf($objPage->arrTree['id']);
			
			$sqlOptions = str_replace('#CMSSITEID#', $arrConf['id'], $sqlOptions);		
			$sqlOptions = str_replace('#CMSSITELANGID#', $arrConf['cmssitelangid'], $sqlOptions);		
		}
		
		if ($sqlOptions) {
		
			$arrRsOptions = $objDB->fetch($sqlOptions);
			
			if ($arrRsOptions['ok']) {
				
				$arrOptions = array();
				
				// check if disabled
				$arrOptions['disabled'] =  $this->objField->fieldvalue4 ? true : false;
				//set class
				$arrOptions['width'] = $this->objField->fieldvalue3;
				$arrOptions['name'] = $this->objField->htmltagname;								
				
				$arrOptions['selected'] = $this->objField->value;
				
				$arrOptions['onchange'] = $this->objField->ONCHANGE;
				
				if($this->objField->fieldvalue2) {
				  $arrOptions['options'][] = array(	"value" => "",
					  								"name" => $this->objField->fieldvalue2);
				}
				
				foreach($arrRsOptions['data'] as &$arrOption) {
					$arrOptions['options'][] = array(	"value" => $arrOption['id'],
														"name" => $arrOption['name']);
				}
				
				$arrOptions['disabled'] = $this->objField->readonly;
				
				$htmlReturn = tuksiFormElements::getSelect($arrOptions);
				
			} else {
				$htmlReturn = 'Error in SQL: ' . $arrRsOptions['error'];
			}
		} else {
			$htmlReturn = " SQL string is invalid.";	
		} 
		
		$Html  = parent::getHtmlStart();
		$Html .= $htmlReturn;
		return parent::returnHtml($this->objField->name,$Html);
		
	}

	function saveData()	{
		$sql = $this->objField->colname . " = '" . $this->objField->value . "'";
		return $sql;
	}
	
	function getListHtml() {
		
		$objDB = tuksiDB::getInstance();
			
		list($sql) = preg_split("/(order by)/i", $this->objField->fieldvalue1);
		list($sql,$where) = preg_split("/(where)/i", $sql);

		if($this->objField->fieldvalue5)
			$fieldname = $this->objField->fieldvalue5;
		else
			$fieldname = "id";
		
		if($where)
			$sql .= "WHERE ($where) AND $fieldname = '" .$this->objField->value."'";
		else
			$sql .= " WHERE $fieldname = '" .$this->objField->value."'";
		
		$sql = $objDB->validateSelectSQL($sql);	
		$sql = $this->rowDataReplace($sql);
		$Result = $objDB->fetch($sql) or $html .= "(".$this->objField->fieldvalue1."): " . mysql_error();

		if ($Result['num_rows']) { 
			$html.= $Result['data'][0]['name']. "&nbsp;";
		}
			
		return $html;
	}
}

?>
