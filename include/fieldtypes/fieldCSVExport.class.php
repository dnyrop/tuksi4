<?
/**
 * @package tuksiFieldType
 */

class fieldCSVExport extends field {

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
	$sql=$this->objField->fieldvalue1; //the sql
	
	//Fetch field names
	$sqlLim=$sql." LIMIT 0,1";
	if($sql){
						
		$fields = $objDB->fetch($sqlLim);
		if($fields['num_rows']){
			$row= $fields['data'][0];
			while(list($key,$val) = each($row) ) {
				$arrKeys[]=$key;
				$csvContent.=$key.";";
			}
			
			$csvContent.="\n";   
			       
			//Fetch table data
			$rs=$objDB->fetch($sql,array("type" => "object"));
			if($rs['ok'] && $rs['num_rows']){
				$count=0;	
				foreach($rs['data'] as $obj){
					
					foreach($arrKeys as $col){
									
						$tmp=$obj->{"".$col};
						$tmp=str_replace("\n","-",$tmp);
						$tmp=str_replace("\r","-",$tmp);
						$csvContent.=str_replace(";","",$tmp).";";
						
					}
				
					$csvContent.="\n";   
				}
			}	
			
			if($this->objField->fieldvalue2)
				$BTNname=$this->objField->fieldvalue2;
			else
				$BTNname="Hent fil";
			
			$content = tuksiFormElements::getInput(array(	'type' => 'hidden',
															'id' => $this->objField->htmltagname."_download",
															'value' => 0));
			$content.= tuksiFormElements::getButton(array(	'action' => "DOWNLOAD",
															'value' => $BTNname,
															'onclick' => "tuksiForm." . $this->objField->htmltagname."_download.value = '1';"));
			//$content= "<input type=\"submit\" value=\"".$BTNname."\" class=\"formbutton\" name=\"".$this->objField->htmltagname."_download\" onclick=\"document.tablesform.BTNSAVEAUTO.value = 1;\">";
		
		}
	}
	
	if($_POST->getStr($this->objField->htmltagname."_download")){
		header('Cache-Control: public'); 
		header("Content-Type: application/vnd.ms-excel\n");
		header("Content-Disposition: filename=datafile.cvs"); 
	 	header("Content-transfer-encoding: binary\n"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0\n"); 
		header("Pragma: no-cache\n"); 
		header('Content-Disposition: attachment; filename="datafile.csv"');
		
		header("Expires: 0");
		echo $csvContent;	
		exit();
	}
		$Html  = parent::getHtmlStart();
		$Html .= $content;
		return parent::returnHtml($this->objField->name, $Html);
	
	}// getHTML
	
	function saveData() {
		global $TUKSI;
	    
		return "";
	}
	function getListHtml() {
		return $this->objField->value;
	}

} // END Class
?>
