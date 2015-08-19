<?php
	
/** 
 * Tuksi configuration klass.
 * Sets data from database to tuksiIni class
 *
 * @uses tuksiDB
 * @uses tuksiIni
 * @package tuksiCore
 */
class tuksiConf {

	public $arrConf;
	
	static private $arrSiteConf = array();
	
	static private $instance;
	
	static private $webSiteStageName;
	static private $wildCatMatch;

	/**
	 * Loads default site and backend conf.
	 *
	 */
	function __construct() {
		$token = $_GET->getStr('token');
		
		// Load current site conf
		tuksiIni::$arrIni['site'] = $this->loadSiteConf(0, $token);

	
		// Loading backend information
		$this->loadBackendConf();

		// Loading backend information
		$this->appendNewsletterConf();

		
		 self::$instance = $this;
	} // End __construct();

	static function getInstance() {

		if (self::$instance) {
			return self::$instance;
		} else {
			return new tuksiConf();
		}
	} // End getInstance()
	
	/**
	 * Static function for getting tuksiConf array.
	 *
	 * @return unknown
	 */
	static function getConf() {
		$objConf = tuksiConf::getInstance();
		
		return tuksiIni::$arrIni;
	} // End getConf();


	/**
	 * returns site configuration from cmssite and cmssitelang
	 * Uses paremeter $cmssitelangid or $_GET vars (lang, siteid or treeid)
	 *
	 * @param int $cmssitelangid
	 * @return array
	 */
	function loadSiteConf($cmssitelangid = '', $token = '') {
		return self::loadSite($cmssitelangid, $token);
	} // End loadSiteConf();
	
	
	/**
	 * loadSite 
	 * 
	 * @param int $cmssitelangid If this is set, just get conf from this ID
	 * @param string $lang 
	 * @static
	 * @access public
	 * @return void
	 */
	static function loadSite($cmssitelangid = '', $token = '') {
		//print '----------------------<br>';
		//print "cmssitelangid: {$cmssitelangid}, sitelang: {$sitelang} <br><br>";

		$objDB = tuksiDB::getInstance();
		
		$isDefaultSite = false;
		if (!$cmssitelangid) {
			$isDefaultSite = true;
		}
		
		if (isset(self::$arrSiteConf[$cmssitelangid])) {
			return self::$arrSiteConf[$cmssitelangid];
		}
	
		if (!$cmssitelangid) {
			if (isset(tuksiIni::$arrIni['site']['cmssitelangid'])) {
				$cmssitelangid = tuksiIni::$arrIni['site']['cmssitelangid'];
				error_log("Setting default cmssitelangid: " . $cmssitelangid);
			}
		}

		if (tuksiIni::$arrIni['setup']['system'] != 'newsletter') {
			if ((tuksiIni::$arrIni['setup']['status'] == 'preview' && tuksiIni::$arrIni['auth']['active_preview']) ||
					(tuksiIni::$arrIni['setup']['status'] == 'prod' && tuksiIni::$arrIni['auth']['active_prod'])) {
				$authUser = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
				$authPass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
				$success = false;
				foreach (tuksiIni::$arrIni['auth']['auth_users'] as &$arrUser) {
					if ($authUser == $arrUser['login'] && $authPass == $arrUser['pass']) {
						$success = true;
						break;
					}
				}
				// Login failed
				if (!$success) {
					http_response_code(401);
					header('WWW-Authenticate: Basic realm="' . tuksiIni::$arrIni['auth']['auth_name'] . '"');
					$html = file_get_contents(dirname(__FILE__) . "/../../templates/auth.tpl");
					$html = str_replace('#SERVER_SIGNATURE#', $_SERVER['SERVER_SIGNATURE'], $html);
					print $html;
					exit();
				}
			}
		}

		// Setting current host
		$current_domain = $_SERVER['HTTP_HOST'];
		// Remove preview prefix if exist.
		$current_domain = preg_replace("/^preview\./", '', $current_domain);
		// Getting base domain
		$base_domain = preg_replace("/^(www|test)\./", '', $current_domain);

		// Lookup for redirects
		$sqlSec = "SELECT s.domain, l.urlpart_prefix AS prefix, sd.token ";
		$sqlSec.= "FROM cmssitesetup s ";
		$sqlSec.= "INNER JOIN cmssitesecdomain sd ON s.id = sd.cmssitesetupid ";
		$sqlSec.= "INNER JOIN cmssitelang l ON s.cmssitelangid = l.id ";
		$sqlSec.= "WHERE sd.domain = '%s' AND (sd.token = '%s' OR sd.token IS NULL OR sd.token = '') ";
		$sqlSec.= "ORDER BY sd.token DESC ";
		$sqlSec.= "LIMIT 1";
		$sqlSec = sprintf($sqlSec, $objDB->escapeString($current_domain), $objDB->escapeString($token));
		$arrRsSec = $objDB->fetchItem($sqlSec, array('expire' => 360, 'name' => 'getSecondary'));

		if ($arrRsSec['ok'] && $arrRsSec['num_rows']) {
			// Prepend preview to url if site is in preview mode
			$url = $arrRsSec['data']['domain'] . '/';
			$prefix = $arrRsSec['data']['prefix'];
			if (tuksiIni::$arrIni['setup']['status'] == 'preview') {
				$url = tuksiIni::$arrIni['setup']['preview_prefix'] . '.' . $url;
			} // if
			if (strlen($prefix)) {
				$url .= '/' . $prefix . '/';
			} // if
			if ($arrRsSec['data']['token'] == $token) {
				$url .= str_replace('/' . $token, '', $_SERVER['REQUEST_URI']);
			} else {
				$url .= $_SERVER['REQUEST_URI'];
			} // if
			$url = str_replace('//', '/', $url);
			http_response_code(301);
			header("Location: http://{$url}");
			exit();
		} // if
		
		// If not specific website try by domain
		// If not found by domain use default website
		$sqlSiteSetup = "SELECT s.* ";
		$sqlSiteSetup.= "FROM cmssitesetup s ";
		$sqlSiteSetup.= "INNER JOIN cmssitelang l ON s.cmssitelangid = l.id ";
		if ($cmssitelangid) {
			$sqlSiteSetup.= "WHERE l.id = '{$cmssitelangid}' ";
			if (self::$webSiteStageName) {
				$sqlSiteSetup.= "AND s.stagename = '" . self::$webSiteStageName . "' ";
			} // if
		} else {
			$sqlSiteSetup.= "WHERE s.domain = '{$current_domain}' ";
			$sqlSiteSetup.= "AND (l.urlpart_prefix = '{$token}' OR l.is_default = '1') ";
			if (self::$webSiteStageName) {
				$sqlSiteSetup.= "AND s.stagename = '" . self::$webSiteStageName . "' ";
			} // if
			$sqlSiteSetup.= "ORDER BY l.urlpart_prefix DESC ";
		} // if
		$sqlSiteSetup.= "LIMIT 1";

		//print $sqlSiteSetup . '<br>';
		$arrSiteSetup = $objDB->fetchItem($sqlSiteSetup, array('expire' => 360, 'name' => 'getDomain'));
		//print_r($arrSiteSetup);
		//print "<br>";

		if ($arrSiteSetup['ok'] && $arrSiteSetup['num_rows']) {
			$cmssitelangid = $arrSiteSetup['data']['cmssitelangid'];

			if (!isset(self::$webSiteStageName)) {
				self::$webSiteStageName  = $arrSiteSetup['data']['stagename'];
				//print "stagename: "	 . self::$webSiteStageName . '<br>';
			}
			
			// If wildcat match is registered try to replace
			if (isset(self::$wildCatMatch)) {
				$arrSiteSetup['data']['domain'] = preg_replace('/\*/', self::$wildCatMatch[self::$webSiteStageName][1],  $arrSiteSetup['data']['domain'], 1);
				$arrSiteSetup['data']['domain'] = preg_replace('/\*/', self::$wildCatMatch[self::$webSiteStageName][2],  $arrSiteSetup['data']['domain'], 1);
			}
		} else {
			//print "Check willcard<br>";
			$sqlSiteSetupWildCard = "SELECT s.* FROM cmssitelang l, cmssitesetup s ";
			$sqlSiteSetupWildCard.= "WHERE l.id = s.cmssitelangid AND s.domain LIKE '%*%'";

			$arrSiteSetupWildCard = $objDB->fetch($sqlSiteSetupWildCard, array('expire' => 360, 'name' => 'getDomainWildCard'));
			if ($arrSiteSetupWildCard['num_rows']) {
				foreach ($arrSiteSetupWildCard['data'] as $arrWildCard) {
					$preg = "/" . str_replace('*', '(.*)', $arrWildCard['domain']) . "/";
					if (preg_match($preg, $current_domain, $m)) {
						$arrWildCard['domain'] = $m[0];
						$arrSiteSetup['data'] = $arrWildCard;
						$arrSiteSetup['num_rows'] = 1;

						$cmssitelangid = $arrWildCard['cmssitelangid'];
						//print "Check willcard used {$arrWildCard['domain']}<br>";
						
						// When getting default site setup, we get stagename soo we can get default site confs for other
						// configs with same stagename.
						if ($isDefaultSite) {
							self::$webSiteStageName = $arrWildCard['stagename'];
							//print "stagename: "	 . self::$webSiteStageName . '<br>';
							self::$wildCatMatch[self::$webSiteStageName] = $m;
						}
					}
				}
			}
		}

//		print $cmssitelangid . '<br>';
		
		// OK cmssitelangid is found..		
		// Loading Main configuration
		
		
		// startpageid added soo rootid and frontpage dont have to be the same
		//check if lan is set
		$sqlConf = "SELECT s.*,l.id AS cmssitelangid, l.rootid AS rootid, l.startpageid, l.title AS langtitle, l.meta_description AS langmeta_description, ";
		$sqlConf.= "l.meta_keywords AS langmeta_keywords, l.lang, l.urlpart_prefix, l.mail_encoding ";
		$sqlConf.= "FROM cmssite s, cmssitelang l WHERE s.id = l.cmssiteid ";
		
		if(!empty($cmssitelangid)) {
			$sqlConf.= "AND l.id = '{$cmssitelangid}' ";
		} else {
			$sqlConf.= "AND s.is_default = 1 ";

			if(!empty($token)) {
				//get conf for current lan
				$sqlConf.= "AND l.urlpart_prefix = '{$token}'";
			} else {
				$sqlConf.= "AND l.is_default = 1";
			}
		}

		$arrConf = $objDB->fetchItem($sqlConf, array('expire' => 360, 'name' => 'SiteConf'));

		if ($arrConf['ok'] && $arrConf['num_rows']) {
			
			tuksiDebug::log('SiteID: ' . $arrConf['data']['id']);
			//print "<pre>";

			// Hvis arrSiteSetup not found via current domain, get default by cmssitelangid
			if (!isset($arrSiteSetup['num_rows']) || (isset($arrSiteSetup['num_rows']) && $arrSiteSetup['num_rows'] == 0)) {
			//	print "load Default<br>";
				$sqlSetups = "SELECT * FROM cmssitesetup ";
				$sqlSetups.= "WHERE cmssitelangid = '{$arrConf['data']['cmssitelangid']}' AND isdefault = 1";
			
				$arrSiteSetup = $objDB->fetchItem($sqlSetups, array('expire' => 360, 'name' => 'SiteSetup'));
			}

			$arrExcludeFields = array('id', 'cmssitelangid', 'isdefault', 'domain');
			if ($arrSiteSetup['num_rows']) {
				tuksiDebug::log('SiteSetupID: ' . $arrSiteSetup['data']['id']);
				
				$arrConf['data']['url_prodsite'] = $arrSiteSetup['data']['domain'];
				foreach ($arrSiteSetup['data'] as $field => $value) {
					if (!in_array($field, $arrExcludeFields)) {
						$arrConf['data'][$field] = $value;
					}
				}
			}
			
			// Assign base domain
			$arrConf['data']['domain'] = $base_domain;
			
			$wwwFixer = '';
			if (!preg_match('/^(www|test)\./i', $arrConf['data']['url_prodsite'])) {
				$wwwFixer = 'www.';
			}
			$arrConf['data']['url_preview'] = tuksiIni::$arrIni['setup']['preview_prefix'] . '.' . $wwwFixer . $arrConf['data']['url_prodsite'];

			if (tuksiIni::$arrIni['setup']['status'] == 'preview') {
				$arrConf['data']['url_site'] = tuksiIni::$arrIni['setup']['preview_prefix'] . '.' . $wwwFixer . $arrConf['data']['url_prodsite'];
			} else {
				$arrConf['data']['url_site'] = $arrConf['data']['url_prodsite'];
			}
			
			//print $arrConf['data']['url_site'] . '<br>';
			//print_r($arrConf);

			self::$arrSiteConf[$cmssitelangid] = $arrConf['data'];
			
			//print "<pre>";
			//print_r($arrConf['data']);
			//exit();
			return $arrConf['data'];
		}
		
		return array();
	}
	
	function appendNewsletterConf() {
		// Setting newsletter paths
		tuksiIni::$arrIni['newsletter']['path']['url_uploads'] = 'http://' . tuksiIni::$arrIni['site']['url_site'] . '/' . tuksiIni::$arrIni['path']['uploads_preview'] ; 
		tuksiIni::$arrIni['newsletter']['path']['url_site'] = 'http://' . tuksiIni::$arrIni['site']['url_site']; 
	}

	static function getAllSitesConf(){
		
		$arrSites = array();
		
		$objDB = tuksiDB::getInstance();
		
		//load all sites
		$sqlAll = "SELECT l.*, s.name as sitename, l.name AS langname ";
		$sqlAll.= "FROM cmssite s, cmssitelang l ";
		$sqlAll.= "WHERE s.id = l.cmssiteid ";
		
		$arrRsAll = $objDB->fetch($sqlAll);
		
		foreach($arrRsAll['data'] as $arrData) {
			$arrSites[] = self::getPageConf($arrData['rootid']);
		}
		
		return $arrSites;
	
	}
	/**
	 * Return siteConf for specific treeid
	 * Hvis der ikke findes et cmssitelangid returneres false da vi ikke er interreset i at få vist en ikke frontend side
	 *
	 * @param int $treeid
	 * @return array
	 */
	static function getPageConf($treeid) {
		
		$objDB = tuksiDB::getInstance();
		$objConf = tuksiConf::getInstance();
		
		$arrTree = $objDB->fetchRow('cmstree', $treeid, 'assoc', 'cmssitelangid');
		
		if(empty($arrTree['cmssitelangid']))
			return false;
		
		return $objConf->loadSiteConf($arrTree['cmssitelangid']);
	} // end getPageConf();
	
	/**
	 * Loading paths and cmsconfig data 
	 *
	 */
	private function loadBackendConf() {
		
		$objDB = tuksiDB::getInstance();
		
		// Sti til Tuksi. Kan være f.eks: '/tuksi'
		tuksiIni::$arrIni['path']['vcms'] = ''; 

		tuksiIni::$arrIni['path']['self'] = substr($_SERVER['PHP_SELF'], 0);

		// Relativ sti til Tuksi tema 
		$this->templatescheme = "default";
		
		// Relativ sti til Tuksi tema 
		tuksiIni::$arrIni['path']['theme']			= tuksiIni::$arrIni['path']['vcms'] . "/themes/" . $this->templatescheme . "/";
		tuksiIni::$arrIni['path']['stifinder']		= tuksiIni::$arrIni['path']['scms'] . "/files";
		tuksiIni::$arrIni['path']['suploadlive']	= tuksiIni::$arrIni['path']['scms'] . "/" . tuksiIni::$arrIni['path']['uploads_prod'];
		
		// If preview or backend site use preview upload.
		if (tuksiIni::$arrIni['setup']['status'] == 'preview' || tuksiIni::$arrIni['setup']['system'] == 'backend') {
			tuksiIni::$arrIni['path']['supload']	= tuksiIni::$arrIni['path']['scms'] . "/" . tuksiIni::$arrIni['path']['uploads_preview'];
			tuksiIni::$arrIni['path']['upload']	= "/" . tuksiIni::$arrIni['path']['uploads_preview'];
		} else {
			tuksiIni::$arrIni['path']['supload']	= tuksiIni::$arrIni['path']['scms'] . "/" . tuksiIni::$arrIni['path']['uploads_prod'];
			tuksiIni::$arrIni['path']['upload']	= "/" . tuksiIni::$arrIni['path']['uploads_prod'];
		}

		tuksiIni::$arrIni['path']['content'] = '';
		if (isset(tuksiIni::$arrIni['setup']['content_server'])) {
			// Overwrite content server per site
			if (!empty(tuksiIni::$arrIni['site']['content_server'])) {
				tuksiIni::$arrIni['setup']['content_server'] = tuksiIni::$arrIni['site']['content_server'];
			}
			tuksiIni::$arrIni['path']['upload'] = tuksiIni::$arrIni['setup']['content_server'] . tuksiIni::$arrIni['path']['upload'];
			tuksiIni::$arrIni['path']['content'] = tuksiIni::$arrIni['setup']['content_server'];
		}


		tuksiIni::$arrIni['path']['stables'] 		= tuksiIni::$arrIni['path']['scms'] . "/modules/tables";;
		tuksiIni::$arrIni['path']['vcss']			= tuksiIni::$arrIni['path']['vcms'] . "/themes/" . $this->templatescheme . "/stylesheet/tuksi.css";
		tuksiIni::$arrIni['path']['vimages']		= tuksiIni::$arrIni['path']['vcms'] . "/themes/" . $this->templatescheme . "/images/";
		
		$sqlVarConf = "SELECT * FROM cmsconfig ORDER BY section";
		$arrRsVarConf = $objDB->fetch($sqlVarConf, array('expire' => 360, 'name' => 'cmsconfig'));
		
		if ($arrRsVarConf['ok'] && $arrRsVarConf['num_rows'] > 0) {
			
			foreach ($arrRsVarConf['data'] as $arrVal) {
				if($arrVal['section']) {
					tuksiIni::$arrIni[$arrVal['section']][$arrVal['token']] = $arrVal['value'];
				} else {
					tuksiIni::$arrIni[$arrVal['token']] = $arrVal['value'];
				}
			}
		}
	} // end loadBackendConf();
	
} // end tuksiConf()
?>
