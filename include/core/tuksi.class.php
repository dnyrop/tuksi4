<?php
/**
 * Tuksi standard klasse
 *
 * @package Tuksi
 */
include_once(dirname(__FILE__).'/../tuksi_init.php');

/** 
 * Tuksi hoved klasse.
 *
 * @package Tuksi
 */
class tuksi {
	
	static private $instance;
	
	/**
	 * Indeholder objekt til tuksiDB
	 *
	 * @var object
	 */
		
	public function __construct() {
		$this->secSession();
		self::$instance = $this;
	} // end __construct();

	
	public function getInstance(){

		if (!self::$instance){
			self::$instance = new tuksi();
		}
		return self::$instance;
	}
	
  static function load_elements($cmstreetabid) {
		
		$arrTab = array();
		
		$objDB = tuksiDB::getInstance();
			
		$sqlElements = "SELECT fi.colname,d.content FROM cmsfielditem fi,cmsfielddata d WHERE d.rowid = fi.id AND d.cmsfielditemid = fi.id AND fi.itemtype = 'element' AND fi.relationid = $cmstreetabid";
		$arrResult = $objDB->fetch($sqlElements, array('cache' => 1, 'expire' => 10)); 
		
		foreach ($arrResult['data'] as &$arrRow) { 
			$arrTab[$arrRow['colname']] = $arrRow['content']; 
		}

		return $arrTab;
	}
	
	/**
	 * Tjekker for session hijacking, ved at sikre at man kommer fra samme IP og har samme browser.
	 *
	 */
	
	function secSession() {
		
		// Laver MD5 key som er uniqe pr. bruger.
		list($a, $b, $c) = explode(".", $_SERVER['REMOTE_ADDR']);
		$md5 = md5($a. $b. $c);
		
		if (!isset($_SESSION['session_md5'])) {
			$_SESSION['session_md5'] = $md5;
		} else {
			$objDebug = tuksiDebug::getInstance();
			$objDebug->log("OK (" . session_id() . ")","Checking session");
		}
		
		if ($md5 != $_SESSION['session_md5']) {
			$objDebug = tuksiDebug::getInstance();
			
			$objDebug->log("Ups, Making new (" . session_id() . ")","Checking session");
			$this->makeNewSessionID(true);
			$_SESSION['session_md5'] = $md5;
			$objDebug->log("Making new (" . session_id() . ")","Checking session");
		}
	}
	
	/**
	 * Laver et nyt PHP session ID
	 */
	function makeNewSessionID($delete_old = false) {
		session_regenerate_id($delete_old);			
	}
	
	/** 
	 * Funktion som afslutter klassen, samt den nedarvede klasse.
	 *
	 */
	function end() {
	} // End end();
	

} // END tuksi class
?>
