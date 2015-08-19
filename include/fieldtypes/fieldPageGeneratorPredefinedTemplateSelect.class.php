<?
/**
 * Show Pages that uses modules
 * Fieldvalues:
 * None
 *
 * @package tuksiFieldType
 */

class fieldPageGeneratorPredefinedTemplateSelect extends field{

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {
	   
		$this->tpl = new tuksiSmarty();
		$objDB = tuksiDB::getInstance();
		
		$arrSet[] = array('id' => 0,'name' => 'ingen');
		
	   	$sqlTemplates = "SELECT * FROM cmstree WHERE parentid = 512";
	   	$arrReturn = $objDB->fetch($sqlTemplates);
	   	if ($arrReturn['num_rows'] > 0) {
	   		$this->tpl->assign('pretpl', $arrReturn['data']);
	   	} else {
	   		return "";
	   	}
	   /*$rsTemplates = $this->objDB->query($sqlTemplates);
	   if(mysql_num_rows($rsTemplates) > 0) {
	   	while($arrTpl = mysql_fetch_assoc($rsTemplates)) {
	   		$arrSet[] = $arrTpl;
	   	}	
	   	$this->tpl->assign('pretpl',$arrSet);
	   } else {
	   	return "";
	   }*/
	   
		$this->tpl->assign("fieldid", $this->objField->id);
		$this->tpl->assign("fieldcolname", $this->objField->colname);
		$this->tpl->assign("htmltagname",  $this->objField->htmltagname);		
		
		$HtmlTag = $this->tpl->fetch('fieldtypes/fieldPageGeneratorPredefinedTemplateSelect.tpl');
	   
	   return parent::returnHtml($this->objField->name,$HtmlTag);
		
	}
	
	function saveData() {
		
	}


	function getListHtml() {
		return $html;
	}
}
?>