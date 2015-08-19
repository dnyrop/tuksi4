<?

/**
 * Show Pages that uses modules
 * Fieldvalues:
 * None
 *
 * @package tuksiFieldType
 */

class fieldPageGeneratorModulePages extends field{

	function __construct($objField) {
		parent::field($objField);
	
	}

	function getHTML() {
	   
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$HtmlTag = parent::getHtmlStart();

		$objSelect = new tuksiFormElements();

		
		$sqlPages = "SELECT t.* FROM pg_content cam, cmstree t ";
		$sqlPages.= "WHERE cam.pg_moduleid = '{$this->objField->rowid}' AND t.id = cam.cmstreeid ";
		$sqlPages.= "GROUP BY cam.cmstreeid";

		$arrRsPages = $objDB->fetch($sqlPages);

		$arrOptions = array();
		$arrOptions[] = array('value' => '', 'name' => '');

		if ($arrRsPages['ok'] && $arrRsPages['num_rows'] > 0) {
			foreach ($arrRsPages['data'] as &$arrPage) {
				$url = "/{$arrConf['setup']['admin']}/?treeid=" . $arrPage['id'];

				$arrOptions[] = array('name' => $arrPage['name'] . " ({$arrPage['pg_urlpart_full']})", 'value' => $url);
			}
		}
		$HtmlTag.= $objSelect->getSelect(array(	'name' => $this->objField->htmltagname, 	
						'width' => 400,
						'options' => $arrOptions, 
		'onchange' => 'document.location = this.value'));

		return parent::returnHtml($this->objField->name,$HtmlTag);
	}
	
	function saveData() {
	}


	function getListHtml() {
		return $html;
	}
}
?>
