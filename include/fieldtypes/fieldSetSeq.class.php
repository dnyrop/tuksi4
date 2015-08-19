<?php

/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldSetSeq extends field {
	
	function __construct($objField) {
		parent::field($objField);
		
		$this->validateFieldvalue("Felttype 1", $this->objField->fieldvalue1, "Skal indeholde filter felt");
		$this->validateFieldvalue("Felttype 2", $this->objField->fieldvalue2, "Skal indeholde aktueller ID");
		$this->validateFieldvalue("Felttype 3", $this->objField->fieldvalue3, "Skal indeholde parentid.");
		
		$objPage = tuksiBackend::getInstance();
		$objPage->arrTree;
		
		$this->objField->fieldvalue2 = str_replace("#TREEID#", $objPage->arrTree['id'],$this->objField->fieldvalue2);
		$this->objField->fieldvalue3 = str_replace("#PARENTID#", $objPage->arrTree['parentid'],$this->objField->fieldvalue3);
		
		$this->sqlSeq = "SELECT id FROM cmstree WHERE {$this->objField->fieldvalue1} = '{$this->objField->fieldvalue3}' AND isdeleted = 0 ";
		$this->sqlSeq.= "ORDER BY seq,name";
		
		$this->post_move_up_name = $this->objField->htmltagname . '_page_move_up';
		$this->post_move_down_name = $this->objField->htmltagname . '_page_move_down';
	}
	
	function getHTML() {
		
		if ($arrReturn = $this->checkFieldvalues())
			return $arrReturn;
			
		$HtmlTag = parent::getHtmlStart();

		$HtmlTag .= '<input name="' . $this->post_move_up_name . '" value="0" type="hidden">';
		$HtmlTag .= '<input name="' . $this->post_move_down_name . '" value="0" type="hidden">';
		
		//getting sequens
		$intCount = 1;
		
		$objDB = tuksiDB::getInstance();
		
		$rsSeq = $objDB->fetch($this->sqlSeq);
		$nbRows = $rsSeq['num_rows'];
		
		//traveserer de noder i cmstree som har samme parent som det aktuelle
		foreach($rsSeq['data'] as $arrData) {
			
			$id = $arrData['id'];
			//Checkker om noden er den aktuelle
			
			if ($id == $this->objField->fieldvalue2){
				
				//Er den aktuelle node ikke den første vises der pil op
				if($intCount > 1) {
					$up = '<a href="#" onclick="document.tuksiForm.' . $this->post_move_up_name . '.value=1;doAction(\'SAVE\');return false;"><img src="/themes/default/images/icons/ic_arrowUp.png"></a>';
				}
				//Er den aktuelle node ikke den sidste vises der pil ned
				if($intCount < $nbRows) {
					$down = '<a href="#" onclick="document.tuksiForm.' . $this->post_move_down_name . '.value=1;doAction(\'SAVE\');return false;"><img src="/themes/default/images/icons/ic_arrowDown.png"></a>';
				}
			}
			$intCount++;
		}
		//blev der sat pil op/ned ellers sæt en spacer gif istedet
		if(!$up)
			$up = '<img src="/themes/default/images/icons/ic_arrowUp_deactivated.gif">';
		
		if(!$down)
			$down = '<img src="/themes/default/images/icons/ic_arrowDown_deactivated.gif">';	

		$html = $HtmlTag . $up . $down;
			
	
		return parent::returnHtml($this->objField->name, $html);
	}

	function saveData() {

		//Opdaterer rækkefølgen for siden ved at travesere cmstree
		//Skal det pågældende modul rykkes frem/tilbage får den sekvens 
		
		if ($_POST->getStr($this->post_move_up_name)) {
			$direction = 'up';
		} elseif($_POST->getStr($this->post_move_down_name)) {
			$direction = 'down';
		}
		
		if(isset($direction)) {
			$tuksiTree = new tuksiTree();
			$tuksiTree->alterNodeSequens($this->objField->fieldvalue2,$direction);
		}
   	
		return '';
	}
	
	function getListHtml() {
		return "";
	}
	
	
}

?>