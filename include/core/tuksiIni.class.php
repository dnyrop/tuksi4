<?php
/**
 * Tuksi standard klasse
 * Sætter alle statiske variabler.
 * @package tuksiCore
 */

class tuksiIni {
	
	static private $instance;
	static public $arrIni;
	
	public $phpversion;
	
	private function __construct(){
		
		define("ISWEB", 1);
		define("ISSHELL", 2);

		
		$this->setPhpVersion();
		
		self::$arrIni = $this->loadIni();
		
		// Tjek om det er Web eller shell program
		self::$arrIni['setup']['system'] = $this->checkSystem();
		
		// Start PHP Session hvis scriptet køres via Web
		if (self::$arrIni['session']['default'] == 1 && self::$arrIni['setup']['system'] == 'frontend') { 
			session_start();
		} else if(self::$arrIni['setup']['system'] == 'backend'){
			session_start();
		}

		$this->getSiteStatus();
		
		self::$instance = $this;
	}
	
	static function getInstance(){
		if (!self::$instance){
			self::$instance = new tuksiIni();
		}
		return self::$instance;
	}
	
	/**
	 * This can be used to swich scope between backend, frontend and newsletter
	 * Remember to swich back!
	 * 
	 * @param mixed $type (backend, frontend or newsletter) 
	 * @return void
	 */
	static function setSystemType($type) {
		$objIni = tuksiIni::getInstance();
		
		self::$arrIni['setup']['system'] = $type;
		
		if($type == 'newsletter') {
			if(!self::$arrIni['newsletter']) {
				self::loadNewsletterConf();
			}
		}
	}
	
	/**
	 * Set CmsSiteLangId manually.
	 *
	 * @param unknown_type $cmssitelangid
	 */
	static function setSiteLangID($cmssitelangid) {
		$objIni = tuksiIni::getInstance();
		
		self::$arrIni['site']['cmssitelangid'] = $cmssitelangid;
	}
	
	/** 
	 * Tjek om det er Backend, Frontned or Shell
	 *
	 * @return int ISWEB eller ISSHELL; 
	 */
	function checkSystem() {	
		if (isset($_SERVER['REQUEST_URI'])) {
			$request_uri = $_SERVER['REQUEST_URI'];
		} else {
			$request_uri = '';
		}
		
		if (preg_match('%^/' . self::$arrIni['setup']['admin'] . '(/|$)%', $request_uri, $m)) {
			return 'backend';
		} elseif (preg_match('%^/' . self::$arrIni['setup']['newsletter'] . '(/|$)%', $request_uri, $m)) {
			return 'newsletter';
		} elseif (isset($_SERVER['SHELL'])) {
			return 'shell';
		} else {
			return 'frontend';
		}
	} // End checkSystem()
	
	function setPhpVersion() {
		preg_match("/^(\d+)./", PHP_VERSION, $m);
		
		$this->phpversion = $m[1];
	}
	
	private function loadIni() {
		$iniFile = dirname(__FILE__) . '/../../configuration/tuksi.ini';

		if (file_exists($iniFile)) {
			$arrIni= parse_ini_file($iniFile, true);
		} else {
			// TODO: Make error class handler
			print "INI file not found";
			exit();
		}

		return $arrIni;
	}
	
	static function getIni() {
		return self::$arrIni;
	}

	public function setSetup($arrSetup) {
		$this->arrIni['setup'] = $arrSetup;
	}
	
	/**
 	*  Bestemmer status på websitet 
 	*/
	private	function getSiteStatus() {

		// Deside if domain is preview website
		$preview_prefix= self::$arrIni['setup']['preview_prefix'];
		
		if (isset($_SERVER['HTTP_HOST'])) {
			$host = $_SERVER['HTTP_HOST'];
		} else {
			$host = '';
		}

		// Disse muligheder findes
		// Er på preview website
		// Er på preview website med emulate live
		// Er på prod website 
		
		// Tjek først om vi er på test eller prod website
		$isPreview = preg_match("/^{$preview_prefix}/", $host, $m);

		//print "IsPreview: $isPreview";

		$server_host = php_uname('n');

		self::$arrIni['setup']['hostname'] = $server_host;
		self::$arrIni['setup']['charset'] = ini_get('default_charset');

		self::$arrIni['cache']['key_prefix'] = $_SERVER['HTTP_HOST'];

		// Website root
		tuksiIni::$arrIni['path']['scms'] = realpath(dirname(__FILE__) . "/../..");

  	if ($isPreview) {

			self::$arrIni['setup']['status'] = 'preview';
			self::$arrIni['setup']['tableext'] = '';

			// Cache is off on preview website
			self::$arrIni['cache']['active'] = self::$arrIni['cache']['active_preview'];

			// Har man sat emulate live køres på prod data men med debug info.
			if (isset(self::$arrIni['setup']['emulatelive']) && self::$arrIni['setup']['emulatelive'] == 1) {
				self::$arrIni['setup']['status'] = 'prod';
				self::$arrIni['setup']['tableext'] = 'live';
				self::$arrIni['debug']['no_sql_cache'] = 0;
				self::$arrIni['debug']['active'] = 1;
				self::$arrIni['cache']['active'] = self::$arrIni['cache']['active_prod'];
			}
			if (self::$arrIni['debug']['active'])
			        error_reporting(E_ALL & ~E_NOTICE);
		} else {
					

			error_reporting(E_USER_ERROR | E_ERROR | E_WARNING | E_PARSE);

			//$this->debug->log("Site status", "Livesite");
			self::$arrIni['setup']['status'] = 'prod';
			self::$arrIni['setup']['tableext'] = 'live';
			self::$arrIni['debug']['no_sql_cache'] = 0;
			self::$arrIni['debug']['active'] = 0;
			
			self::$arrIni['cache']['active'] = self::$arrIni['cache']['active_prod'];
		} 
		
		if (self::$arrIni['setup']['system'] == 'backend') {
			if (isset($_SESSION['backend_debug_active']) && $_SESSION['backend_debug_active']) {
				self::$arrIni['debug']['active'] = 1;
			} else {
				self::$arrIni['debug']['active'] = 0;
			}
			self::$arrIni['debug']['no_sql_cache'] = 0;
		}
		if (self::$arrIni['setup']['system'] == 'newsletter') {
			// Newsletter always gets data from bare tables because data is not released
			self::$arrIni['setup']['status'] = 'preview';
			self::$arrIni['setup']['tableext'] = '';
			self::loadNewsletterConf(); 
		}
		
		if (isset(self::$arrIni['auth'], self::$arrIni['auth']['auth_users'])) {
			foreach (self::$arrIni['auth']['auth_users'] as &$user) {
				$arrUser = explode(':', $user);
				$user = array('login' => $arrUser[0], 'pass' => $arrUser[1]);
			}
		}
																				
	}
	
	/**
	 * Loader Newsletter.ini
	 * 
	 * TuksiConf can have entered some values in this array, så the array can not be overwritten. This is
	 * the reason for the foreach 
	 *
	 */
	static function loadNewsletterConf() {
		
		if(!isset(self::$arrIni['newsletter']) || !isset(self::$arrIni['newsletter']['setup'])) {
		
			$arrNewsletterIni = parse_ini_file(dirname(__FILE__) . "/../../configuration/newsletter.ini", true);  

			foreach ($arrNewsletterIni as $section_name => $arrSection) {
				foreach ($arrSection as $key => $value) {
					self::$arrIni['newsletter'][$section_name][$key] = $value;
 				}
				
			}

			self::$arrIni['newsletter']['path']['spool'] = self::$arrIni['path']['scms'] . self::$arrIni['newsletter']['path']['spool'];
			self::$arrIni['newsletter']['path']['spool_single'] = self::$arrIni['path']['scms'] . self::$arrIni['newsletter']['path']['spool_single'];
		}
	}
	
} // End Tuksi_ini klasse
?>
