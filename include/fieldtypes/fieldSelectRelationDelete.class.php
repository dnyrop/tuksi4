<?

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */

class fieldSelectRelationDelete extends field {

	function fieldSelectRelationDel($objField) {
		parent::field($objField);
	}

	function getHTML() {
		$objDB = tuksiDB::getInstance();
		$arrConf =tuksiConf::getConf();
		$HtmlTag = parent::getHtmlStart();
				
		$maintable = $this->objField->tablename;
		list($relationtable,$secondtable)=explode(";",$this->objField->fieldvalue1);
					
		list($head1,$head2)=explode(",",$this->objField->fieldvalue4);					
		
		$this->elementname = $this->objField->elementname;
		$checknew = "TABLE_" . $this->objField->id . "_newrow";
		
		if($this->objField->fieldvalue3){
			if(strpos($this->objField->fieldvalue3,",")){
				list($colname1,$colname2)=explode(",",$this->objField->fieldvalue3);
				$weight2=" ,rel.".$colname2;
				$weight2head="<td>".$head2."</td>";
			}
			else{
				$weight2="";
				$weight2head="";
				$colname1=$this->objField->fieldvalue3;
			}
		}
		
		if($this->objField->fieldvalue3){
			$sqlRel="SELECT rel.id, sec.name, rel.".$colname1." ".$weight2.", sec.id as sid  FROM ".$secondtable." sec, ".$relationtable." rel WHERE rel.".$maintable."id=".$this->objField->rowid." AND rel.".$secondtable."id=sec.id ORDER BY sec.name";
		}else{
			$sqlRel="SELECT rel.id, sec.name, sec.id as sid  FROM ".$secondtable." sec, ".$relationtable." rel WHERE rel.".$maintable."id=".$this->objField->rowid." AND rel.".$secondtable."id=sec.id ORDER BY sec.name";
		}	
		//print $sqlRel;
		
		$querel = $objDB->fetch($sqlRel,array("type" => "object"));
		$arrRels=array();
		$arrCheck=array();
		$count=0;
		//print_r($querel);
		if($querel['num_rows']){
			foreach($querel['data'] as $objRel){
					
				$arrCheck[]=$objRel->sid;
				$arrRels[$count]["id"]=$objRel->id;
				$arrRels[$count]["name"]=$objRel->name;
				if($colname1){
					$arrRels[$count]["weight"]=$objRel->{$colname1};
				}
				if($colname2){
					$arrRels[$count]["weight2"]=$objRel->{$colname2};
				}
				
				$count++;
			}
		}
		if(!$this->objField->fieldvalue2){
			$this->objField->fieldvalue2="SELECT id, name FROM ".$secondtable;
		}
	
		$sqlGet = $this->objField->fieldvalue2;
		$queGet = $objDB->fetch($sqlGet,array("type" => "object"));
		//SELECTED
		//Show overall editable info
		$HtmlTag.=$err;
		$HtmlTag.="<SELECT  NAME=\"".$this->objField->htmltagname."_REL[]\" MULTIPLE SIZE=\"10\" CLASS=\"forminput300\" STYLE=\"height:100px\">\n";
		$HtmlTag.="<OPTION > </OPTION>";	
		
		foreach($queGet['data'] as $objGet){
			if(!in_array($objGet->id,$arrCheck)){
			
				$selected="";
				if(in_array($objGet->id, $arrRels)){
				$selected="SELECTED";	
				}
				$name=$objGet->name;
				$HtmlTag.="<OPTION VALUE=\"".$objGet->id."\" ".$selected.">".$name."</OPTION>\n";	
			}
		}
		$HtmlTag.="</SELECT>";
		//$HtmlTag.="<a name=\"{$this->objField->htmltagname}\"></a>&nbsp;<input type=\"image\" CLASS=\"formminibutton\" name=\"BTNNEWELEMENT\" src=\"{$arrConf['path']['vimages']}mini_tilfoej.gif\" onmouseover=\"this.src = '{$arrConf['path']['vimages']}mini_tilfoej_over.gif'\" onmouseout=\"this.src = '{$arrConf['path']['vimages']}mini_tilfoej.gif'\" onclick=\"saveData('{$this->objField->htmltagname}');\">\n";
					$HtmlTag.= tuksiFormElements::getButton(array(
															"postvalue" => $this->objField->htmltagname,
															"icon" => "add",
															"value" => "Add" ));
		$HtmlTag.="<input name=\"$checknew\" value=\"0\" type=\"hidden\"><table>";
		
		
		if($arrRels[0]){
			$HtmlTag.="<tr><td width=\"200\"></td><td>".$head1."</td>".$weight2head."</tr>\n";	
		}
		
		foreach($arrRels as $rel){
			
			$HtmlTag.="<tr><td width=\"200\">".$rel["name"]."</td>\n";	
			if($colname1){
				//$HtmlTag.="<td width=\"200\"><input type=\"text\" name=\"".$this->objField->htmltagname."_".$rel["id"]."_weight\" class=\"forminput200\" value=\"".$rel["weight"]."\"></td>\n";	
			}
			if($colname2){	
				//$HtmlTag.="<td width=\"200\"><input type=\"text\" name=\"".$this->objField->htmltagname."_".$rel["id"]."_weight2\" class=\"forminput200\" value=\"".$rel["weight2"]."\"></td>\n";	
			}
			//$HtmlTag.="<td align=\"left\"><input type=\"image\" CLASS=\"formminibutton\"  name=\"".$this->objField->htmltagname."_".$rel["id"]."_DEL\" src=\"{$arrConf['path']['vimages']}mini_slet.gif\" onmouseover=\"this.src = '{$arrConf['path']['vimages']}mini_slet_over.gif'\" onmouseout=\"this.src = '{$arrConf['path']['vimages']}mini_slet.gif'\"onclick=\"saveData('{$this->objField->htmltagname}');\"> </td></tr>\n";	
			
			
			$HtmlTag.= "<td align=\"left\">" .  tuksiFormElements::getButton(array(
															"postvalue" => $this->objField->htmltagname . "_" . $rel["id"] . "_DEL",
															"icon" => "delete",
															"value" => "Delete" )) . "</td>";
			
		}		
		
		


		$HtmlTag.="</table>";
		
			return parent::returnHtml($this->objField->name, $HtmlTag);
	}
	
	function saveData() {

		$objDB = tuksiDB::getInstance();
		$arrVals = $_POST->getArray($this->objField->htmltagname."_REL");
		
		
		$maintable = $this->objField->tablename;
		list($relationtable,$secondtable)=explode(";",$this->objField->fieldvalue1);
			
		if($this->objField->fieldvalue3){
			if(strpos($this->objField->fieldvalue3,",")){
				list($colname1,$colname2)=explode(",",$this->objField->fieldvalue3);
			}
			else{
				$colname1=$this->objField->fieldvalue3;
			}
		}
		if(is_array($arrVals)){
			
			foreach($arrVals as $val){	
				$sqlCheck="SELECT * FROM ".$relationtable." WHERE ". $secondtable."id=".$val." AND ".$maintable."id=".$this->objField->rowid;	
				$rsCheck=$objDB->fetch($sqlCheck);	
				if($rsCheck['num_rows']){
						
				}else{
					
					$sqlUpdate="INSERT INTO ".$relationtable." ({$maintable}id, {$secondtable}id) VALUES ('{$this->objField->rowid}','{$val}')";
					$rsUpdate=$objDB->write($sqlUpdate);
			
				}
			}
		}

			//$sqlSec=$this->objField->fieldvalue2;
			$sqlSec="SELECT * FROM ".$relationtable."";
			$rsSec=$objDB->fetch($sqlSec,array("type" => "object"));		
	
			foreach($rsSec['data'] as $objSec){
				IF($_POST->getStr($this->objField->htmltagname."_".$objSec->id."_weight")){
					
					$value=$objDB->escapeString($_POST->getStr($this->objField->htmltagname."_".$objSec->id."_weight"));
					$sqlUpd="UPDATE ".$relationtable." SET ".$colname1."='".$value."' WHERE id=".$objSec->id;
					$rsUpd=$objDB->write($sqlUpd);
					
				}
				
				IF($_POST->getStr($this->objField->htmltagname."_".$objSec->id."_weight2")){
					
					$value=$objDB->escapeString($_POST->getStr($this->objField->htmltagname."_".$objSec->id."_weight2"));
					$sqlUpd="UPDATE ".$relationtable." SET ".$colname2."='".$value."' WHERE id=".$objSec->id;
					$rsUpd=$objDB->write($sqlUpd);
				}
				
				IF($_POST->getStr($this->objField->htmltagname."_".$objSec->id."_DEL") || $_POST->getStr($this->objField->htmltagname."_".$objSec->id."_DEL_x")){
					
					$sqlDel="DELETE FROM ".$relationtable." WHERE id=".$objSec->id;
					$rsDel=$objDB->write($sqlDel);
				}

			}

	}
	function deleteData() {
		
		
		$maintable = $this->objField->tablename;
		list($relationtable,$secondtable)=explode(";",$this->objField->fieldvalue1);
		
		$sqlDel="DELETE FROM ".$relationtable." WHERE ".$maintable."id=".$this->objField->rowid."";	
		print $sqlDel;
	}
		function getListHtml() {
		$objDB = tuksiDB::getInstance();
		
		
		$maintable = $this->objField->tablename;
		list($relationtable,$secondtable)=explode(";",$this->objField->fieldvalue1);
		
		if($this->objField->fieldvalue3){
			if(strpos($this->objField->fieldvalue3,",")){
				list($colname1,$colname2)=explode(",",$this->objField->fieldvalue3);
				$col2=",rel.".$colname2." as seq2";
				
			}
			else{
				$colname1=$this->objField->fieldvalue3;
				$col2="";
			}
		}
		
		if($this->objField->fieldvalue3){
			$sqlList="SELECT t2.name as name, rel.".$colname1."  as seq ".$col2." FROM ".$relationtable." rel , ".$secondtable." t2 WHERE rel.".$maintable."id=".$this->objField->rowid." AND rel.".$secondtable."id=t2.id ORDER BY t2.name";		
		}else{
			$sqlList="SELECT t2.name as name FROM ".$relationtable." rel , ".$secondtable." t2 WHERE rel.".$maintable."id=".$this->objField->rowid." AND rel.".$secondtable."id=t2.id ORDER BY t2.name";		
		}	
				
		$queList=$objDB->fetch($sqlList,array("type" => "object"));
		$sep="";
		$names="";
			
		foreach($queList['data'] as $obj){
			
			$weight2="";
			if($colname2){
				$weight2=", ".$obj->seq2;
			}
			
			$names.=$sep.$obj->name." - ".$obj->seq.$weight2;
			$sep="<br>";
		}
		//error_log();
		return $names;		
	}

	function releaseData() {
		$objPage = tuksiBackend::getInstance();
		
		$maintable = $this->objField->tablename;
		list ($relationtable, $secondtable) = explode(";", $this->objField->fieldvalue1);
		
		$sqlAppend = sprintf(" WHERE %sid = %d", $maintable, $this->objField->rowid);

		if (!tuksiRelease::releaseTable($relationtable, null, $sqlAppend)) {
			$objPage->alert($objPage->cmstext("pagereleasefailed"));
		}
			
	}

} // END Class
?>
