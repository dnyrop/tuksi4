<?php
/** 
 * Tuksi DB klasse, som håndtere SQL's, samt timings.
 *
 * @package tuksiCore
 */
abstract class tuksiDB_superclass {
	
	/**
	 * @var array Indeholder komplet liste over kørte SQLs.
	 */
	var $arrSqlHistory = array();
	
	/**
	 * @var array Indeholder database forbindelser
	 */
	var $dbh = array();

	var $debug = false;
	var $cache	= false;

	/**
	 * @var array Indeholder setup. 
	 */
	var $arrSetup = array();

	// Force Extending class to define this method
	abstract public function getDBH($type = 'read');
	abstract protected function query($sql, $type = 'read');
   abstract public function fetch($sql, $arrOptions = array());
   abstract public function fetchItem($sql, $arrOptions = array());
   abstract public function write($sql);
   abstract protected function load_database($type = 'read');

		/**
	 * Sætter debugging aktiv
	 *
	 */
	public function setDebug() {
		$this->debug = true;
	}

	/**
	 * Sætter debugging aktiv
	 *
	 */
	public function setCacheOff() {
		$this->cache= false;

	}
	
	/**
	 * Henter SQL historie array
	 * 
	 * @return array 
	 */
	public function getHistory() {
		
		return $this->arrSqlHistory;
	
	} // addHistoryToDebug()

	/**
	 * Tilføj SQL til arrSqlHistory
	 *
	 */
	protected function addHistory($name, $action, $sql, $numrows, $execTime, $arrExplain, $arrAlert,$trace) {

		$objDebug = tuksiDebug::getInstance();
		
		$arrData = array(	 'name' => $name, 
								 'action' => $action, 
								 'sql' => $sql, 
								 'numrows' => $numrows, 
								 'exectime' => $execTime, 
								 'explain' => $arrExplain, 
								 'alert' => $arrAlert,
								 'trace' => $trace);
		
		$this->arrSqlHistory[] = $arrData;
												 
		$objDebug->sql($arrData);
	}

}



?>
