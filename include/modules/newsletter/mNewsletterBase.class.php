<?

/**
 * Enter description here...
 *
 * @todo PHP doc missing
 * @package tuksiBackendModule
 */
class mNewsletterBase {
	//return the html for the module
	function __construct(&$objMod, $tpl = "standard"){
	  
		$this->objMod = $objMod;
		$this->tplname = $tpl;
		$this->tpl = new tuksiSmarty();
	
	}
	function getHtml() {

		$arrConf = tuksiConf::getConf();
		
		$objPage = tuksiNewsletter::getInstance();
		$arrLinks = $objPage->getLinks();
		
		$this->tpl->assign("newsletter_link", $arrLinks);
		
		if($this->objMod->link) {			
			$this->tpl->assign("trackingurl", "{$arrConf['newsletter']['path']['url_site']}/mail_redirect/{$this->objMod->id}.[TRACKINGID]/");    
		}
	
		$template = "modules/newsletter/" . $this->tplname . ".html.tpl";
		
		if (file_exists(dirname(__FILE__) . "/../../../templates/" . $template)) {
			$this->addStandardFields();
			$html = $this->tpl->fetch($template, $this->objMod->id);
		} else {
			$html = "HTML skabelon kunne ikke findes: {$template}";
		}
		
		return $html;	
	}
	
	function getText() {
		
		$this->addStandardFields(false);
	
		$template = "modules/newsletter/" . $this->tplname . ".text.tpl";
		
		if (file_exists(dirname(__FILE__) . "/../../../templates/" . $template)) {
			$this->addStandardFields();
			$html = $this->tpl->fetch($template, $this->objMod->id);
		} else {
			$html = "HTML skabelon kunne ikke findes: {$template}";
		}
		
		return $html;		
	}
	
	
	/**
	 * Auto insert field from Tuksi fieldtypes
	 * cmsfielditem.speciel_frontend needs to be 1 to use getFrontendValue in class.
	 *
	 * @return array Module values
	 */
	public function addStandardFields() {

		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$arrFields = array();
		$sql = "SELECT fi.*, ft.classname ";
		$sql.= "FROM cmsfielditem fi, cmsfieldtype ft  ";
		$sql.= "WHERE fi.itemtype = 'pg' AND fi.cmsfieldtypeid = ft.id AND fi.relationid = '{$this->objMod->pg_moduleid}'";
		$sql.= "AND ft.speciel_frontend = 1 ";

		$arrReturn = $objDB->fetch($sql, array('type' => 'object'));
		
		foreach ($arrReturn['data'] as $arrFieldItem) {
			$arrFieldTypes[$arrFieldItem->colname] = $arrFieldItem;
    }
        
		foreach ($this->objMod as $key => $value) {
			
			if (isset($arrFieldTypes[$key]->cmsfieldtypeid)) {
				$classname = $arrFieldTypes[$key]->classname;
				$arrFieldTypes[$key]->value = $value;
				$arrFieldTypes[$key]->pg_moduleid = $this->objMod->id;
				$arrFieldTypes[$key]->rowid = $this->objMod->id;
				
				$objFieldType = new $classname($arrFieldTypes[$key]);
				
				$arrFields[$key] = $objFieldType->getFrontendValue();
				
			} else {
				$arrFields[$key] = $value;
			}
			if ($this->useUTF8) {
				if (!is_array($arrFields[$key]) && !is_object($arrFields[$key])) {
					$arrFields[$key] = tuksiTools::encode($arrFields[$key]);
				}
			}
		}
		
		$arrFields['id'] = $this->objMod->id;
		
	 	$this->tpl->assign("module", $arrFields);
	 	
	 	return $arrFields;

	} // End addStandardFields();
	
}
?>
