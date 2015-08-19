<?

/**
 * 
 * Dropdown with multiple dimensions
 * Fieldvalues:
 * fielvalue1: 1 = With rootid and subfolders 
 *             2 = Only subfolders 
 *             3 = Only rootid 
 *             Eks. 768:1;587:3
 * fieldvalue2: Linktypes: [intern|file|extern]
 * fieldvalue3: allowed filetypes in file upload (pdf is default)
 * 
 * Link typer:
 * cmstree:[ID]
 * mail:[E-mail]
 * file:[sti til fil] (i editorMod er det /downloads/[ID i cmslinkupload]/urlpart)
 * extern:[new;][link]
 *
 * @package tuksiFieldType
 * 
 */

class fieldLink extends field{
	
	static $arrLinkedFields = array();
	private $arrFolder = array();
	private $intSaveCounter = 0;
	private $editorMode = false;
	
	private static $arrSite = array();
	private static $arrLinks = array();
	
	function __construct($objField) {
		parent::field($objField);
		
		// Shows all websites pr default
		//$this->validateFieldvalue("Felttype 1", $this->objField->fieldvalue1, "Skal indeholde root treeid");
		
		$this->debug = false;
		
	}

	function prepareUpload() {
		$objUpload = new stdClass;
		$objUpload->colname 		= $this->objField->colname;
		$objUpload->htmltagname  	= $this->objField->htmltagname."_file";


		$arrLink = self::getLinkType($this->objField->value);

		if ($arrLink['type'] == 'file') {
			$objUpload->value = $arrLink['value'];
		} else {
			$objUpload->value			= '';
		}

		$objUpload->vcolname 		= $this->objField->htmltagname;
		$objUpload->tablename		= $this->objField->tablename;
		$objUpload->rowid 			= $this->objField->rowid;
		$objUpload->id 					= $this->objField->id;
		if ($this->objField->fieldvalue3) {
			$objUpload->fieldvalue1 	= $this->objField->fieldvalue3; 
		} else {
			$objUpload->fieldvalue1 	= 'pdf,jpg,jpeg,doc,xls,docx,xlsx,mst,mstx';
		}

		$objUpload->fieldvalue2		= '';
		$objUpload->fieldvalue4		= '3';

		$this->UploadField = new fieldFileUpload($objUpload,$this->objPage);
		if ($this->editorMode)
			$this->UploadField->editorMode();

	}
	
	/**
	 * editorMode is used by FCK Editor fieldtype. 
	 * FCK needs to get the file returned via JS.
	 * 
	 * @access public
	 * @return void
	 */
	function editorMode() {
		$this->editorMode = true;
	}
	
	/**
	 * Test link: http://test.t4.hjo.meer2-udv.dwarf.dk/thirdparty/fckeditor/editor/plugins/tuksilink/tuksilink.php?link=mailto:hjo@dwarf.dk
	 *
	 * @param string $link
	 * @param string $target
	 */
	function setValue($link, $target) {
		$setTarget = false;
		//print 'link: ' . $link . '<br>';
		
		$arrType = self::getLinkType($link);
		
		if ($arrType['type']) {
			$type = '';
			$value = $link;
			$setTarget = true;
		} else {
			if (preg_match("/^mailto:/", $link, $m)) {
				$value = 'mail:' . str_replace('mailto:', '', $link);
			} else {
				
				if ($link) {
					$setTarget = true;
					$type = 'extern:';
					$value = $link;
				} else {
					$type = 'cmstree:';
				}
				
			}
		}
		
		switch ($target) {
			case('_blank') : $value = $type . 'new;' . $value; break;
			case('_new') : $value = $type . 'new;' . $value; break;
			default:  $value = $type . $value;
		}
		
		$this->objField->value = $value;
	}
	
	function GetFolder($treeid, $curpage, $levels = -1, $currentlevel = 1) {
		
		$this->intSaveCounter++;
		
		if ($curpage) {
			$this->arrFolder[] = array('selected' => ($treeid == $this->currentTreeId), 
																'value' => "cmstree:".$treeid, 
																'name' => $curpage );
		}

		//print $treeid . '-' . $currentlevel .'-' . $levels . '<br>';
		if ($currentlevel > ($levels-1) && $levels > 0)
			return;

		$objDB = tuksiDB::getInstance();
		
	 	$sql = "SELECT id, name FROM cmstree WHERE parentid = '$treeid' AND isdeleted = 0 ORDER by seq";
		$rs = $objDB->fetch($sql);
		
		foreach($rs['data'] as $arrTree) {
			if($curpage != "") {
				$this->GetFolder($arrTree['id'], $curpage . "->" . $arrTree['name'], $levels, $currentlevel++);
			} else {
				$this->GetFolder($arrTree['id'], $arrTree['name'], $levels, $currentlevel++);	
			}
		}
	
	}

	static function getLinkType($link) {
		$type = '';
		$value= '';


		//tjekker for hvilken type der er valgt
		if (preg_match("/^(\/downloads|\/newsletter\/downloads)\/(\d+)\//", $link, $m)) {
			$type = 'file';
			$value = $m[2];
		} elseif (preg_match("/file:/", $link)) {
			$type = 'file';
			$value = str_replace('file:','', $link);
		} elseif (preg_match("/mail:/", $link)) {
			$type = 'mail';
			$value = str_replace('mail:','', $link);
		} elseif (preg_match("/extern:/", $link)) {
			$type = 'extern';
			$value = str_replace('extern:','', $link);
		} elseif (preg_match("/relative:/", $link)) {
			$type = 'relative';
			$value = str_replace('relative:','', $link);
		} elseif(preg_match("/cmstree:/", $link)) {
			$type = 'cmstree';
			$value = str_replace('cmstree:','', $link);			
		} elseif(preg_match('/custom1:(\d+):(\d+)/i', $link, $m)) {
			$type = 'custome';
			$value = $m[2];		
		}

		return array('type' => $type, 'value' => $value);
	}

	static function parseValue($arrValue) { 
		
		$arrData = array();

		switch ($arrValue['type']) {
			case('file') :
				$arrData['file']['file'] = $arrValue['value']; 
				break;
							
			case('mail') :
				$arrData['mail']['email'] = $arrValue['value']; 
				break;
							
			case('cmstree') : 
				if (preg_match("/^new;(.*)/", $arrValue['value'], $m)) {
					$arrData['cmstree']['target'] = 'new';
					$arrData['cmstree']['id'] = (int) $m[1];
				} else {
					$arrData['cmstree']['id'] = (int) $arrValue['value'];
				}
				break;
							
			case('extern') : 
				$value = $arrValue['value'];
				
				if (preg_match("/^new;(.*)/", $value, $m)) {
						$arrData['extern']['target'] = 'new';
						$value = $m[1];
				}

				if (preg_match("/^(https|http|ftp):\/\/(.*)/", $value, $m)) {
					$arrData['extern']['protocol'] = $m[1];
					$arrData['extern']['link'] = $m[2];
				} else {
					$arrData['extern']['protocol'] = 'http';
					$arrData['extern']['link'] = $value;
				}
				break;
							
			case('relative') : 
				$value = $arrValue['value'];
				
				if (preg_match("/^new;(.*)/", $value, $m)) {
						$arrData['relative']['target'] = 'new';
						$value = $m[1];
				}

				if (preg_match("/^(https|http|ftp):\/\/(.*)/", $value, $m)) {
					$arrData['relative']['link'] = $m[2];
				} else {
					$arrData['relative']['link'] = $value;
				}
				break;
		}
		
		return $arrData;
	}
	
	static function ajaxGetValue($link) {
		return self::parseValue(self::getLinkType($link));
	}

	function getHTML() {
		$this->prepareUpload();
		$this->debug($this->objField->value, 0);
		
		$objDB = tuksiDB::getInstance();
		
		if ($arrReturn = $this->checkFieldvalues())
			return $arrReturn;
		
		$tpl = new tuksiSmarty();
		
		$startHtml = parent::getHtmlStart();
		
		$tpl->assign('startHtml',$startHtml);
		$tpl->assign('value', $this->objField->value);
		$tpl->assign('htmltagname',$this->objField->htmltagname);

		$arrSelected = self::getLinkType($this->objField->value);
		
		// Find de type links der skal vieses
		$arrShowLinkTypes = array();
		if(strlen($this->objField->fieldvalue2) == 0) {
			if (empty($arrSelected['type'])) {
				$arrSelected['type'] = 'cmstree';
			}
			$arrShowLinkTypes['cmstree'] = true;
			$arrShowLinkTypes['extern'] = true;
			$arrShowLinkTypes['relative'] = true;
			$arrShowLinkTypes['file'] = true;
			$arrShowLinkTypes['mail'] = true;
		} else {
			$arrShow = explode(',',$this->objField->fieldvalue2);
			foreach ($arrShow as &$value) {
				if ($value == 'intern') {
								$value = 'cmstree';
				}
				if (empty($arrSelected['type'])) {
					$arrSelected['type'] = $value;
				}
				$arrShowLinkTypes[$value] = true;	
			}
		}
		
		$tpl->assign('showlinktype', $arrShowLinkTypes);
		$tpl->assign('selected', $arrSelected);
		
		
		$arrData = self::parseValue($arrSelected);

		if ($arrShowLinkTypes['cmstree']) {
			$this->currentTreeId = (int)$arrData['cmstree']['id'];

      $strRoot = $this->objField->fieldvalue1;
      if (!$strRoot) {
        $objPage = tuksiBackend::getInstance();
        $rootId = tuksiTree::getFrontpageId($objPage->arrPage['treeid']);
        if ($rootId) {
          $strRoot = $rootId . ':1';
        }
      }
			// Show all website pr default
			if (!$strRoot) {
				$arrSites = tuksiConf::getAllSitesConf();
				foreach ($arrSites as $site) {
					$value1[] = $site['rootid'] . ':1';
				}
				$strRoot = join(';', $value1);
			}
		
			$arrRoot = explode(';', $strRoot);
			$this->arrFolder[] = array('value' => '', 'name' => 'choose_page');
		
			foreach($arrRoot as &$setup) {
				$arrSetup= explode(":", $setup);
				$rootTreeId = $arrSetup[0];
				$setupId = $arrSetup[1];


				switch ($setupId) {
					case (2) : 
									// show parent and sub pages
									//$arrTree = $objDB->fetchRow('cmstree', $rootTreeId, 'assoc', "id, name");
									$this->GetFolder($rootTreeId, '');
											
									break;
					case (3) : 
									
									$arrTree = $objDB->fetchRow('cmstree', $rootTreeId, 'assoc', "id, name");
									$this->GetFolder($rootTreeId, $arrTree['name'], 1);
									
									break;
					default: 
									// show parent and sub pages
									$arrTree = $objDB->fetchRow('cmstree', $rootTreeId, 'assoc', "id, name");
									$this->GetFolder($rootTreeId, $arrTree['name']);
								
									break;
				}
			}
				
			$arrData['cmstree']['pages']  = $this->arrFolder;
		}
		
		// Load fieldFile fieldtype
		if ($arrShowLinkTypes['file']) {
			$arrUpload = $this->UploadField->getHTML();
			$arrData['file']['html'] = $arrUpload['html'];
		}

		$tpl->assign('value', $arrData);
		
		return parent::returnHtml($this->objField->name,$tpl->fetch('fieldtypes/fieldLink.tpl'));
	}	
	
	function saveData() {
		$objDB = tuksiDB::getInstance();

		//$this->prepareUpload();
		$linkType = $_POST->getStr($this->objField->htmltagname."_type");
		$this->debug("Linktype to save: " . $linkType, 0);

		switch ($_POST->getInt('action')) {
			case (1) : break;

			default: 
					// Ingen action så nu kan der gemmes.
					$value = $linkType . ':';
					switch ($linkType) {
						case('extern'): 
						
							$url = $_POST->getStr($this->objField->htmltagname."_extern_link");
							$url = preg_replace("/(.*):\/\//", '', $url);

							if ($url) {
								
								// Kun hvis link skal vises i nyt vindue sættes new; foran linket.
								if ($_POST->getStr($this->objField->htmltagname."_extern_target") == '_blank') {
									$value.= 'new;';
								}
								if ($_POST->getStr($this->objField->htmltagname."_extern_protocol")) {
									$value.=  $_POST->getStr($this->objField->htmltagname."_extern_protocol") . '://';
								}
								$value.=  $url;
							} else {
								$value = '';
							}
							
							break;
							
						case('relative'): 
						
							$url = $_POST->getStr($this->objField->htmltagname."_relative_link");
							$url = preg_replace("/(.*):\/\//", '', $url);

							if ($url) {
								
								// Kun hvis link skal vises i nyt vindue sættes new; foran linket.
								if ($_POST->getStr($this->objField->htmltagname."_relative_target") == '_blank') {
									$value.= 'new;';
								}
								$value.=  $url;
							} else {
								$value = '';
							}
							
							break;
							
						case('cmstree'): 
							// Kun hvis link skal vises i nyt vindue sættes new; foran linket.
							if ($_POST->getStr($this->objField->htmltagname."_cmstree_target") == '_blank') {
								$value.= 'new;';
							}
							$value.= $_POST->getStr($this->objField->htmltagname."_cmstree");
							break;
						case('mail'): 
						
							$value.= $_POST->getStr($this->objField->htmltagname."_mail_input");
							
							break;

						case('file'):
							$this->prepareUpload();
							$this->UploadField->saveData();
							$value.= $this->UploadField->objField->value;

					}
				
					
					$sql = $this->objField->colname . " = '" . $objDB->escapestring($value) . "'";
					
					break;

		}
		$this->objField->value = $value;
		//print $sql . '<br>';

		return $sql;
	}
	
	function copyData($rowid_to) {
		$arrSelected = self::getLinkType($this->objField->value);
		//print_r($arrSelected);
		
		// Only file link type have speciel copy 
		if ($arrSelected['type'] == 'file' && $arrSelected['value']) {

			// If numeric it is linked to media lib should not be copied.
			// Same file should be used.
			if (is_numeric($arrSelected['value'])) {
				// Value already copied in main row function 
			} else {
				$this->prepareUpload();
				$this->UploadField->copyData($toid);
			}

		}
	}
	
	function onEndCopy_DEL(){
		
		$arrCopiedItemsFromTo = tuksi_tree::getCopiedItemsFromTo();
		
		if (is_array(self::$arrLinkedFields)) {
			foreach(self::$arrLinkedFields as &$arrItem) {
				if ($arrItem['isMoved']) continue;
				if (key_exists($arrItem['linktreeid'],$arrCopiedItemsFromTo)){
					$sql = "UPDATE pg_content SET {$arrItem['objField']->colname} = 'cmstree:".$arrCopiedItemsFromTo[$arrItem['linktreeid']]."' WHERE id = '{$arrItem['toid']}'";
					$objDB = tuksiDB::getInstance();
					$objDB->write($sql);
					$arrItem['isMoved'] = true;
				} 
			}
		}	
	}
	
	function getFrontendValue() {
		
		if ($this->objField->value) {
			$value = self::makeUrl($this->objField->value);
		} else {
			$value = '';
		}
		
		return $value;
	}
	
	
	/**
	 * Used for converting cmstreeid to urlpart
	 *
	 * @param int $link 
	 * @return unknown
	 */
	static function makeUrl($link, $fullUrl = false) {
		
		$systemType = tuksiIni::$arrIni['setup']['system'];
		if(isset(self::$arrLinks[$systemType . $link])) {
			return self::$arrLinks[$systemType . $link];
		} else {
			$arrLink = self::getLinkType($link);

			$target = '';
			$type = '';
			$url = '';
			$name = '';
			switch ($arrLink['type']) {
				case('cmstree') :
				
				$objDB = tuksiDB::getInstance();
				$arrConf = tuksiConf::getConf();
				
				$arrData = self::parseValue($arrLink);
				if (isset($arrData['cmstree']['target'])) {
					$target = ($arrData['cmstree']['target'] == 'new') ? '_blank' : '';
				}
				$treeid = $arrData['cmstree']['id'];
				$sqlUrl = "SELECT pg_menu_name, pg_urlpart_full, cmssitelangid FROM cmstree{$arrConf['setup']['tableext']} WHERE id = '{$treeid}'";
				
				$arrRsUrl = $objDB->fetchItem($sqlUrl, array('expire' => 180));
	
				if ($arrRsUrl['ok'] && $arrRsUrl['num_rows'] > 0) {

					$url = '';
					if ($fullUrl || $arrConf['site']['cmssitelangid'] != $arrRsUrl['data']['cmssitelangid'] || $systemType == 'newsletter') {
						$arrConf = tuksiConf::getPageConf($treeid);
						
						$url = 'http://' . $arrConf['url_site'];
						$target = "_blank";
					}
					
					$url.= '/' . $arrRsUrl['data']['pg_urlpart_full'];
					
					if(preg_match('/nyhedsbrev\/([0-9]+)/i',$url)) {
						$target = "_blank";
					}
					$type = 'link';
					$name = $arrRsUrl['data']['pg_menu_name'];
				}
				break;
			
			case('extern') : 

				$arrData = self::parseValue($arrLink);

				$url = $arrData['extern']['protocol'] . '://' . $arrData['extern']['link'];
		
				$target = '';
				if (!empty($arrData['extern']['target'])) {
					$target = ($arrData['extern']['target'] == 'new') ? '_blank' : '';
				}   

				$type = 'link';
				
				break;
				
			case('relative') : 
			
				$arrConf = tuksiConf::getConf();
				$arrData = self::parseValue($arrLink);
				
				$url = 'http://' . $arrConf['site']['url_site'] . '/' . $arrData['relative']['link'];
		
				$target = '';
				if (!empty($arrData['relative']['target'])) {
					$target = ($arrData['relative']['target'] == 'new') ? '_blank' : '';
				}   

				$type = 'link';
				
				break;
			
			case('file') : 
				if (is_numeric($arrLink['value'])) {
					$url = '';

					// If newsletter context we need 'newsletter/' on all file, so we can get files without live ext
					$objPage = tuksiBackend::getInstance();
					if ($objPage->arrTree['cmscontextid'] == 3) {
						$url = '/newsletter';
					} 
					$url.= '/downloads/' . $arrLink['value'] . '/';
					
				} else {
					$url = str_replace('file:','',$link);
					$url = '/uploads/' . $url;
					$target = '_blank';
					
					if(substr($url,-4,4) == '.pdf') {
						$type = 'pdf';
					} else {
						$type = 'file';
					}
				}
				
				break;

			case('custom1') : 
			//} elseif (preg_match('/custom1:(\d+):(\d+)/i',$link, $m)) {
			//	$id = $m[2];
			//	$arrUrl = fieldLink::makeUrl('cmstree:'.$m[1]);
			//	$url = $arrUrl['url']."#id=".$id;
				break;

			default:
				$url = "";
				$target = '';
			}

			self::$arrLinks[$systemType . $link] = array('url' => $url, 'target' => $target, 'type' => $type, 'name' => $name);

			return self::$arrLinks[$systemType . $link];
		}
	}
	
}	
?>
