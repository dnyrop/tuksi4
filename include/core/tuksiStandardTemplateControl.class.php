<?
/**
 * @todo PHP doc
 * @package tuksiBackend
 */

class tuksiStandardTemplateControl {
	
	private $tpl;
	private $arrHtml = array();
	private $arrGroup = array();
	private $arrHiddens = array();
	private $arrBannedButtons = array();
	
	function __construct(){
		$this->tpl = new tuksiSmarty();
	}
	
	public function addHeadline($headline, $collapsible = 0, $is_collapsed = 0){
		$this->arrGroup[] = array('headline' => $headline, "collapsible" => $collapsible, "is_collapsed" => $is_collapsed);
	}
		
	public function addElement($name,$html,$arrOptions = array()){
		
		if(!is_array($html)) {
			$html = array($html);
		}
		
		$this->arrGroup[] = array('element' => array(	'name' => $name,
																									'html' => $html,
																									'options' => $arrOptions));
	}
	
	public function addInputElement($name,$options){
		$html = tuksiFormElements::getInput($options);
		$this->addElement($name,$html);
	}
	
	public function addDateElement($name,$options){
		$html = tuksiFormElements::getDate($options);
		$this->addElement($name,$html);
	}
	
	public function addPasswordElement($name,$options){
		$options['type'] = 'password';
		$html = tuksiFormElements::getInput($options);
		$this->addElement($name,$html);
	}
	
	public function addSelectElement($name,$options){
		$html = tuksiFormElements::getSelect($options);
		$this->addElement($name,$html);
	}
	
	public function addRadioElement($name,$options){
		$html = "";
		if(is_array($options['options'])) {
			$arrRadioHtml = array();
			foreach ($options['options'] as $arrOption) {
				$arrOption['id'] = $options['id'];
				$arrOption['disabled'] = $options['disabled'];
				$arrRadioHtml[] = $arrOption['name'];
				$arrRadioHtml[] = tuksiFormElements::getRadio($arrOption);
			}
			$this->addElement($name,$arrRadioHtml);
		}
	}
	
	public function addCheckboxElement($name,$options){
		$html = tuksiFormElements::getCheckBox($options);
		$this->addElement($name,$html);
	}
	
	public function addButtonElement($name,$options,$arrHtmlOptions) {
		$html = tuksiFormElements::getButton($options);
		$this->addElement($name,$html,$arrHtmlOptions);
	}
	
	/**
	 * Tilføjer hiddenfield i $this->arrHiddens til senere indsættelse i skabelon
	 *
	 * @param array $arrField array med NAME og VALUE.
	 */
	public function addHiddenField($arrField) {
		if (!isset($arrField["VALUE"]))
			$arrField["VALUE"] = '';

		$this->arrHiddens[] = array("name" => $arrField["NAME"],'value' => $arrField["VALUE"]);
	}
	
	/**
	 * Tilføjer rå html til skabelon
	 *
	 * @param array $arrField array med NAME og VALUE.
	 */
	public function addHtml($html) {
		$this->arrHtml[] = array('html' => $html);
	}
	
	
	public function fetch(){

		$this->tpl->assign("group",$this->arrGroup);
		$this->tpl->assign("hiddens",$this->arrHiddens);
		$this->tpl->assign("rawhtml",$this->arrHtml);
		return $this->tpl->fetch(__CLASS__ . ".tpl");
	}
}
