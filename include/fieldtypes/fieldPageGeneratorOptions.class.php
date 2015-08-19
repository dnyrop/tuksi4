<?


/**
 * Template Class
 *
 * @package tuksiFieldType
 */

class fieldPageGeneratorOptions extends field {

	function __construct($objField) {
		parent::field($objField);
	}

	function getHTML() {

		$HtmlTag = parent::getHtmlStart();
		//$this->objPage->alert(print_r($this->objField, 1));

		$objFieldItem = new tuksiFielditemPageGeneratorOptions($this->objField->rowid);

		$arrFields = $objFieldItem->getFields();
		

		$HtmlTag.= '<table border=0 width=100%>' . $objFieldItem->getHTML($arrFields, 'option') . '</table>';

		return parent::returnHtml('', $HtmlTag);
	}

	function saveData() {

		$objFieldItem = new tuksiFielditemPageGeneratorOptions($this->objField->rowid);

		$arrFields = $objFieldItem->getFields();

		$objFieldItem->saveData($arrFields);

		return '';
	}
	
	function getListHtml() {
		return "";
	}
	
	function copyData($rowid_to) {
		$objFieldItem = new tuksiFielditemPageGeneratorOptions($this->objField->rowid);
		
		$objFieldItem->copyItem($rowid_to);
	}
	
	function deleteData() {
		$objFieldItem = new tuksiFielditemPageGeneratorOptions($this->objField->rowid);
		//$this->objPage->alert('ROWID: ' . $this->objField->rowid);
		
		$objFieldItem->deleteItems();
	}

} // END Class
?>
