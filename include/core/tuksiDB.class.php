<?php
/** 
 * Tuksi DB klasse, som håndtere SQL's, samt timings.
 *
 * @package tuksiCore
 */
class tuksiDB {
	
	/**
	 * Variable der indeholder én instance af tuksiDB
	 *
	 * @var unknown_type
	 */
	static private $arrInstance;
	
	/**
	 * @var array Indeholder database forbindelser
	 */

	static $arrSetup = array();

	static $debug = false;

	/**
	 * @var array Indeholder komplet liste over kørte SQLs.
	 */
	var $arrSqlHistory = array();


	static function addSetup($name, $dbtype, $arrSetup) {
		$arrSetup['dbtype'] = $dbtype;
		$arrSetup['name'] = $name;

		if (!isset(self::$arrSetup[$name])) {
			self::$arrSetup[$name] = $arrSetup;	
		}
	}

	static function getInstance($name = 'default') {
		//print "Getting instance<br>";
					//
		// Load setup fra INI hvis den ikke er loadet
		if (!count(self::$arrSetup)) {
			self::loadSetup();
		}
					
		if (isset(self::$arrSetup[$name]['obj'])) {
			return self::$arrSetup[$name]['obj']; 
		} elseif (self::$arrSetup[$name]) {
			//print "Making instance<br>";
			
			$classname = 'tuksiDB' . self::$arrSetup[$name]['dbtype'];

			$filename = $classname . '.class.php';
			include_once(dirname(__FILE__) . '/' . $filename);

			$objDB = new $classname(self::$arrSetup[$name]);

			self::$arrSetup[$name]['obj'] = $objDB;
			
			return $objDB;
		} else {
			print "No DB setup with that name ($name)";
		}
	}

	/** Loading DB setup from ini file
	 *
	 */
	static function loadSetup() {
		
		$debug = false;

		$arrDBSetup= parse_ini_file(dirname(__FILE__) . '/../../configuration/tuksi_db.ini', true);

		$server_host = php_uname('n');
		$system_type = tuksiIni::$arrIni['setup']['system'];
		
		if ($debug) print "System: $system_type<br>";
		if ($debug) print "Host: $server_host<br>";

		$arrSetups = array();

		foreach ($arrDBSetup as $ini_name => $arrSetup) {
			$bSetupOk = false;
			$ini_hostname = '';
				
			if ($debug) print "<hr>";
			$arrList = explode("_", $ini_name, 3);

			$name = $arrList[0];
			$level = count($arrList);

			if ($debug) print_r($arrList);
			if ($debug) print "<br>";
			
			$ininame = $arrList[0];

			// Format [connect name][hostname]
			if ($level == 2) {
				if ($debug) print "Format is: [connect name][hostname]<br>";

				$ini_hostname = $arrList[1];

				if ($server_host == $ini_hostname) {
					$name = $ininame;
					$bSetupOk = true;
				}
			}
			// Format [connect name][system type][hostname]
			if ($level == 3) {
				if ($debug) print "Format is: [connect name][system type][hostname]<br>";

				$ini_system= $arrList[1];
				$ini_hostname = $arrList[2];

				if ($ini_system == $system_type && $server_host == $ini_hostname) {
					$bSetupOk = true;
				}

			}
			if ($level == 1) {
				if ($debug) print "Format is: [connect name]<br>";

				$bSetupOk = true;
			}
			if ($debug) print '<br>';
			
			if ($bSetupOk && !isset(self::$arrSetup[$name])) {
				if ($debug) print "adding [$name]<br>";
				if ($debug) print_r($arrSetup);
				if ($debug) print "<br>";

				$arrSetup['read_user'] = str_replace('##HOST##', $server_host, $arrSetup['read_user']);
				$arrSetup['write_user'] = str_replace('##HOST##', $server_host, $arrSetup['write_user']);

				self::addSetup($name, $arrSetup['type'], array(	'debug' => tuksiIni::$arrIni['debug'], 'dbname' => $arrSetup['default_dbname'],
																			'read' => array(	'host' => $arrSetup['read_host'], 
																									'user' => $arrSetup['read_user'], 
																									'pass' => $arrSetup['read_pass']), 
																			'write' => array(	'host' => $arrSetup['write_host'], 
																									'user' => $arrSetup['write_user'], 
																									'pass' => $arrSetup['write_pass'])));
			} else {
				if ($debug) print "Not valid<br>";
			}
		}
		if ($debug) die();
	}
	
	/**
	 * Sætter debugging aktiv
	 *
	 */
	static function setDebug() {
		foreach (self::$arrSetup as $arrSetup) {
						self::$arrSetup[$arrSetup['name']]['debug'] = 1;

			if (isset($arrSetup['obj']))
				$arrSetup['obj']->setDebug();
		}
	}

	function getDBH() {
		if (!isset($this->dbh))
			$this->load_database();
			
		return $this->dbh;
	}
	
	/**
	 * Henter SQL historie array
	 * 
	 * @return array 
	 */
	function getHistory() {
		
		$this->arrSqlHistory = array();
		foreach (self::$arrSetup as $arrSetup) {

			if (isset($arrSetup['obj']))
				$this->arrSqlHistory = array_merge($this->arrSqlHistory, $arrSetup['obj']->getHistory());
				
		}
		return $this->arrSqlHistory;
	
	} // addHistoryToDebug()

	/**
	 * Afslutter klassen, ved at tilføje SQL historie til debug array
	 *
	 */
	function end() {
		foreach (self::$arrSetup as $arrSetup) {

			if (isset($arrSetup['obj']))
				$arrSetup['obj']->end();				
		}
		
	} // End End();

} // End tuksiDB klasse

?>
