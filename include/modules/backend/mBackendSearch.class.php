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

class mBackendSearch extends mBackendBase {
	
	public $tpl;
	private $tablelayoutid,$tablename,$sql,$sqlNew,$useNavigation,$pagesizes,$searchFields,$useSearch,$urlAppend,$idFieldName;
	private $objHook = false;
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
/*	
		//for readabllity
		$this->tablename = $objMod->value1;
		$this->tablelayoutid = $objMod->value2;
		$this->sql = $objMod->value3;
		$this->sqlNew = $objMod->value4;
		$this->name = $objMod->name;
		$this->pagesizes = $objMod->value5;
		$this->useNavigation = $objMod->value6;
		
		$this->useSearch = $objMod->value8;
		$this->idFieldName = $objMod->value9;
		$this->customUrl = $objMod->link;
		$this->useSeq = $objMod->value10;
		
		$this->rowid = 0;
		if($_POST->getInt('rowid'))	
			$this->rowid = $_POST->getInt('rowid');
		else if($_GET->getInt('rowid'))
			$this->rowid = $_GET->getInt('rowid');
		elseif($_POST->getInt('editRow_'.$this->objMod->id))
			$this->rowid =$_POST->getInt('editRow_'.$this->objMod->id);
		elseif($_POST->getInt('deleteRow_'.$this->objMod->id))
			$this->rowid =$_POST->getInt('deleteRow_'.$this->objMod->id);
			
			
		if($_POST->getInt('page')) {
			$this->currentPage = $_POST->getInt('page');
		} else if($_GET->getInt('page'))	{
			$this->currentPage = $_GET->getInt('page');
		}
				

		if($_POST->getInt('changePagesize')) {
			$this->pagesize = $_POST->getInt('changePagesize');
			$this->currentPage = 1;
		}else if($_POST->getInt('pagesize')) {
			$this->pagesize = $_POST->getInt('pagesize');
		} else if($_GET->getInt('pagesize')) {
			$this->pagesize = $_GET->getInt('pagesize');
		} else {
			$this->pagesize = 10;
		}
		
		if($_POST->getStr('query')) {
			$this->query = $_POST->getStr('query');
		} elseif ($_GET->getStr('query')) {
			$this->query = $_GET->getStr('query');
		}
		
		$this->addHookValue('rowid',$this->rowid);
*/
	}
	
	/**
	 *  getting the html list
	 *
	 * 	@return string 	HTML for the list
	 */
	
	public function getHTML() {
		
		$objPage = tuksiBackend::getInstance();
		$arrConf = tuksiConf::getConf();
		$objDB = tuksiDB::getInstance();
		
		$this->tpl->assign('showsearch',true);
		
		$strSearch = $_POST->getStr("strSearch");
			// print $strSearch;
		if($strSearch){
			if(is_numeric($strSearch)){			
				if($objPage->arrPerms["ADMIN"]){
					$sqlId = "SELECT id, name, pg_urlpart_full as url FROM cmstree WHERE isdeleted = 0 AND id = ".$strSearch;
					$arrResultId = $objDB->fetch($sqlId);
					$this->tpl->assign('resultId',$arrResultId['data']);
				}
			}else{
				// cmsContent 9
				if($objPage->arrPerms["ADMIN"]){
					$sqlTree = "SELECT t.id as id, t.name as name, t.pg_urlpart_full as url FROM cmstree t WHERE t.isdeleted = 0 AND ( t.name LIKE '%".$strSearch."%' OR t.pg_menu_name LIKE '%".$strSearch."%') ";
				}else{
					$sqlTree = "SELECT t.id as id, t.name as name, t.pg_urlpart_full as url FROM cmstree t, cmsperm p WHERE t.isdeleted = 0 AND ( t.name LIKE '%".$strSearch."%' OR t.pg_menu_name LIKE '%".$strSearch."%') AND t.id = p.rowid AND p.cmsgroupid = 9 AND pread = 1";
				}	
				$arrResultTree = $objDB->fetch($sqlTree);
				if($arrResultTree['num_rows'] > 0){
					$this->tpl->assign('resultTree',$arrResultTree['data']);
				}
				if($objPage->arrPerms["ADMIN"]){
					$this->tpl->assign('showModule',1);
					
					$sqlModule = "SELECT c.cmstreeid as id, t.name as name, t.pg_urlpart_full as url, m.classname as classname FROM pg_module m, pg_content c LEFT JOIN cmstree t ON (t.id = c.cmstreeid) WHERE ( m.name LIKE '%".$strSearch."%' OR m.classname LIKE '%".$strSearch."%' ) AND m.id = c.pg_moduleid AND isdeleted = 0 GROUP BY c.cmstreeid";
					$arrResultModule = $objDB->fetch($sqlModule);
					if($arrResultModule['num_rows'] > 0){
						$this->tpl->assign('resultModule',$arrResultModule['data']);
					}
					
				}
				
			}
		/*
			print $sqlId;
			print "<br><br>";
			print $sqlTree;
			print "<br><br>";
			print $sqlModule;
			print "<br><br>";
		*/
		}
		$this->tpl->assign('strSearch',$strSearch);
		
		
/*		
		if($this->userActionIsSet('ADMIN') && $objPage->arrPerms["ADMIN"]) {

			$baseUrl = $objPage->getUrl($arrConf['link']['tableadmin_treeid']);
			if(($pos = strpos($this->tablename,".")) !== false) {
				$arr = explode("\.",$this->tablename);
				$tablename = $arr[1];
				$db = $arr[0];
			} else {
				$db = "";
				$tablename = $this->tablename;
			}
			
			$baseUrl.= "&db=" . $db;
			$baseUrl.= "&table=" . $tablename;
			$baseUrl.= "&layout=" . $this->tablelayoutid;

			header('Location: ' . $baseUrl);
			die();
		}
		
		
		
		$objDataList = new tuksiDataList($this->tablename,$this->sql,$this->tablelayoutid,$this->idFieldName);
		
		if($this->customUrl) {
			$objDataList->setCustomUrl($this->customUrl);
		}
		
		$arrError = $objDataList->getErrors();
		if (count($arrError)) {
			return join("<br />",$arrError);
		}
		
		if($this->userActionIsSet('ADD') && $objPage->arrPerms["ADD"]) {
			$addRow = true;
			if(!$this->hookBefore('add')) {
					$addRow = false;
			} 
			if($addRow) {
				if(($rowid = $objDataList->addRow($this->sqlNew)) !== false) {
					$this->rowid = $rowid;
					$this->hookAfter('add');
				}
			}
		}
		
		if($this->rowid > 0 && $objPage->action == "DELETE" && $objPage->arrPerms['DELETE']) { 
			$objDataList->setRow($this->rowid);
			$deleteRow = true;
			if(!$this->hookBefore('delete')) {
					$deleteRow = false;
			}
			if($deleteRow) {
				if($objDataList->deleteRow() !== false) {
					$this->rowid = NULL;
					$objPage->status($objPage->cmstext('rowdeleted'));
					$this->hookAfter('delete');
				}
			}
			
		}
		if ($this->rowid > 0) {
			
			$objDataList->setRow($this->rowid);
			
			if($this->userActionIsSet('DELETE') && $objPage->arrPerms["DELETE"]) {
				$deleteRow = true;
				if(!$this->hookBefore('delete')) {
					$deleteRow = false;
				}
				if($deleteRow) {
					if($objDataList->deleteRow() !== false) {
						$this->rowid = NULL;
						$this->hookAfter('delete');
					}
				}
			} 
			
			if($this->userActionIsSet('SAVE') && $objPage->arrPerms["SAVE"]) {
				
				$saveRow = true;
				if(!$this->hookBefore('save')) {
						$saveRow = false;
				}
				
				if($saveRow) {
					$objDataList->saveRow();
					$objPage->status($objPage->cmstext('pagesaved'));		
					$this->hookAfter('save');
				}
			}
			
			$returnHtml = $objDataList->getRowHtml();;
			
			$this->addButton("BTNBACK", "", "READ");
			$this->addButton("BTNSAVE","","SAVE");
			
			if ($objPage->arrPerms['ADD']) {
				// $this->addButton("BTNCOPY", "", "ADD");
			}
			$this->addButton("BTNADD","","ADD");
			
			// Vis preview knap, hvis der er sat URL op under tree admin.
			if (isset($objPage->ELEMENT['PREVIEW_URL']))
				$this->addButton("BTNPREVIEW", "", "READ");
				
			$this->addButton("BTNDELETE", "", "DELETE", "Slet?");
			
		} else {

			if($this->userActionIsSet('RELEASE') && $objPage->arrPerms["RELEASE"]) {
				$arrReturn = $objDataList->releaseTable();
				if ($arrReturn['ok']) {
					$objPage->status($objPage->cmstext('table_released'));
				} else {
					$objPage->status($objPage->cmstext('table_release_error'));
				}
			}
			
			$objPage->addBookmark();
			
			if($this->useNavigation) {
			
				$arrNavigation = array();
				
				if($this->pagesizes) {
					$arrNavigation['pagesizes'] = explode(",",$this->pagesizes);
				} else {
					$arrNavigation['pagesizes'] = array(10,25,50,75,100);
				}

				if(!in_array($this->pagesize,$arrNavigation['pagesizes'])) {
					$this->pagesize = $arrNavigation['pagesizes'][0];
				}
				
				$arrList = $objDataList->getRows($this->pagesize,$this->currentPage);
				
				//get current page from data list as it might be wrong
				$this->currentPage = $objDataList->getCurrentPage();
				
				$arrNav = $objDataList->getNavigation();
				
				if($arrNav['next']) {
					$arrNavigation['next'] = array('url' => $objPage->getUrl($objPage->treeid,$objPage->tabid) . "&page=".$arrNav['next']."&pagesize=".$this->pagesize . 	$this->urlAppend);
				}
				if($arrNav['prev']) {
					$arrNavigation['previous'] = array('url' => $objPage->getUrl($objPage->treeid,$objPage->tabid) . "&page=".$arrNav['prev']."&pagesize=".$this->pagesize . 	$this->urlAppend);
				}
				
				$arrPages = $objDataList->getPages(9);
				
				for($i = $arrPages['start'];$i <=$arrPages['stop'];$i++) {
					$active = false;
					
					if($i == $this->currentPage) {
						$active = true;
					}
					$arrNavigation['pages'][] = array('name' => $i,'url' =>  $objPage->getUrl($objPage->treeid,$objPage->tabid) . "&page=".$i."&pagesize=".$this->pagesize . $this->urlAppend,'isactive' => $active);
				}
				
				$arrNavigation['pagesize'] = $this->pagesize;
				
				$this->tpl->assign('nav',$arrNavigation);
			
			} // End use navagation 
			 else {
					$arrList = $objDataList->getRows();
			}
			//print_r($arrList);
			
			$this->tpl->assign("headers",$arrList['headers']);
			$this->tpl->assign("data",$arrList['data']);
			$this->tpl->assign("title",	$this->name);
			$this->tpl->assign("useseq",	$this->useSeq);
			

			
			
			
		}
*/

		$returnHtml = parent::getHTML();
		
		$this->tpl->assign("perm",$objPage->arrPerms);

		//$this->addButton("ADD",$objPage->cmstext('btnadd'),"ADD");
		//$this->addButton("RELEASE",$objPage->cmstext('btnrelease'),"RELEASE");
		$this->addButton("ADMIN",$objPage->cmstext('btnadmin'),"ADMIN");

		return $returnHtml;
	}
}
?>
