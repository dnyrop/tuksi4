<?

/**
 * Get all sub
 *
 * @uses tuksiDebug
 * @uses tuksiSmarty
 * 
 * @package tuksiFrontend
 * 
 */

class mFrontendExpandList extends mFrontendBase {

	//return the html for the module
	function __construct(&$objMod){

		parent::__construct($objMod);

		$this->tpl = new tuksiSmarty();

	}
	/**
	 * Henter HTML
	 */

	function getHTML() {
		
		$objDB = tuksiDB::getInstance();
		
		$arrContent = $this->setContent();
		
		$this->tpl->assign("arrList",$arrContent);
		
		return parent::getHTML();
	}
	
	//set function for html in the content area
    function setContent($areaid = 0){

        $objDB = tuksiDB::getInstance();
				$arrConf = tuksiConf::getConf();
		
        $arrModule = array();

        // Hent moduler i valgte kolonne
        $sqlMod = "SELECT r.*, m.classname, t.pg_browser_title AS pagename, DATE(t.pg_starttime) as date ";
        $sqlMod.= "FROM (pg_content{$arrConf['setup']['tableext']} r, pg_module{$arrConf['setup']['tableext']} m) ";
        $sqlMod.= "INNER JOIN cmstree{$arrConf['setup']['tableext']} t ON (r.cmstreeid = t.id) ";
        $sqlMod.= " WHERE m.id <> {$this->objMod->pg_moduleid} AND t.pg_isactive = 1 AND r.website = 'frontend'";
        $sqlMod.= " AND t.parentid = ".$this->objMod->cmstreeid. "  AND r.pg_moduleid = m.id AND r.isactive = 1 AND isdeleted = 0 ";
        $sqlMod.= " ORDER BY t.seq, r.seq";
        
        // AND r.pg_contentareaid = $areaid

        $arrReturn= $objDB->fetch($sqlMod, array('type' => 'object')) or print mysql_error();

        $module_count = 0;
        $module_count_max = 0;
        $arrContent = array();

        if ($arrReturn['ok']) {
			$module_count_max = $arrReturn['num_rows'];

            if ($module_count_max) {

                foreach ($arrReturn['data'] as $objMod) {
                    $objMod->count = $module_count;
                    
                    $objModule = mFrontendBase::getInstance($objMod);
                    
                    if (is_subclass_of ($objModule, 'mFrontendBase')) {
                        $html= trim($objModule->getHtml());
                        if (!isset($arrContent[$objMod->cmstreeid])){
                                $arrContent[$objMod->cmstreeid]['name'] = $objMod->pagename;
                                $arrContent[$objMod->cmstreeid]['date'] = $objMod->date;
                                $arrContent[$objMod->cmstreeid]['html'] = $html;
                        }else{
                                $arrContent[$objMod->cmstreeid]['html'].= $html;
                        }

                    }
                }
            }
        }

        return $arrContent;

    } // End setContent();
}
?>
