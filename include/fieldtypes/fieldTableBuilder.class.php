<?
/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldTableBuilder extends field{

	function fieldTableBuilder($objField) {
		parent::field($objField, 1);
		
		$this->tbl_name="cmsfielddata";
		$this->tbl_relcol="cmsfielditemid";
		$this->tbl_rel2col="rowid";
		$this->tbl_rownbcol="nb_value1";
		$this->tbl_colnbcol="nb_value2";
		$this->tbl_contentcol="content";
		
	}

	function getHTML() {
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$tplPage = new tuksiSmarty();
		
		$tplPage->assign("fielditemid",$this->objField->id);
		$tplPage->assign("htmtagname",$this->objField->htmltagname);
		
		
		$this->setText(&$tplPage);
			
		list($colCount,$rowCount)=explode(";",$this->objField->value);
		
		$arrCmsText = array();
		
		$arrCmsText['add_col'] = $objPage->cmstext('add_col');
		$arrCmsText['delete_col'] = $objPage->cmstext('delete_col');
		$arrCmsText['add_row'] = $objPage->cmstext('add_row');
		$arrCmsText['delete_row']= $objPage->cmstext('delete_row');
		$tplPage->assign("readonly",$this->objField->readonly);
		$tplPage->assign("cmstext",$arrCmsText);
			
		//Hvis der ikke er oprettet nogle rækker	
		//Skal der minimum være en række og kolonne
		if($colCount<1)		
			$colCount=1;

		if($rowCount<1)
			$rowCount=1;
		//Find ud af hvilke knapper der skal vises i de forskellige
		//situationer udfra regler om max og min og exact
		$arrBTN=array(); //Array med informationer om hvorvidt knapper skal vises eller ej
						//Printes til templaten
		$arrBTN['show_del_col']=true;	
		$arrBTN['show_del_row']=true;
		$arrBTN['show_add_col']=true;
		$arrBTN['show_add_row']=true;
		
		if($this->arrFieldvalues['COL_EXACT']){
			$colCount=$this->arrFieldvalues['COL_EXACT'];		
			$arrBTN['show_del_col']=false;	
			$arrBTN['show_add_col']=false;
		}else{
			
			if($this->arrFieldvalues['COL_MAX'] && $colCount>=$this->arrFieldvalues['COL_MAX']){
				$arrBTN['show_add_col']=false;
				$colCount=$this->arrFieldvalues['COL_MAX'];	
			}
			
			if($this->arrFieldvalues['COL_MIN'] && $colCount<=$this->arrFieldvalues['COL_MIN']){
				$arrBTN['show_del_col']=false;
				$colCount=$this->arrFieldvalues['COL_MIN'];	
			}
		}
		
		if($this->arrFieldvalues['ROW_EXACT']){
			$rowCount=$this->arrFieldvalues['ROW_EXACT'];		
			$arrBTN['show_del_row']=false;	
			$arrBTN['show_add_row']=false;
		}else{
			

			if($this->arrFieldvalues['ROW_MAX'] && $rowCount>=$this->arrFieldvalues['ROW_MAX']){
				$arrBTN['show_add_row']=false;
				$rowCount=$this->arrFieldvalues['ROW_MAX'];	
			}

			if($this->arrFieldvalues['ROW_MIN'] && $rowCount<=$this->arrFieldvalues['ROW_MIN']){
				$arrBTN['show_del_row']=false;
				$rowCount=$this->arrFieldvalues['ROW_MIN'];	
			}
		}
			
		$tplPage->assign("btn",$arrBTN);
		//Hent data til tabellen
		
		$arrData=array(); //Array med data til tabellen
		$sqlFields="SELECT * FROM ".$this->tbl_name." WHERE ".$this->tbl_relcol."='".$this->objField->id."' AND ".$this->tbl_rel2col."='".$this->objField->rowid."'";
	
		$rsFields=$objDB->fetch($sqlFields,array("type" => "object"));
		
		
		if($rsFields['num_rows']){
			
			foreach($rsFields['data'] as $objData){
				$arrData[$objData->{$this->tbl_rownbcol}][$objData->{$this->tbl_colnbcol}]['ID']=$objData->id;
				$arrData[$objData->{$this->tbl_rownbcol}][$objData->{$this->tbl_colnbcol}]['CONTENT']=$objData->{$this->tbl_contentcol};
			}
		}
	
		$tplPage->assign("ROWCOUNT",$rowCount);
		$tplPage->assign("COLCOUNT",$colCount);
		
		$arrTable=array(); //array til at indeholde felterne med data
		//For hver række
		for($i=1;$i<=$rowCount;$i++){
			
			$returnHtml.="<tr>";
			//For hver kolonne
			for($j=1;$j<=$colCount;$j++){
				$arrTable[$i][$j]=$arrData[$i][$j]['CONTENT'];
			}
		}
		$tplPage->assign("arrData",$arrTable);

	//Dette array oprettes udelukkende af den grund at 
	//man ikke kan oprette variable i smarty, og dermed 
	//ikke køre en for eller while
	$arrCols=array();
	for($j=1;$j<=($colCount+2);$j++){
		$arrCols[$j]="";
	}
	$tplPage->assign("arrColumns",$arrCols);
	

	// Returning html
	$Html  = parent::getHtmlStart();
	//$Html .= $returnHtml;
	$Html .=$tplPage->fetch("fieldtypes/fieldtable_builder.tpl");
	return parent::returnHtml($this->objField->name,$Html);
	}

	function saveData() {
		$objDB = tuksiDB::getInstance();			
		$newRowCount=$rowCount=$_POST->getStr($this->objField->htmltagname.'_row_count');
		$newColCount=$colCount=$_POST->getStr($this->objField->htmltagname.'_col_count');
		
		//Hvis en række er blevet slettet tælles denne variabel op
		//indholdet af denne variabel trækkes fra iteratoren, da
		//De resterende rækker således vil blive skubbet op
		$rowsDeleted=0; 
		//same same for kolonner
		$colsDeleted=0;
		
		//gemte id'er
		$arrIDsaved=array();
		
		if($rowCount && $colCount){
			
			//Gennemløber alle rækker
			for($i=1;$i<=$rowCount;$i++){
				
				
				if($_POST->getStr($this->objField->htmltagname.'_del_row')==$i){
					$rowsDeleted++;	
					$newRowCount=$rowCount-1;
					
				//Pågældende række skal kun gemmes hvis den ikke er blevet slettet
				}else{	
										
					//Gennemløber alle kolonner
					for($j=1;$j<=$colCount;$j++){
						
						if($_POST->getStr($this->objField->htmltagname.'_del_col')==$j){
							$colsDeleted=1;
							$newColCount=$colCount-1;
						}else{
								
							//Bytter om på rækker og kolonner alt efter hvilke knapper der er trykket på
							//Hvis der er slettet en række eller kolonne skal de næste rækkenumre
							//have trukket 1 fra
							if($j>=$_POST->getStr($this->objField->htmltagname.'_del_col') && $colsDeleted)
								$save_colnb=($j-$colsDeleted);
							else 
								$save_colnb=$j;
							
							$save_rownb=($i-$rowsDeleted);
								
							//Hvis en række er rykket ned skal den næste rykkes op
							if($_POST->getStr($this->objField->htmltagname.'_'.$i.'_ned'))	
								$save_rownb=($i+1);
							if($_POST->getStr($this->objField->htmltagname.'_'.($i-1).'_ned'))			
								$save_rownb=($i-1);
							
							//Hvis en række er rykket op skal den forrige rykkes ned	
							if($_POST->getStr($this->objField->htmltagname.'_'.$i.'_op'))	
								$save_rownb=($i-1);
							if($_POST->getStr($this->objField->htmltagname.'_'.($i+1).'_op'))	
								$save_rownb=($i+1);
							
							$value=$_POST->getStr($this->objField->htmltagname.'_'.$i.'_'.$j);
							$value=htmlentities($value);							
								
							$sqlChk = "SELECT id FROM ".$this->tbl_name." WHERE ".$this->tbl_relcol."='".$this->objField->id."' AND ".$this->tbl_rel2col."='".$this->objField->rowid."' AND ".$this->tbl_colnbcol."='".$save_colnb."' AND ".$this->tbl_rownbcol."='".$save_rownb."'";
							
							$rsChk = $objDB->fetchItem($sqlChk);
							
							if($rsChk['num_rows']){
								$dataID = $rsChk['data']['id'];			
								$sqlUpdate="UPDATE ".$this->tbl_name." SET ".$this->tbl_contentcol."='".$value."' WHERE id='".$dataID."'";
								$objDB->write($sqlUpdate);			
							}else{	
								$sqlIns="INSERT INTO ".$this->tbl_name." (".$this->tbl_relcol.",".$this->tbl_rel2col.",".$this->tbl_colnbcol.",".$this->tbl_rownbcol.",".$this->tbl_contentcol.") VALUES ('".$this->objField->id."','".$this->objField->rowid."','".$save_colnb."','".$save_rownb."','".$value."')";
								$arrSaved = $objDB->write($sqlIns);
								$dataID = $arrSaved['insert_id'];
							}		
							$arrIDsaved[]=$dataID;
						}
					}
				}
			}
		}		
		
			$sqlDel="DELETE FROM ".$this->tbl_name." WHERE ".$this->tbl_relcol."='".$this->objField->id."' AND ".$this->tbl_rel2col."='".$this->objField->rowid."' AND id NOT IN(" . join(',', $arrIDsaved) . ")";
			$rsDel = $objDB->write($sqlDel);
		
		
		
		
		
		//print_r($_POST);
		if($_POST->getStr($this->objField->htmltagname."_add_col")){
			$newColCount=$colCount+1;
			
		}
		
		if($_POST->getStr($this->objField->htmltagname."_add_row")){
			$newRowCount=$rowCount+1;
		}
		
		
		//Der er for få kolonner
		if($this->arrFieldvalues['COL_MIN'] && $newColCount<$this->arrFieldvalues['COL_MIN'])	
			$newColCount=$this->arrFieldvalues['COL_MIN'];
			
		//Der er for mange kolonner
		if($this->arrFieldvalues['COL_MAX'] && $newColCount>$this->arrFieldvalues['COL_MAX'])	
			$newColCount=$this->arrFieldvalues['COL_MAX'];	
			
		if($this->arrFieldvalues['ROW_MIN'] && $newRowCount<$this->arrFieldvalues['ROW_MIN'])
			$newRowCount=$this->arrFieldvalues['ROW_MIN'];	
			

		if($this->arrFieldvalues['ROW_MAX'] && $newRowCount>$this->arrFieldvalues['ROW_MAX'])	
			$newRowCount=$this->arrFieldvalues['ROW_MAX'];			
		
			
		$value=$newColCount.";".$newRowCount;
		$sql = $this->objField->colname . " = '" . $value . "'";
				
		
		return $sql;
	}

	function getListHtml() {
		
		if ($this->objField->value) {
				$this->objField->value = $this->objPage->cmstext('yes'); 
			} else {
				$this->objField->value = $this->objPage->cmstext('no');
			}
		$html = $this->objField->value . "&nbsp;";
		
		return $html;
	}
	
	
	function copyData($rowid_to) {
		$objDB = tuksiDB::getInstance();
		
		$sqlFields = "SELECT * FROM ".$this->tbl_name." WHERE ".$this->tbl_relcol."='".$this->objField->id."' AND ".$this->tbl_rel2col."='".$rowid_from."'";
	
		$rsFields = $objDB->fetch($sqlFields,array("type" => "object"));
		if($rsFields['num_rows']){
			foreach($rsFields as $objData){
				$sqlIns ="INSERT INTO ".$this->tbl_name." (".$this->tbl_relcol.",".$this->tbl_rel2col.",".$this->tbl_colnbcol.",".$this->tbl_rownbcol.",".$this->tbl_contentcol.") ";
				$sqlIns.=" VALUES ('".$this->objField->id."','".$this->objField->rowid."','".$objData->{$this->tbl_colnbcol}."','".$objData->{$this->tbl_rownbcol}."','".$objData->{$this->tbl_contentcol}."')";
				$objDB->write($sqlIns);
			}
		}
	}
	
	function deleteData() {
		
		$objDB = tuksiDB::getInstance();
		
		$sqlDel = "DELETE FROM {$this->tbl_name} ";
		$sqlDel.= "WHERE ".$this->tbl_relcol."='".$this->objField->id."' AND ".$this->tbl_rel2col."='".$this->objField->rowid."'";
		$rsDel = $objDB->write($sqlDel);
		
		//$this->objPage->alert($sqlDel);
		
		return true;
	}
	
	function releaseData() {
		
		$objDB = tuksiDB::getInstance();
		
		$arrFieldIds=array();
		$sqlFields="SELECT * FROM ".$this->tbl_name." WHERE ".$this->tbl_relcol."='".$this->objField->id."'  AND ".$this->tbl_rel2col."='".$this->objField->rowid."'";
		$rsFields=$objDB->fetch($sqlFields,array("type" => "object"));
		
		foreach($rsFields['data'] as $objFields){
			$arrFieldIds[] = $objFields->id;
			$sqlRelease = "REPLACE INTO ".$this->tbl_name."live SELECT * FROM ".$this->tbl_name." WHERE id='".$objFields->id."' ";
			$rs = $objDB->write($sqlRelease);
		}
				
		$sqlDelete = "DELETE FROM ".$this->tbl_name."live ";
		$sqlDelete.= "WHERE ".$this->tbl_relcol."='".$this->objField->id."'  AND ".$this->tbl_rel2col."='".$this->objField->rowid."' AND id NOT IN(" . join(',', $arrFieldIds) . ")";
		$objDB->write($sqlDelete);
	}

	/**
	 * getFrontendValue 
	 * 
	 * @access public
	 * @return void
	 */
	function getFrontendValue() {
		$objDB = tuksiDB::getInstance();
    $arrConf = tuksiConf::getConf();
                
    $arrData=array();
        
    $sqlFields = "SELECT nb_value1, nb_value2, id, content FROM cmsfielddata{$arrConf['setup']['tableext']} ";
    $sqlFields.= "WHERE rowid='" . $this->objField->pg_moduleid . "' AND cmsfielditemid='" .$this->objField->id . "' ORDER BY nb_value1,nb_value2";

    $arrRsFields = $objDB->fetch($sqlFields);
    if($arrRsFields['num_rows']){
        	
       foreach ($arrRsFields['data'] as $arrItem) {
                    
          $arrData[$arrItem['nb_value1']]['cols'][$arrItem['nb_value2']]['id'] =$arrItem['id'];
          $arrData[$arrItem['nb_value1']]['cols'][$arrItem['nb_value2']]['content'] = $arrItem['content'];
                           
       }
     }
        
     return $arrData;        
	}
}

?>
