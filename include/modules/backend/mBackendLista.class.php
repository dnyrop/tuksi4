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

class mBackendLista extends mBackendBase {
	
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
		
		//for readabllity
		$this->tablename = $objMod->value1;
		$this->tablelayoutid = $objMod->value2;
		$this->sql = $this->pageDataReplace($objMod->value3);
		$this->sqlNew = $this->pageDataReplace($objMod->value4);
		$this->name = $objMod->name;
		$this->pagesizes = $objMod->value5;
		$this->useNavigation = $objMod->value6;
		$this->searchFields = $objMod->value7;
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
		elseif($objMod->value11)
			$this->rowid = (int) $objMod->value11;
			
			
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
		
		if ($_POST->getStr('query') !== null) {
			$this->query = $_POST->getStr('query');
		} else if ($_GET->getStr('query') !== null) {
			$this->query = $_GET->getStr('query');
		}
		
		if (!isset($this->query) && isset($_SESSION['lista_' . $this->tablename])) {
			$this->query = $_SESSION['lista_' . $this->tablename];
		} else {
			$_SESSION['lista_' . $this->tablename] = $this->query;
		}
		
		$this->addHookValue('rowid',$this->rowid);
	}
	
	/**
	 *  getting the html list
	 *
	 * 	@return string 	HTML for the list
	 */
	
	public function getHTML() {
		
		$objPage = tuksiBackend::getInstance();
		$arrConf = tuksiConf::getConf();
		
		if($this->userActionIsSet('ADMIN') && $objPage->arrPerms["ADMIN"]) {

			$baseUrl = $objPage->getUrl($arrConf['link']['tableadmin_treeid']);
			if(($pos = strpos($this->tablename,".")) !== false) {
				$arr = explode(".",$this->tablename);
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
		
		if($this->useSearch) {
			
			if($this->query){
				
				$arrFields = explode(",",$this->searchFields);
				if(count($arrFields) > 0) {
				
					$objDB = tuksiDB::getInstance();
					
					$sqlOrder = "";
					
					if(stripos($this->sql,"ORDER BY")) {
						$arr = preg_split("/order by/i",$this->sql);
						$sqlOrder = $arr[1];
						$this->sql = $arr[0];
					}
					
					if(stripos($this->sql,"where") === false) {
						$this->sql.= " WHERE ";
					} else {
						$this->sql.= " AND ";
					}
					
					foreach ($arrFields as $fieldname) {
						$arrSqlSearch[] = $fieldname . " LIKE '%".$objDB->realEscapeString($this->query)."%' ";
					}
					
					$this->sql.= "( ". join(" OR ",$arrSqlSearch) .")";
					
					if(!empty($sqlOrder)) {
						$this->sql.= " ORDER BY " . $sqlOrder;
					}
				}
				$this->urlAppend = "&query=" . urlencode($this->query);
			}
			$this->tpl->assign('query',$this->query);
			$this->tpl->assign('showsearch',true);
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
				$posFrom = strripos($this->sql, "FROM");
				$posWhere = strripos($this->sql, "WHERE");
				$posOrder = strripos($this->sql, "ORDER BY");

				$strWhere = '';
				if ($posWhere && $posWhere > $posFrom) {
					if ($posOrder) {
						$strWhere = substr($this->sql, $posWhere, $posOrder - $posWhere);
					} else {
						$strWhere = substr($this->sql, $posWhere);
					}
				}
				
				$arrReturn = $objDataList->releaseTable($strWhere);
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
			
			$this->addButton("ADD",$objPage->cmstext('btnadd'),"ADD");
			$this->addButton("RELEASE",$objPage->cmstext('btnrelease'),"RELEASE");
			$this->tpl->assign("perm",$objPage->arrPerms);
			
			
			$returnHtml = parent::getHTML();
		}
		
		$this->addButton("ADMIN",$objPage->cmstext('btnadmin'),"ADMIN");

		return $returnHtml;
	}

	private function pageDataReplace($str) {

		$objPage = tuksiBackend::getInstance();

		$str = str_replace("#TREEID#", (int) $objPage->arrTree['id'], $str);
		$str = str_replace("#CMSSITELANGID#", (int) $objPage->arrTree['cmssitelangid'], $str);
		$str = str_replace("#CMSSITEID#", (int) $objPage->arrTree['cmssiteid'], $str);
		
		return $str;
	} // function pageDataReplace
}
?>
