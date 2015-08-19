<?

/**
 * Class to handle the settings of a given page in tree admin
 *
 * @todo PHP doc
 * @package tuksiBackend
 */

class tuksiPageSetttings {
	
	public function __construct($treeid){
		
		$this->treeid = $treeid;
		
		$objDB = tuksiDB::getInstance();	
		
		$this->arrTree	= $objDB->fetchRow("cmstree", $this->treeid); //cmstree objektet for treeid
		$this->objTableView = new tuksiTableview($this->treeid, "cmstree");
	
	}	
	
	public function getHTML() {
		
		$this->setData();
			
		//get html
		$arrHtml = $this->objTableView->getFields();
		
		return $arrHtml;
			
	}
	
	private function setData(){
		
		$objDB = tuksiDB::getInstance();	
		
		//set the options
		$this->setSettings();
		
		//overfører værdierne for det aktuelle tab til tableview
		$this->arrTree = $objDB->fetchRow("cmstree", $this->treeid); //cmstree objektet for treeid
		
		$this->objTableView->addData($this->arrTree);
	
	}
	
	private function setSettings() {
		
		$objPage = tuksiBackend::getInstance();
		
		//set id
		$this->objTableView->setField("fieldTextInput", 
										ucfirst($objPage->cmsText("treeid")) . ":",
										"id","","","2");
		
		$arrNodes = $this->getNodes(0);
		
		//set parent select
		$this->objTableView->setField("fieldSelectDefine", 
										ucfirst($objPage->cmsText("parent")) . ":",
										"parentid",
										$arrNodes);
																	
		//set name
		$this->objTableView->setField("fieldTextSuggest", 
										ucfirst($objPage->cmsText("name")) . ":",
										"name");
										
		//set show in menu
		$this->objTableView->setField("fieldCheckbox", 
										ucfirst($objPage->cmsText("show_inmenu")) . ":",
										"show_inmenu");
		
		//set seq															
		$this->objTableView->setField("fieldSelectFromTable", 
										ucfirst($objPage->cmsText("nodetype")) . ":",
										"cmstreetypeid",
										"SELECT * FROM cmstreetype order by id");				

		//set seq															
		$this->objTableView->setField("fieldTextInput", 
																	ucfirst($objPage->cmsText("value1")) . ":",
																	"value1");																							
		//set seq															
		$this->objTableView->setField("fieldTextInput", 
																	ucfirst($objPage->cmsText("value2")) . ":",
																	"value2");					
		//set seq															
		$this->objTableView->setField("fieldTextInput", 
																	ucfirst($objPage->cmsText("value3")) . ":",
																	"value3");					
		//set seq															
		$this->objTableView->setField("fieldTextInput", 
																	ucfirst($objPage->cmsText("seq")) . ":",
																	"seq");				
																	
		//set context															
		$this->objTableView->setField("fieldSelectFromTable", 
																	ucfirst($objPage->cmsText("context")) . ":",
																	"cmscontextid",
																	"SELECT * FROM cmscontext");			
																	
		//set site													
		$this->objTableView->setField("fieldSelectFromTable", 
																	ucfirst($objPage->cmsText("site")) . ":",
																	"cmssitelangid",
																	"SELECT l.id, concat(s.name,' ',l.name) as name FROM cmssite s,cmssitelang l WHERE l.cmssiteid = s.id ORDER BY name", $objPage->cmsText("choose_website"));	
	}
	
	public function save() {
		
		$this->setData();
		
		$arrTreePreSave = $this->arrTree;
		
		$r = $this->objTableView->saveFields();
		
		$this->setData();
		
		//find out which data has been changed		
		$this->arrChanged = array();
		
		foreach($arrTreePreSave as $field => $value) {
			if($value != $this->arrTree[$field]) {
				$this->arrChanged[$field] = array("old" => $value, "new" => $this->arrTree[$field]);
			}
		}
	}
	
	/**
	 * Henter nodestruktur og lave select options.
	 *
	 * @param int $treeid
	 * @param string $curpage
	 * @param int $selectedid
	 * @return array
	 */
	function getNodes($treeid, $curpage = "",$level = 0,$arrUsed = array()) {
		
		$objDB = tuksiDB::getInstance();
		$objText = tuksiText::getInstance('table');
		
		$arrNodes = array();
		
		$level++;
		
		$sqlNode = "SELECT t.parentid, t.id, t.name ";
		$sqlNode.= ", (SELECT count(*) FROM cmstree tt WHERE tt.parentid = t.id AND tt.isdeleted = 0) as haschild ";
		$sqlNode.= "FROM cmstree t ";
		$sqlNode.= "WHERE t.parentid = '$treeid' AND isdeleted = 0 ";
		$sqlNode.= "ORDER BY t.seq, t.name";

		$arrRsNode = $objDB->fetch($sqlNode);
		if($arrRsNode['ok'] && $arrRsNode['num_rows'] > 0) {
			foreach ($arrRsNode['data'] as $arrNode) {
				if(!$arrUsed[$arrNode['id']]) {
					$arrUsed[$arrNode['id']] = true;
					$name = $curpage != "/" ? $curpage . "/" . $arrNode['name'] : $arrNode['name'];
					$sel = $this->currentNodeId == $arrNode['id'] ? true : false;
					$arrNodes[] = array('name' => $name,'value' => $arrNode['id'],'sel' => $sel);
					if ($arrNode['haschild']) {
						$arrChildNodes = $this->getNodes($arrNode['id'], $name,$level,$arrUsed);
						$arrNodes = array_merge($arrNodes,$arrChildNodes);
					}
				}
			}
		} 
		return $arrNodes;
	}
	
}
?>
