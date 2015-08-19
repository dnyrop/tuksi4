<?php

/**
 * Denne klasse håndtere sitemap struktur
 *
 * @package tuksiFrontend
 */

class tuksiFrontendSitemap {
	
	private $save_all_nodes = false;
	
	// Indeholder site struktur, som kan bruges i menu'en
	private $arrSitemap = array();

	// Indeholder urlparts /[0]/[1]/[2]..., som hver er en side
	private	$arrUrlParts = array();

	// Indeholder assoc array af alle side fundet via urlparts
	private $arrTreeObjs	= array();

	// Indeholder side browser titler for alle urlparts
	private $arrTreeTitles = array();
	
	// Indeholder alle treeid's der må søges i
	private $arrSearchTreeIDs = array();
	
	private $arrAllNodes = array();
	
	private $arrFilter = array();
	
	private $forcedTreeId = null;
	
	/**
	 * Setting preview. Active page dont need to be active.
	 * 
	 */
	private $previewMode = false;
	
	function __construct() {
		
		$this->resolveUrl();
		
		// Hent hele menu struktur eller kun valgte menu punkter
		$this->load_all = false;
		
		// Hent alle indtil dette niveau
		$this->load_levels = 1;
	}
	
	function setLoadAll($bool) {
		$this->load_all = $bool;
	}
	
	function setSaveAllNodes($bool) {
		$this->save_all_nodes = $bool;
	}
	function setLoadLevels($level) {
		$this->load_levels = $level;
	}
	
	function getUrlParts() {
		return $this->arrUrlParts;
	}
	
	function getTreeObjs() {
		return $this->arrTreeObjs;
	}
	function getTreeTitles() {
		return $this->arrTreeTitles;
	}
	
	function getSearchTreeIDs() {
		return $this->arrSearchTreeIDs;
	}
	
	function getAllNodes() {
		return $this->arrAllNodes;
	}
	
	/**
	 * Tvinger Tuksi til at loade et bestemt tree id
	 * Praktisk hvis man f.eks. har 1 side der viser en masse produkter
	 *
	 * @param int $treeId
	 */
	function setForcedTreeId($treeId) {
		$this->forcedTreeId = $treeId;
	}
	
	function getTreeid() {
		$arrConf = tuksiConf::getConf();

		$iCount = count($this->arrTreeObjs);
				
		// Check forced tree id first
		if ($this->forcedTreeId) {
			$treeid = $this->forcedTreeId;
		} else {
			if ($iCount) {
				$treeid = $this->arrTreeObjs[$iCount-1]['id'];
			} elseif ($_GET->getInt('treeid')) {
				$treeid = $_GET->getInt('treeid');
			} else {
				// startpageid added, so rootid dont have to be frontpage
				if (isset($arrConf['site']['startpageid'])) {
					$treeid = $arrConf['site']['startpageid'];
				} else {
					$treeid = $arrConf['site']['rootid'];
				}
			}
		}
		
		// Hvis treeid ikke er fundet, sæt treeid til forside ID
		//if ($treeid == $arrConf['site']['rootid']) {
		//	tuksiDebug::log($treeid, "Frontpage");
		//}
		tuksiDebug::log("TreeID used: " . $treeid);
		
		return $treeid;
	}
	
	function makeSitemap($rootid = 0) {

	//	if ($_GET->getStr('preview') == 'on') {
	//		$this->previewMode = true;
	//		tuksiDebug::log('Setting preview mode: ' , 'on'); 
	//	}
		$arrConf = tuksiConf::getConf();

		tuksiDebug::log('Setting site root ID: ' . $arrConf['site']['rootid'], __CLASS__); 

		list($this->arrSitemap, $foo) = $this->getNodes($arrConf['site']['rootid'], 0, true);
	/*	
		print "<pre>";
		print_r($this->arrSitemap);
 		print "</pre>";
	 */

		//print_r($this->arrUrlParts);
		//print "<br>";
		//print_r($this->arrTreeObjs);
		return $this->arrSitemap;
	} // End MakeMenu()

	/**
	 * Return if current page is previewMode
	 *
	 * @return bool
	 */
	function getPreviewMode() {
		return $this->previewMode;
	}
	
	function getSitemap() {
		return $this->arrSitemap;
	}
	
	/**
	 * Henter urlparts til array $arrUrlParts
	 */
	function resolveUrl() {
		$urlPart = trim($_GET->getStr('urlpart'));
		if (!empty($urlPart)) {
			$this->arrUrlParts = explode('/', $urlPart);
		}
	}

	
	/**
	 * Funktion som returnere SQL, som henter underside til et treeid 
	 *
	 * @param int $parentid TreeID
	 * @return string SQL
	 */
	function sqlGetNodes($parentid) {

		$arrConf = tuksiConf::getConf();
		
		$sql = "SELECT t.id, t.name AS name, t.pg_menu_name AS menuname, t.pg_browser_title AS title, t.pg_urlpart AS urlpart, ";
		$sql.= "t.pg_show_inmenu AS show_inmenu, pg_notinsearch, pg_url AS url, pg_target AS target, pg_urlpart, pg_urlpart_full, ";
		$sql.= "pg_fulltext, t.haschild ";
		$sql.= "FROM cmstree" . $arrConf['setup']['tableext'] . " t ";
		$sql.= "WHERE t.parentid =  '{$parentid}' AND isdeleted = 0 ";
		$sql.= "AND t.pg_urlpart <> '' ";

		if (!$this->previewMode) {
			$sql.= "AND t.pg_isactive = 1 ";
		}

		$sql.= "ORDER BY t.seq ";

		return $sql;
	}
	
	/**
	 * Henter site struktur
	 *
	 * @param int $tree_parent Menu ROOT ID 
	 * @param int $level Fortæller på niveauet
	 * @param bool $node_open Fortælle om noden er valgt. Første node er altid åben.
	 */

	function getNodes($treeid_parent, $level = 0, $node_open = false, $arrParents = array()) {

		$objDB = tuksiDB::getInstance();
		
		$sql = $this->sqlGetNodes($treeid_parent);
		
		$arrReturn = $objDB->fetch($sql, array('name' => 'sitemap', 'expire' => 360));

		// indeholder fundne noder
		$arrNodes  = array();
		
		// Hver node har et array med alle parent ID'ere
		
		$arrParents[] = $treeid_parent;
		//print $treeid_parent;
		//print_r($arrParents);
		
		// Fortæller om noden har undernoder som skal vises.
		$haveMenuSubNodes = false;

		$count = 0;

		foreach ($arrReturn['data'] as $arrPage) {
			
			$count++;
			$arrPage['parentids'] = $arrParents;
			$arrPage['count'] = $count;
			
			if (!$arrPage['pg_notinsearch']) {
				$this->arrSearchTreeIDs[] = $arrPage['id'];
				// 	print $this->load_all . "-" . $arrPage['name'] . "<br>";
			}
			
			if ($this->save_all_nodes)
				$this->arrAllNodes[$arrPage['id']] = $arrPage;
                        
			$subnodes_loaded = false;
			$arrPage['open'] = false;
			
			// Fortæller om niveauet i menuen
			$arrPage['level'] = $level;

			$arrPage['menuname'] = stripslashes($arrPage['menuname']);
			$arrPage['open_selected'] = false;
			$arrPage['selected'] = false;
			$arrPage['haveMenuSubNodes'] = false;
			
			// Sætter at parent noden har undernoder, som er aktive.
			if ($arrPage['show_inmenu'])
				$haveMenuSubNodes = true;

				
			//print $arrPage['urlpart'] . " $selected ($level)=" . $this->arrUrlParts[$level] .'<br>' ;
				
			// Hvis hovednode er åben, og node er valgt i url
 			if ($node_open && isset($this->arrUrlParts[$level]) && $arrPage['urlpart'] == $this->arrUrlParts[$level]) {
 				
			//print $arrPage['urlpart'] . " $selected ($level)=" . $this->arrUrlParts[$level] .'<br>' ;
		
				// Fortæller om menupunktet er åbent
				$arrPage['open'] = true;
				
				// Fortæller om menupunkter er stien ned til den valgte side
				$arrPage['open_selected'] = true;
				
				// Hvis dette er sidste urlpart, så er dette den valgte side, som skal vises.
				if (!isset($this->arrUrlParts[$level+1]))
					$arrPage['selected'] = true;
							
				$this->arrTreeObjs[] = $arrPage;
			
				// Gemmer titles til browser
				$this->arrTreeTitles[] = $arrPage['title'];
				
				if (!$this->load_all) {
					$subnodes_loaded = true;
					list($arrPage['nodes'], $arrPage['haveMenuSubNodes']) = $this->getNodes($arrPage['id'], $level + 1, true, $arrParents);
				}
					
			} else {
				// Noden er ikke den aktive			
				$arrPage['selected'] = false;
				
				// Noden er ikke åben.
				$arrPage['open'] = false;
			}

			// Bestem om subnoder skal hentes, selve den ikke er aktiv.
			if ($arrPage['haschild']) {
				if ($this->load_all || ($level < $this->load_levels) && !$subnodes_loaded) {
					if (($level <= $this->load_levels))
						$arrPage['open'] = true;			
				
					// Henter undernoder	
					list($arrPage['nodes'], $arrPage['haveMenuSubNodes']) = $this->getNodes($arrPage['id'], $level + 1, false, $arrParents);
				}
			}

			$arrNodes[] = $arrPage;

		} // End while
		return array($arrNodes, $haveMenuSubNodes);
	} // End function getNodes();

} // End tuksiFrontendSitemap class

?>
