<?
/**
 * Show Pages that uses modules
 * Fieldvalues:
 * None
 *
 * @package tuksiFieldType
 */

class fieldPageGeneratorPredefinedTemplate extends field{

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {
	   
		$this->tpl = new tuksiSmarty();
		$objDB = tuksiDB::getInstance();
		
		$sqlTemplates = "SELECT * FROM cmstree WHERE pg_page_templateid = '{$this->objPage->arrHiddens['ROWID']}' AND parentid = 512";
		$arrReturn = $objDB->fetch($sqlTemplates);
		if ($arrReturn['num_rows'] > 0) {
			$this->tpl->assign('pretpl', $arrReturn['data']);
		}
		/*$rsTemplates = $objDB->fetch($sqlTemplates);
		if(mysql_num_rows($rsTemplates) > 0) {
			while($arrTpl = mysql_fetch_assoc($rsTemplates)) {
				$arrSet[] = $arrTpl;
			}	
			$this->tpl->assign('pretpl',$arrSet);
		}*/
		
		//$this->tpl->assign("ny_template", $this->objPage->cmstext("ny_predefinerettemplate"));
		$this->tpl->assign("fieldid", $this->objField->id);
		$this->tpl->assign("fieldcolname", $this->objField->colname);
		$this->tpl->assign("htmltagname",  $this->objField->htmltagname);		
		
		$HtmlTag = $this->tpl->fetch('fieldtypes/fieldPageGeneratorPredefinedTemplate.tpl');
	   
	   return parent::returnHtml($this->objField->name,$HtmlTag);
		
	}
	
	function saveData() {
	
		// * ------------------------------------------------------------------ * 
		// Ser om brugeren har rettigheder til at oprette en ny underside 
		// * ------------------------------------------------------------------ * 
		if ($this->objPage->PERM['SAVE'] && $_POST['TABLE_'.$this->objField->colname.'_'.$this->objField->id.'_newrow']) {
			
			include_once('../../modules/pagegenerator/pagegenerator.lib.php');
			
			//kopierer den aktuelle node til en ny node som har den aktuelle node som parent
			//derudover ændrer den typen af den aktuelle node til 20 (folder med indhold)
			//og den nyoprettede node til 4 (dokument)
			$objTreeFunc = new tuksi_tree($objPage);
			$arrNew = $objTreeFunc->copyTreeNode(238,512, 20, 4);
				
			//sikrer preview
			$sqlIns = "INSERT INTO cmstreeelement (cmsvariableid,value,cmstreeid,cmstreetabid) VALUES (45,'Preview','{$arrNew['NEWTREEID']}','{$arrNew['NEWTABID'][0]}')";
			$this->objDB->query($sqlIns);
				
			//Sikrer at 'settings' knappen er synlig på den nyoprettede node 
			$sqlUpdate = "UPDATE cmstree SET pg_show_settings = 1,pg_notinsearch = 1,pg_datechanged = now() WHERE id = '{$arrNew['NEWTREEID']}'";
			$this->objDB->query($sqlUpdate);
			
			//logger oprettelsen af den nye side
			$arrUser = tuksiBackendUser::getUserInfo();
			pg_log(2,$arrUser['id'],$arrNew['NEWTREEID']);
			
			//redirecter til 'settings' for den nyoprettede node
			header("location: /modules/pagegenerator/pagegenerator.php?TREEID=".$arrNew['NEWTREEID']."&TABID=".$arrNew['NEWTABID'][0]."&SETACTIVEMENU=".$arrNew['NEWTREEID']."&AREAID=-1&newpage=1");
			exit();	
			
		} //end if add
	}


	function getListHtml() {
		return $html;
	}
}
?>