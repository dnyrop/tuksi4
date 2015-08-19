<?php

/**
 * Grundklasse til alle module i frontenden
 *
 * @uses tuksiDebug
 * @uses tuksiDB
 * 
 * @package tuksiFrontend
 * 
 */
class mFrontendBase {
	
	private $cachename;
	
	/**
	 * class for the content area returns with modules
	 *
	 * @param object $objMod Module object
	 * @return module_base
	 */
  	function __construct (&$objMod) {
		$this->objMod 	= &$objMod;
  	}
  	
  	/**
  	 * Use this function to activate caching.
	 * 
	 * $this->cachename is automaticaly set via $cachename option
  	 *
  	 * @param string $cachename
  	 */
  	function checkCache($cachename = '') {
  	
		if (!empty($this->objMod->cache_timeout)) {
			if (!$cachename) {
				$cachename = $this->objMod->id;
			}
			$this->cachename = 'module_' . $cachename;
			if ($html = tuksiCache::get($this->cachename)) {
				return $html;
			}
		}
		//print $this->cachename . '<br>';
		//print $this->objMod->cache_timeout;
		return false;
  	}

	/**
	 * Laver en instance af modul
	 *
	 *
	 * @param object Modul that should be made
	 * @return object Instance af modul
	 */
	static function getInstance($objMod) {

		if (preg_match("/.tpl$/", $objMod->classname, $m)) {
			$objMod->template = $objMod->classname;
			$objMod->classname = "mFrontendStandard";
		} else {
			$objMod->template = $objMod->classname . ".tpl";
		}

		tuksiDebug::log('Loading module: ' . $objMod->classname);
		
		$filename = dirname(__FILE__) . "/" .$objMod->classname . ".class.php";
		if (file_exists($filename)) {
			$objModule = new $objMod->classname($objMod);
			$objModule->class = $objMod->classname;
			$objModule->template = $objMod->template;
			
			return $objModule;
		} else {
			return false;
		}
	}

	/**
	 * Auto insert field from Tuksi fieldtypes
	 * cmsfielditem.speciel_frontend needs to be 1 to use getFrontendValue in class.
	 *
	 * @return array Module values
	 */
	public function addStandardFields() {
		
		$objDB = tuksiDB::getInstance();
		$arrConf = tuksiConf::getConf();
		
		$arrFields = array();
		$sql = "SELECT fi.*, ft.classname ";
		$sql.= "FROM cmsfielditem{$arrConf['setup']['tableext']} fi, cmsfieldtype ft  ";
		$sql.= "WHERE fi.itemtype = 'pg' AND fi.cmsfieldtypeid = ft.id AND fi.relationid = '{$this->objMod->pg_moduleid}'";
		$sql.= "AND ft.speciel_frontend = 1 ";

		$arrReturn = $objDB->fetch($sql, array('type' => 'object', 'expire' => 360));
		
		foreach ($arrReturn['data'] as $arrFieldItem) {
        	$arrFieldTypes[$arrFieldItem->colname] = $arrFieldItem;
    	}
        
		foreach ($this->objMod as $key => $value) {
			
			if (isset($arrFieldTypes[$key]->cmsfieldtypeid)) {
				
				$classname = $arrFieldTypes[$key]->classname;
				$arrFieldTypes[$key]->value = $value;
				$arrFieldTypes[$key]->pg_moduleid = $this->objMod->id;
				$arrFieldTypes[$key]->rowid = $this->objMod->id;
				
				$objFieldType = new $classname($arrFieldTypes[$key]);
				
				$arrFields[$key] = $objFieldType->getFrontendValue();
				
				//print_r($arrFields[$key]);
			} else {
				$arrFields[$key] = $value;
			}
			
		/*switch ($arrFieldTypes[$key]['cmsfieldtypeid']) {
			case(61): // Table builder
					
						list($arrHead, $arrData) = fieldtablebuilder::makeTable($arrFieldTypes[$key]['id'],$this->objMod->id);
						//$arrLink = fieldTableBuilder::makeTable($value);
							
						$arrFields[$key]['tableHead'] = $arrHead;
						$arrFields[$key]['tableData'] = $arrData;
						
						break;
			case(26): // Picture Cropper
						if ($value) {
                      	$arrImg = explode(";",$value);
                        $arrFields[$key] = "/uploads/" . $arrImg[0];
                      }
                      break;
			case(24) : // Multiple Elements
						$sql = $arrFieldTypes[$key]['fieldvalue1'];
						$sql = str_replace("#ROWID#", $this->objMod->id, $sql);
												
						$arrReturn = $objDB->fetch($sql);
						
						$arrFields[$key] = $arrReturn['data'];
						
						break;
			
		}*/
			
		}
		$arrFields['id'] = $this->objMod->id;
		
	 	$this->tpl->assign("module", $arrFields);
	 	
	 	return $arrFields;

	} // End addStandardFields();
	
	public function getHtml() {
		$template = "modules/frontend/" . $this->objMod->template;
		
		if (file_exists(dirname(__FILE__) . "/../../../templates/" . $template)) {
			$this->addStandardFields();
			$html = $this->tpl->fetch($template, $this->objMod->id);
		} else {
			$html = "HTML skabelon kunne ikke findes: {$template}<br>";
		}
		
		if (isset($this->cachename) && $this->objMod->cache_timeout > 0) {
			tuksiCache::set($this->cachename, $html, $this->objMod->cache_timeout);
			
		}
		$htmlButton = '';
		return $html . $htmlButton;
	}


}
?>
