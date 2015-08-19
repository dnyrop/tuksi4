<?
/**
 * List the contents of a given table
 * According to the tablelayout set
 *
 * value 1: tablename
 * value 2: tablelayoutid
 * value 3: sql
 * value 4: sqlNew
 *  
 * @package tuksiBackendModule
 * @version $Id: eLista.class.php
 * 
 */

class mBackendListb extends mBackendBase {
	
	public $tpl;
	private $tablelayoutid,$tablelayoutid2,$tablename,$tablename2,$sql,$sqlNew,$urlAppend,$idFieldName,$showrowid;
	private $pagesize = 10;
	private $currentPage = 1;
	
	 /**
   *  Sets all the private params and checks for any givin rowid
   * 
   *  @param objModule $objMod	Contains the current moduledata
   * 	
   *  @access public
   */
	
	function __construct(&$objMod){
		parent::__construct($objMod);
		
		$this->tpl = new tuksiSmarty();
		
		//for readabllity
		$this->tablename = $objMod->value1;
		$this->tablename2 = $objMod->value6;
		$this->tablelayoutid = $objMod->value2;
		$this->tablelayoutid2 = $objMod->value7;
		$this->sql = $objMod->value3;
		$this->sql2 = $objMod->value5;
		$this->sqlNew = $objMod->value4;
		$this->name = $objMod->name;
		$this->idFieldName = $objMod->value9;
		
		
		if($_POST->getInt('rowid'))	{
			$this->rowid = $_POST->getInt('rowid');
		} else if($_GET->getInt('rowid')) {
			$this->rowid = $_GET->getInt('rowid');
		} elseif($_POST->getInt('deleteRow_'.$this->objMod->id)) {
			$this->rowid =$_POST->getInt('deleteRow_'.$this->objMod->id);
		} elseif($_POST->getInt('editRow_'.$this->objMod->id)) {
			$this->rowid =$_POST->getInt('editRow_'.$this->objMod->id);
		}
		if($_POST->getInt('showrowid')) {
			$this->showrowid = $_POST->getInt('showrowid');
		} else if($_GET->getInt('showrowid')) {
			$this->showrowid = $_GET->getInt('showrowid');
		}
	}
	
	/**
	 *  getting the html list
	 *
	 * 	@return string 	HTML for the list
	 */
	
	public function getHTML() {
		
		$objPage = tuksiBackend::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$objPage->addBookmark();
		
		
	if($this->userActionIsSet('ADMIN') && $objPage->arrPerms["ADMIN"]) {
			$baseUrl = $objPage->getUrl($objPage->arrConf['link']['tableadmin_treeid']);
			$baseUrl.= "&table=" . $this->tablename;
			$baseUrl.= "&layout=" . $this->tablelayoutid;
			header('Location: ' . $baseUrl);
			die();
		}
		
		
		$objDataList = new tuksiDataList($this->tablename,$this->sql,$this->tablelayoutid,$this->idFieldName);
		
		if($this->userActionIsSet('ADD') && $objPage->arrPerms["ADD"]) {
			if(($rowid = $objDataList->addRow($this->sqlNew)) !== false) {
				$this->rowid = $rowid;
			}
		}
		// You can not delete top table row
		//if($this->rowid > 0 && $objPage->action == "DELETE" && $objPage->arrPerms['DELETE']) { 
		//	$objDataList->setRow($this->rowid);
		//	if($objDataList->deleteRow() !== false) {
		//		$this->rowid = NULL;
		//		$objPage->status($objPage->cmstext('rowdeleted'));
		//	}
		//}
		if($this->showrowid > 0){
			if($this->sql2){
				$sql2 = str_replace("#ID#",$this->showrowid,$this->sql2);
				$objDataSubList = new tuksiDataList($this->tablename2,$sql2,$this->tablelayoutid2,$this->idFieldName);

				if($this->rowid > 0 && $objPage->action == "DELETE" && $objPage->arrPerms['DELETE']) { 
					$objDataSubList->setRow($this->rowid);
					if($objDataSubList->deleteRow() !== false) {
						$this->rowid = NULL;
						$objPage->status($objPage->cmstext('rowdeleted'));
					}
				}
				$arrSubList = $objDataSubList->getRows();
				$this->tpl->assign("subHeaders",$arrSubList['headers']);
				$this->tpl->assign("subData",$arrSubList['data']);
				$this->tpl->assign("showrowid",$this->showrowid);
			}
		}
		if($this->rowid > 0) {
			
			if($this->sql2){
				
				$sql2 = str_replace("#ID#",$this->showrowid,$this->sql2);
				$objDataSubList = new tuksiDataList($this->tablename2,$sql2,$this->tablelayoutid2,$this->idFieldName);
				$arrSubList = $objDataSubList->getRows();
			
				$objDataSubList->setRow($this->rowid);
			
				if($this->userActionIsSet('DELETE') && $objPage->arrPerms["DELETE"]) {
					if($objDataSubList->deleteRow() !== false) {
						$this->rowid = NULL;
					}
				} 
				if($this->userActionIsSet('SAVE') && $objPage->arrPerms["SAVE"]) {
					$objDataSubList->saveRow();
					$objPage->status($objPage->cmstext('pagesaved'));
				}
			
				$returnHtml = $objDataSubList->getRowHtml();;
			
				$this->addButton("BTNBACK", "", "READ","","window.location = '/" . $arrConf['setup']['admin'] . "?treeid=".$objPage->treeid."&tabid=". $objPage->tabid . "';return false;");
				$this->addButton("BTNSAVE","","SAVE");
				
				if ($objPage->PERM['ADD']) {
					$this->addActionButton("BTNCOPY", "", "ADD");
				}
				$this->addButton("BTNADD","","ADD");
				
				// Vis preview knap, hvis der er sat URL op under tree admin.
				if (isset($objPage->ELEMENT['PREVIEW_URL']))
					$this->addButton("BTNPREVIEW", "", "READ");
					
				$this->addActionButton("BTNDELETE", "", "DELETE", "Slet?");
				$this->addActionButton("BTNADMIN","","ADNIN");
			
			}
		} else {
			
			$arrList = $objDataList->getRows();
				
			$this->tpl->assign("headers",$arrList['headers']);
			$this->tpl->assign("nbcols",count($arrList['headers']));
			$this->tpl->assign("data",$arrList['data']);
			$this->tpl->assign("title",	$this->name);
			
			$this->addButton("ADD",$objPage->cmstext('btnadd'),"ADD");
			$this->addButton("ADMIN",$objPage->cmstext('btnadmin'),"ADMIN");
			$this->addButton("RELEASE",$objPage->cmstext('btnrelease'),"RELEASE");
			$this->tpl->assign("perm",$objPage->arrPerms);
			
			
			$returnHtml = parent::getHTML();
		}
		
		return $returnHtml;
	}
}
?>
