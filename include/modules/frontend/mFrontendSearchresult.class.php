<?php
/**
 * ??
 *
 * @uses tuksiDebug
 * @uses tuksiSmarty
 * 
 * @package tuksiFrontend
 * 
 */
class mFrontendSearchresult extends mFrontendBase {
	//return the html for the module
	function m_sogeresultat (&$objMod, &$objPage){

		parent::__construct($objMod);

		$this->objMod = $objMod;
		$this->tpl = new tuksiSmarty();

		//if (!$this->objPage->udvsite) {
			// Ingen caching i dette modul!
			//$this->tpl->setCaching(10);
		//}
		
	}

	function getHtml() {

		$this->makeSearch(strip_tags($_GET->getStr('q')));
		$this->tpl->assign('searchWord',strip_tags($_GET->getStr('q')));
		
		$template = "pagegenerator/modules/" . $this->objMod->template;

		
		if ($this->tpl->is_cached($template, $this->objMod->id)) {
			$html.=  $this->tpl->fetch($template, $this->objMod->id);
		} else {

			if (file_exists("templates/" . $template)) {
				$this->addStandardFields();
				$html.= $this->tpl->fetch($template, $this->objMod->id);
			} else {
				$html.= "HTML skabelon kunne ikke findes: {$template}";
			}
		}
		
		return $html;	
	}
	
	
	function makeSearch($query){
	
		//---------------------------------
		//search string
		//---------------------------------
	
		$query =	strtolower($query);
		
		$this->objPage->addDebug("Search:" . $query);
		
		include_once(dirname(__FILE__) . '/../../tuksi_sitemap.class.php');
		
		//---------------------------------
		// Hent sider der må søges i
		//---------------------------------
		
		$this->objSitemap = new tuksi_sitemap($this->objPage->arrConf['rootid']);
		$this->objSitemap->setLoadAll(true);
		$this->objSitemap->setLoadLevels(1);
		$this->objSitemap->setSaveAllNodes(true);
		
		$this->arrSitemap = $this->objSitemap->makeSitemap();
		
		//print_r($this->arrSitemap);
		$colorCounter = 1;
		$arrColors = array();
		
		foreach($this->arrSitemap[0]['nodes'] as &$val) {
			if(is_array($val) && $val['id']) {
				$arrColors[$val['id']] = $colorCounter++;
			}
		}
		
		$this->arrTreeIDs = $this->objSitemap->getSearchTreeIDs();
		$this->arrNodes = $this->objSitemap->getAllNodes();
		
		$arrFound=array();
		
		//$arrPGM_IDs=$this->getIDsFromTables($query);
		
		//---------------------------------
		// search in text pages
		//---------------------------------
		if(substr_count($query,' ') > 0) {
			$arrKeywords = explode(' ',$query);
			foreach ($arrKeywords as $keyword) {
				if(strlen($keyword) > 0) {
					$arrAgainst[] = "+$keyword*";
				}
				$againstSql = join(' ',$arrAgainst);
			}
		} else {
			$againstSql = "+$query*";
		}
		
		$sql = "SELECT t.id,t.pg_fulltext ";
		$sql.= "FROM cmstree t ";
		$sql.= "WHERE pg_isactive = 1 AND pg_notinsearch = 0 AND t.id <> 465 AND MATCH (pg_searchtext) AGAINST ('".mysql_real_escape_string($againstSql)."' IN BOOLEAN MODE)";

		$objDB = tuksiDB::getInstance();
	
		$rsSearch=$objDB->query($sql) or print mysql_error();
		if (mysql_num_rows($rsSearch)) {
			while ($objSearch = mysql_fetch_object($rsSearch)) {
				$arrNode = $this->arrNodes[$objSearch->id];

				$arrNode['pg_urlpart_full'] = str_replace('topmenu/', '', $arrNode['pg_urlpart_full']);
				$arrNode['pg_urlpart_full'] = str_replace('bundmenu/', '', $arrNode['pg_urlpart_full']);

				$arrFound[$objSearch->id]= $arrNode;

				$arrFound[$objSearch->id]['stripped_text'] = strip_tags($arrFound[$objSearch->id]['pg_fulltext']);

				$arrFound[$objSearch->id]['stripped_text'] = $this->highLightQuery($arrFound[$objSearch->id]['stripped_text'], $query);

				if($arrColors[$objSearch->id]) {
					$arrFound[$objSearch->id]['color'] = $arrColors[$objSearch->id];
				}else {
					if ($arrColors[$arrFound[$objSearch->id]['parentids'][2]]) {
						$arrFound[$objSearch->id]['color'] = $arrColors[$arrFound[$objSearch->id]['parentids'][2]];
					} else {
						$arrFound[$objSearch->id]['color'] = 0;
					}
				}
			}
		}
		// Vis resultat i browser
		$this->tpl->assign("result", $arrFound);
	}

	function highLightQuery($content, $query) {

		$len = 30;
		$len_delta= $len * 2;

		$strlen = strlen($content);

		$pos = strpos(strtolower($content), strtolower($query));

		// Tjek om query er tæt på slutning af tekst
		if ($pos + $len > $strlen) {
			$len_delta = $strlen - $pos + $len;
			
			$dotEnd = '';
		} else
			$dotEnd = '..';

		// Tjek om query er inden for de først $len tegn
		if ($pos > $len) {
			$content_part = substr ($content, $pos - $len, $len_delta);
			$dotStart = '..';
		} else {
			$content_part = substr ($content, 0,  $len_delta);
			$dotStart = '';
		}


		if ($content_part) {
			$content_part = $dotStart . $content_part. $dotEnd;
		} else {
			// ingen tekst??
			$content_part = $content;
		}
		//$content_part = strstr($content, $query);

		$content_part = preg_replace("/($query)/i", "<strong>\$1</strong>", $content_part);

		// Debuging
		//return $strlen . ':' . $pos . ':' . $len_delta.  '<br><br>' . $content_part . '<br><br>' . $content;
		return $content_part;
	}

}
?>
