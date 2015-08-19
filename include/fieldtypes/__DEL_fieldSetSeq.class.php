<?
// Template Class


/**
 * Enter description here...
 *
 * @package tuksiFieldType
 */
class fieldSetSeq extends field {

	function fieldSetSeq($objField) {
		parent::field($objField);


		$this->validateFieldvalue("Felttype 1", $this->objField->fieldvalue1, "Skal indeholde filter felt");
		$this->validateFieldvalue("Felttype 2", $this->objField->fieldvalue2, "Skal indeholde aktueller ID");
		$this->validateFieldvalue("Felttype 3", $this->objField->fieldvalue3, "Skal indeholde parentid.");
		
		$this->sqlSeq = "SELECT id FROM cmstree ";
		$this->sqlSeq.= "WHERE {$this->objField->fieldvalue1} = '{$this->objField->fieldvalue3}' AND isdeleted = 0 ";
		$this->sqlSeq.= "ORDER BY seq,name";
		
		$this->post_move_up_name = $this->objField->htmltagname . '_page_move_up';
		$this->post_move_down_name = $this->objField->htmltagname . '_page_move_down';
		
	}

	function getHTML() {
		
		
		if ($arrReturn = $this->checkFieldvalues())
			return $arrReturn;
		
		$objDB = tuksiDB::getInstance();
			
		$HtmlTag = parent::getHtmlStart();

		$HtmlTag .= '<input name="' . $this->post_move_up_name . '" value="0" type="hidden">';
		$HtmlTag .= '<input name="' . $this->post_move_down_name . '" value="0" type="hidden">';
		
		//getting sequens
		$intCount = 1;
	
		$arrRsSeq = $objDB->fetch($this->sqlSeq);
		
		//traveserer de noder i cmstree som har samme parent som det aktuelle
		foreach($arrRsSeq['data'] as &$arrSeq) {
			//Checkker om noden er den aktuelle
			if ($arrSeq['id'] == $this->objField->fieldvalue2){
				
				//Er den aktuelle node ikke den første vises der pil op
				if($intCount > 1)
					$up = '<a href="#" onclick="document.forms[0].' . $this->post_move_up_name . '.value=1;saveData();return false;" class="mini_button"><span class="mini_bn_st"></span><span class="mini_a_up">UP</span><span class="mini_bn_end"></span></a>';
					
				//Er den aktuelle node ikke den sidste vises der pil ned
				if($intCount < $arrRsSeq['num_rows'])
					$down = '<a href="#" onclick="document.forms[0].' . $this->post_move_down_name . '.value=1;saveData();return false;" class="mini_button"><span class="mini_bn_st"></span><span class="mini_a_down">DOWN</span><span class="mini_bn_end"></span></a>';
			}
			$intCount++;
		}
		//blev der sat pil op/ned ellers sæt en spacer gif istedet
		if(!$up)
			$up = '<span class="mini_up_down_spacer"></span>';
			
		if(!$down)
			$down = '<span class="mini_up_down_spacer"></span>';


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

} // END Class
?>
