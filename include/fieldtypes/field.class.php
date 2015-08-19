<?


/**
 * Grundlægende klasse for alle felttyper. Henter alle ekstra feltværdier, og kan validere at alle påkrævet feltværdier er sat. 
 *
 * @todo PHP doc
 * @package tuksiFieldType
 */
class field {
	
	/**
	 * Indeholder ekstra værdier
	 *
	 * @var unknown_type
	 */
	public $arrFieldvalues = array();
	
	/**
	 * Indeholder tekster til felttypen. Hentes kun engang.
	 *
	 * @var unknown_type
	 */
	static $arrCmsText = array();
	private $arrError = array();
	
	public $debug = false;
	
	function field($objFieldItem, $getExtraFieldValues = 0) {
		$this->objField = $objFieldItem;

		// Sætter eller henter ekstra fieldvalues
		if ($getExtraFieldValues) {
						if (isset($this->objField->arrExtraFieldValues) && is_array($this->objField->arrExtraFieldValues)) 
				$this->setExtraFieldValues(); // Hent fra $objField
			elseif ($getExtraFieldValues)
				$this->getExtraFieldValues(); // Hent fra cmsfieldvalues tabel
		}
	
		// Array til håndtering af fejl i feltværdier
		$this->arrError = array();
	}
	
	function setObjField($objFieldItem){
		$this->objField = $objFieldItem;
	}
	
	// * ---------------------------------------------------------------------- *
	// Sætter ekstra fieldvalues ude fra objField->arrExtraFieldValues
	// * ---------------------------------------------------------------------- *
	function setExtraFieldValues() {
		$this->arrFieldvalues = $this->objField->arrExtraFieldValues;
	}
	
	// * ---------------------------------------------------------------------- *
	// Henter ekstra fieldvalues ude fra objField ID og fieldvaluetablename
	// Skrives til $this->arrFieldvalues, som kan bruges i fieldtype klassen
	// * ---------------------------------------------------------------------- *
	function getExtraFieldValues() {

		$objDB = tuksiDB::getInstance();
			
		$sqlFieldvalues = "SELECT v.name AS colname, f.value ";
		$sqlFieldvalues.= "FROM cmsfieldvalue f, cmsvariable v  ";
		$sqlFieldvalues.= "WHERE f.cmsvariableid = v.id  AND f.cmsfielditemid = '{$this->objField->id}'";

		$arrRsFieldvalues = $objDB->fetch($sqlFieldvalues);
		
		if($arrRsFieldvalues['ok']) {	
			foreach ($arrRsFieldvalues['data'] as &$arrFieldValue) {
				$this->arrFieldvalues[$arrFieldValue['colname']] = $arrFieldValue['value'];
			}
		}
	}
	
	function makeError() {
		if(count($this->arrError) > 0) {
			$this->htmlError = "test";
		}
		
		/*if (isset($GLOBALS['error']) && $GLOBALS['error'][$this->objField->vcolname])
			$this->htmlError = "<span class=\"error\">" . $GLOBALS['error'][$this->objField->vcolname] . '</span><BR>';
		else
			$this->htmlError = "";*/
	}
	
	function checkFieldvalues() {
		if (count($this->arrError)) {
			$HtmlTag = join("<br />", $this->arrError);
			$HtmlTag .= "<br />";
			
			return $this->returnHtml($this->objField->name, $HtmlTag);
		}
	}

	function unitTest() {
		return $this->unitTestResult('1', 'unittest funktion ikke fundet');
	}

	function unitTestResult($ok, $error = '') {
		return array('ok' => $ok, 'error' => $error);
	}

	/**
	 * Validering af feltværdi. Tjekke om flettet er sat.
	 *
	 * @param string $name Feltværdi navn
	 * @param string $value
	 * @param string $error
	 */
	function validateFieldvalue($name, $value, $error = "Fejl ") {

		if (!$value)
			$this->arrError[] = $name . ': ' . $error;
	}
	
	/**
	 * Lav en fejlbesked til bruger, som stopper visning af felttype.
	 *
	 * @param string $name
	 * @param string $error
	 */
	function setError($name, $error) {
		$this->arrError[] = $name . ': ' . $error;
	}
	
	function getError() {
		return $this->htmlError;
	}

	function getHelptest() {
		if (isset($this->objField->helptext) && $this->objField->helptext)
			return $this->objField->helptext . "<br />";
		else
			return "";
	}

	function getHtmlStart() {
		return $this->htmlError . $this->getHelptest();
	}

	function returnHtml($name, $html,$arrOptions = array()) {
		return array("name" => $name, "html" =>  $html,"options" => $arrOptions);
	}
	
	/**
	 * Return sprogrelvant cmstekst-streng for det aktelle modul
	 * Det samlede array af cmstekster for modulet caches i statisk variabel og hentes dermed kun een gang
	 *
	 * @param string $str variabel navn
	 * @return string cms tekst streng
	 */
	
	protected function cmstext($str) {
		
		$field = &self::$arrCmsText[$this->objField->cmsfieldtypeid];
		
		// Returner en dummy-streng, så man kan se hvis en tekst mangler at blive sat op i tuksi
		if ($field[$str]) {
			return $field[$str];
		} else {
			return "[$str]";
		}
	}
	
	/**
	 * Checker om cmstext array'et for modulet er cachet, ellers hentes det
	 *
	 */
	
	private function checkCmstextCache(){
		// Er array'et af cms-tekst hentet for dette modul, hvis ikke, så hent det
		if (!self::$arrCmsText[$this->objField->cmsfieldtypeid])
			self::$arrCmsText[$this->objField->cmsfieldtypeid] = $this->lookupCmstext();
			
	}
	
	
	/**
	 * Returner hele arrayet med cmstekster for det givne modul
	 *
	 * @return array
	 */
	
	protected function getArrCmstext(){
		$this->checkCmstextCache();
		return self::$arrCmsText[$this->objField->cmsfieldtypeid];
	}

	/**
	 * Henter sprogrelevant array af cmstekst til det aktuelle modul
	 * Anvendes når funktionen cmstext() endnu ikke har cached
	 *
	 * @return array 
	 */
	
	private function lookupCmstext(){
		
		$objDB = tuksiDB::getInstance();
		$objPage = tuksiBackend::getInstance();
		
		$arrTexts = array();
		
		// slå tekster op
		$sqlText = "SELECT colname, value_{$objPage->language_prefix} AS val FROM cmstext_fieldtype WHERE ";
		$sqlText.= "cmsfieldtypeid = '{$this->objField->cmsfieldtypeid}'";
				
		$arrRsText = $objDB->fetch($sqlText);
		
		//traverser tekster og gem med 'colname' som key
		if($arrRsText['ok'] && $arrRsText['num_rows'] > 0) {
			foreach($arrRsText['data'] as &$arrText) {
				$arrTexts[$arrText['colname']] = $arrText['val'];
			}
		}
		
		return $arrTexts;		
	}
	
	/**
	 * Tilføjer et givent object (alt) til et error-array der kan tilgås fra savedata
	 *
	 * @param unknown_type $unknown_type (den streng, int, object etc der skal gemmes)
	 */
	
	protected function addError($unknown_type) {
		$this->arrError[] = $unknown_type;
	}
	
	/**
	 * Returner de fejl som er identificeret i getHtml
	 *
	 * @return array
	 */
	
	protected function getErrors(){
		return $this->arrError;
	}
	
	/**
	 * Assigner text.[strname] og error.[strname] til tpl
	 *
	 * @param tuksi_smarty object $tpl
	 * @return boolean succes
	 */
	
	protected function setText(&$tpl){
			
		if (!(is_a($tpl,"tuksiSmarty")))
			return false;	
		$this->checkCmstextCache();
		
		foreach(self::$arrCmsText[$this->objField->cmsfieldtypeid] AS $key=>&$val){
			
			if (in_array($key,$this->arrError)){
				$arrError[$key] = $val;
			}
		}
		
		$tpl->assign("error",$arrError);

		$tpl->assign("text",self::$arrCmsText[$this->objField->cmsfieldtypeid]);
		
		return true;

	}
	
	function debug($mixed, $active = 0) {
		if ($this->debug) {
			if (is_array($mixed)) {
				print "[" . $this->objField->name . "] : " . print_r($mixed, 1) . '<br>';
			} else {
				print "[" . $this->objField->name . "] : " . $mixed . '<br>';
			}
		}
	}

	/**
	 * Laver replace på rowData hvis feltet er sat
	 *
	 * @param string $str
	 * @return string
	 */
	function rowDataReplace($str) {
		if (isset($this->objField->rowData)) {
			$objDB = tuksiDB::getInstance();
			
			foreach ($this->objField->rowData as $key => &$value) {
				if (!is_array($value)) {
					$str = str_replace("#ROWDATA_{$key}#", $objDB->escapeString($value), $str);
				} // if
			} // foreach
		} // if

		return $str;
	} // function rowDataReplace
}

?>
