<?php
/** 
 * Tuksi DB klasse, som håndtere SQL's, samt timings.
 *
 * @package tuksiCore
 */
class tuksiDBMysql extends tuksiDB_superclass {
	
	private $arrDebug = array();
	
	/**
	 * Loader tuksi_db() klassen, og tilføjer default database forbindelse af $tuksi->conf.
	 *
	 * @return tuksi_db
	 */
	function __construct($arrSetup) {

		//print "Mysql class loaded<br>";
		//print_r($arrSetup);

		$this->arrSetup = $arrSetup;

		if (isset($arrSetup['debug'])) {
			$this->arrDebug = $arrSetup['debug'];
		}
			
	} // End tuksi_db()
	
	/**
	 * Loader database.
	 *
	 * @param string $name Load database udfra om der er read eller write
	 */
	protected function load_database($type = 'read') {
		//$old = error_reporting(0);
				
		// Loading database class
		if($this->load_currentDB($type)) {
			return $this->dbh[$type];
		} else {
			if (isset($this->arrDebug['active']) && $this->arrDebug['active']) {
				print mysql_error();
			} else {
				header("Location: /services/error.php?error=loaddatabase");
				exit();
			}
		}
	} // End load_database();
	
	
	
	private function load_currentDB($type = 'read'){
		
		if (isset($this->dbh[$type])) 
			return true;

		if ($this->dbh[$type] = @mysql_connect($this->arrSetup[$type]['host'], $this->arrSetup[$type]['user'], $this->arrSetup[$type]['pass'], true)) {
			mysql_set_charset('latin1', $this->dbh[$type]);
			if (!@mysql_select_db($this->arrSetup['dbname'], $this->dbh[$type])) {
				return false;
			} 
			$this->selectDB = $this->arrSetup['dbname'];
		} else {
			return false;
		}
		return true;
	}
	
	
	function dbconnected(){
		if($this->load_currentDB()){
			return true;
		} else {
			return false;
		}
	}
	

	/**
	 * Hent DB handler
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	public function getDBH($type = 'read') {
		if (!isset($this->dbh[$type]))
			$this->load_database($type);
			
		return $this->dbh;
	}
	
	/**
	 * Kørsel af SQL, hvor der tages tid og gemmes i SQL historie.
	 * 
	 * Database loades ved først kørsel af query.
	 *
	 * @param string $sql SQL der skal køres
	 * @param string $name Database forbindelse, Default = "default"
	 * @return resource Returnere resultset.
	 */
	protected function query($sql, $type = 'read') {

		$dbh = 	$this->load_database($type);
		mysql_select_db($this->selectDB, $dbh);

		if ($_GET->getStr('trace')) {
			//print "<pre>\n";
			//print $GLOBALS['sqls']++ . ':'.$type . ' - ' . $sql . '\n';
			//if ($sql == 'SELECT id,name FROM cmstree WHERE parentid = \'82\' AND isdeleted = 0 ORDER by seq')
			//print_r(debug_backtrace());
			//			print "</pre>\n";
		}
		
		$arrExplain = array();
		$arrAlert = array();
	
		if ($this->arrDebug['active']) {
			
			$timeparts = explode(" ",microtime());
			$timer_starttime = $timeparts[1].substr($timeparts[0],1);
	
			// Hvis udvsite og SELECT statement, lav explain og sæt timer
			if (preg_match("/^select/i", $sql, $m)) {

				$sqlExplain = "EXPLAIN " . $sql;
				$rsExplain = @mysql_query($sqlExplain, $dbh); 
				$arrAlert = array();
				if($rsExplain) {
					while ($row = mysql_fetch_assoc($rsExplain)) {
						if (!$row['key'] && (stripos($sql,"where") !== false)) {
							$arrAlert[] = array('text' => 'Intet index i ' . $row['table'] . $sql);
						}
						$arrExplain[] = $row;
					}
				} 
				// Fjern query CACHE også med SQL_NO_CACHE
				if ($this->arrDebug['no_sql_cache']) {
					$sql = preg_replace("/^select/i", "SELECT SQL_NO_CACHE ", $sql);
				}
				
			} 
		}
		$rs = @mysql_query($sql, $dbh); 
		
		if ($rs) {
			$this->num_rows = @mysql_num_rows($rs);
		} else {
			 //throw new Exception('Mysql Error: ' . mysql_error());
			$this->num_rows = mysql_error();
			$arrAlert[] = array('text' => 'Fejl i SQL: ' . mysql_error());
		}
		
		if ($this->arrDebug['active']) {
			$timeparts = explode(" ",microtime());
			$endtime = $timeparts[1].substr($timeparts[0],1);
			$execTime = $endtime - $timer_starttime;

			$action = '?';
			if (preg_match("/^select/i", $sql, $m))
				$action = 'read';

			if (preg_match("/^(insert|update|replace|delete)/i", $sql, $m))
				$action = 'write';

			//	print $this->trace();
			$this->addHistory($this->arrSetup['name'], $action, $sql, $this->num_rows, $execTime, $arrExplain, $arrAlert, $this->trace());
			
		}
		return $rs;
	} // End Query();

	/**
	 * Real escape string
	 *
	 * @param $str String
	 * @param $type Connection name
	 *
	 * @return string
	 */
	public function realEscapeString($str, $type = 'read') {
		return $this->escapeString($str, $type);
	}
	
	public function escapeString($str, $type = 'read') {
		$dbh =  $this->load_database($type);

		return mysql_real_escape_string($str, $dbh);
	}

	/**
	 * Lav ændring i database
	 *
	 * @param string $sql SQL
	 * @return array arrReturn
	 */
	public function write($sql) {
		$arrReturn = array();

		// Tjekker om SQL er SELECT statement. 
		$is_write= preg_match("/^(insert|update|replace|delete|create)/i", $sql, $m); 

		if (!$is_write) {
			$arrReturn['ok'] = false;
			$arrReturn['error'] = "Only INSERT, REPLACE, DELETE / UPDATE statements allowed!";

			return $arrReturn;
		}

		$rs = $this->query($sql, 'write');
		if ($rs) {
			$dbh =  $this->load_database('write');
			$arrReturn['ok'] = true;
			$arrReturn['insert_id'] = mysql_insert_id($dbh);
			$arrReturn['num_rows'] = $this->affected_rows($dbh);
			$arrReturn['sql'] = $sql;
		} else {
			$arrReturn['ok'] = false;
			$arrReturn['error'] = mysql_error();
			$arrReturn['sql'] = $sql;
		}

		return $arrReturn;
	}

	/**
	 * Returne én række fra en table
	 *
	 * @param string $sql SQL statemant.
	 * @param mixed $id ID eller array med WHERE kriterier
	 * @param string $name Database forbindelse, Default = "default"
	 * @return object Returnere en fundne række.
	 */		  
	public function fetch($sql, $arrOptions = array()) {

		$arrReturn = array();
		
		if(empty($sql)) {
			$arrReturn['ok'] = false;
			$arrReturn['error'] = "Empty query!";
			return $arrReturn;
		}
			
		(isset($arrOptions['expire'])) ? $expire = $arrOptions['expire'] : $expire = 0;

		if (tuksiIni::$arrIni['cache']['active'] && $expire > 0) {

			if (isset($arrOptions['name'])) {
				$cache_name = md5($sql) . '-' . $arrOptions['name'];
			} else {
				$cache_name = md5($sql);
			}
			
			if ($arrReturn = tuksiCache::get($cache_name)) {
				if (isset($arrOptions['type']) && $arrOptions['type'] == 'object') {
					$arrReturn = unserialize($arrReturn);
				}
				return $arrReturn;
			}
		}

		//print $sql . '<br>';

		// Tjekker om SQL er SELECT statement. 
		$is_select = preg_match("/^select|show|describe/i", $sql, $m); 

		if (!$is_select) {
			$arrReturn['ok'] = false;
			$arrReturn['error'] = "Only SELECT statements allowed!";
			return $arrReturn;
		}

		$arrReturn['sql'] = $sql;
		$arrReturn['data'] = array();
		
		$rs = $this->query($sql);
		
		if ($rs) {
			$arrReturn['num_rows'] = mysql_num_rows($rs);
			$arrReturn['ok'] = true;
			
			if(isset($arrOptions['tablename']) && $arrOptions['tablename']) {
				$arrReturn['tablename'] = mysql_field_table($rs, 0);
			} 
			
			if(isset($arrOptions['num_fields']) && $arrOptions['num_fields']) {
				$arrReturn['num_fields'] = mysql_num_fields($rs);
			} 
			
			if(isset($arrOptions['fields']) && $arrOptions['fields']) {
				$arrCols = array();	
				for ($i=0;$i< mysql_num_fields($rs);$i++){
						$arrCols[] = mysql_fetch_field($rs, $i);
					}
					$arrReturn['fields'] = $arrCols;
			} 
			
			
			if ($arrReturn['num_rows']) {
					
				$fetch_type = 'mysql_fetch_assoc';

				if (isset($arrOptions['type'])) {
					switch ($arrOptions['type']) {
						case('object') : $fetch_type = 'mysql_fetch_object'; break;
						case('array') : $fetch_type = 'mysql_fetch_array'; break;
					}
				}
				while ($arrRow = $fetch_type($rs)) {
					$arrReturn['data'][] = $arrRow;
				}	
			}
		} else {
			$trace = debug_backtrace();;
			$arrReturn['ok'] = false;
			$arrReturn['error'] = mysql_error();
			$arrReturn['num_rows'] = 0;
			$objDebug = tuksiDebug::getInstance();
			$objDebug->error($arrReturn['error'],"",true);

		}
		
		if (tuksiIni::$arrIni['cache']['active'] && $expire > 0) {
			if (isset($arrOptions['name'])) {
				$cache_name = md5($sql) . '-' . $arrOptions['name'];
			} else {
				$cache_name = md5($sql);
			}
			$arrReturnData = $arrReturn;
			if (isset($arrOptions['type']) && $arrOptions['type'] == 'object') {
				$arrReturnData = serialize($arrReturnData);
			}
			tuksiCache::set($cache_name, $arrReturnData, $expire);
		}
		
		mysql_select_db($this->arrSetup['dbname']);
		
		return $arrReturn;	
	} // End fetch()

	
	public function fetchItem($sql, $arrOptions = array()) {
		
		$arrReturn = $this->fetch($sql, $arrOptions);

		if (isset($arrReturn['data']) && isset($arrReturn['data'][0]))
			$arrReturn['data'] = $arrReturn['data'][0];
		else
			$arrReturn['data'] = array();

		return $arrReturn;
	}
	
	
	/**
	 * Returne én række fra en table
	 *
	 * @param string $tablename Table der skal hentes række fra.
	 * @param mixed $id ID eller array med WHERE kriterier
	 * @param string $name Database forbindelse, Default = "default"
	 * @return object Returnere en fundne række.
	 */		  
	public function fetchRow($tablename, $id, $return_type = 'assoc', $sqlSelect="*") {
		
		if (is_array($id) && count($id)) {
			foreach ($id as  $field => $value) 
				$arrFields[] = $field . "= '" . $this->realEscapeString($value) . "'";

			$sql = "SELECT {$sqlSelect} FROM $tablename WHERE " . join(" AND ", $arrFields);
	
		} else {

			$sql = "SELECT {$sqlSelect} FROM $tablename WHERE id = '$id'";
		
		}
		
		$rs = $this->query($sql);
		
		if (mysql_errno() == 0 && mysql_num_rows($rs) > 0) {
			switch($return_type) {
				case('object') : return mysql_fetch_object($rs); break;
				case('array') : return mysql_fetch_array($rs);
				default:  return mysql_fetch_assoc($rs);
			}
			
		} else {
			return false;
		}
			
	} // End getrow()
	
	/**
	 * Copies a row from a table to a new row
	 *
	 * @param unknown_type $tablename
	 * @param unknown_type $id
	 */
	
	public function copyRow($tablename,$id){
		
		if(($arrRow = $this->fetchRow($tablenamem,$id)) !== false){
			$arrSet = array();
			foreach ($arrRow as $fieldname => $value) {
				$arrSet[]= "$fieldname = '" . $this->realEscapeString($value). "' ";
			}
			$sqlIns = "INSERT INTO $tablename VALUES " . join(", ",$arrSet);
			die($sqlIns);
		}
	}
	
	/**
	 * Henter kolonner fra en tabel
	 *
	 * @param string $tablename
	 * @return array
	 */
	public function getArrColumns($tablename) {
		$arrCol = array();
		
		$sql="SHOW COLUMNS FROM {$tablename}";
		$rs = $this->query($sql);
	
		while($arr = mysql_fetch_assoc($rs))
			$arrCol[] = $arr;

		return $arrCol;
	}

	public function getTables($dbname = ""){
		
		if(!$dbname)
			$dbname = $this->arrSetup['dbname'];
		//getting all tables in database
		$result = mysql_list_tables($dbname);

		$arrTables = array();
		
		while ($arrTable = mysql_fetch_array($result)) {
			$arrTables[$arrTable[0]] = $arrTable[0];
		}
		
		if($dbname != $this->arrSetup['dbname']) {
			mysql_select_db($this->arrSetup['dbname']);
		}
		
		return $arrTables;
	}
	
	public function getDatabases(){
		
		//getting all tables in database
		$result = mysql_list_dbs();

		$arrDBs = array();
		
		while ($arrDB = mysql_fetch_array($result)) {
			$arrDBs[$arrDB[0]] = $arrDB[0];
		}
		
		return $arrDBs;
	}
	
	
	public function getFields($tablename, $like = ""){
		
		$sqlFields = "SHOW COLUMNS FROM " . $tablename;
		
		if (isset($like) && $like != '') {
			$sqlFields .= " LIKE '{$like}'";
		}
 		$arrRsFields = $this->fetch($sqlFields);
		
		return $arrRsFields;
	}

	/**
	 * Afslutter klassen, ved at tilføje SQL historie til debug array
	 *
	 */
	function end() {
		if (isset($this->dbh['read']))
			mysql_close($this->dbh['read']);
		
		if (isset($this->dbh['write']))
		mysql_close($this->dbh['write']);
		
	} // End End();
	
	public function insert($tbl, $arr, $arrNoQuotes = array()) {
		if (!($this->array_withitem($arr) || $this->array_withitem($arrNoQuotes))) return false;
		
		$this->arraySqlEscape($arr);
		$this->arrayQuote($arr);
		$this->arraySqlEscape($arrNoQuotes);		
		
		$arrInsert = array_merge($arr, $arrNoQuotes);
		
		$sql = "INSERT INTO %s (%s) VALUES (%s)";
		$sql = sprintf($sql, $tbl, join(', ', array_keys($arrInsert)), join(', ', $arrInsert));
		return $this->write($sql);
	}
	
	public function update($tbl, $arr, $arrNoQuotes = array(), $strWhere = "") {
		if (!($this->array_withitem($arr) || $this->array_withitem($arrNoQuotes))) return false;
		
		if (!empty($strWhere)) {
			$strWhere = "WHERE " . $strWhere;
		}
		
		$this->arraySqlEscape($arr);
		$this->arrayQuote($arr);
		$this->arraySqlEscape($arrNoQuotes);		
		
		$arrInsert = array_merge($arr, $arrNoQuotes);
		$arrValues = array();
		foreach ($arrInsert as $key => &$value) {
			$arrValues[] = $key . " = " . $value;
		}
		
		$sql = "UPDATE %s SET %s %s";
		$sql = sprintf($sql, $tbl, join(', ', $arrValues), $strWhere);
		return $this->write($sql);
	}
	
	public function delete($tbl, $arrWhere = array()) {
		
		$this->arraySqlEscape($arrWhere);
		
		$arrSqlWhere = array();
		foreach ($arrWhere as $col => &$value){
			$arrSqlWhere[] = $col . " = " . $value;
		}
		if (count($arrSqlWhere)) {
			$sqlWhere = "WHERE " . join(" AND ", $arrSqlWhere);
		} else {
			$sqlWhere = '';
		}
		$sqlDel = sprintf("DELETE FROM %s %s", $tbl, $sqlWhere);
		
		return $this->write($sqlDel);
	}
	
	// * ------------------------------------------------------------------------- *
	// Validating SQL. Only SELECT is valid
	// * ------------------------------------------------------------------------- *
	
	function validateSelectSQL($sql) {
		
		$mySql = strtolower($sql);
		
		// Check for ;
		if (count(explode(";", $mySql)) > 1) 
			$sql = "";
		
		if (strpos($mySql, "select") !== 0) 
			$sql = "";
		
		return $sql;
	}
 	
	function validateInsertSQL($sql) {
		
		$mySql = strtolower($sql);
		
		// Check for ;
		if (count(explode(";", $mySql)) > 1) 
			$sql = "";
		
		if (strpos($mySql, "insert") !== 0) 
			$sql = "";
		
		return $sql;
	}
  
	private function array_withitem($array){
		return (is_array($array) && count($array)>0);
	}

	private function arraySqlEscape(&$mixed){
		
		// 23-10-2008 - Denne manglede (tilføjet af dly)
		$dbh =  $this->load_database('write');
		
		if (is_array($mixed)){
			array_walk($mixed,array($this,"arraySqlEscape"));
		}
		else{
			$mixed = mysql_real_escape_string($mixed, $dbh);
		}
	}
	
	private function arrayQuote(&$mixed){
		
		if (is_array($mixed)){
			array_walk($mixed,array($this,"arrayQuote"));
		}
		else{
			$mixed = "'".$mixed."'";
		}
	}
	
	private function trace(){
		   
		$trace = debug_backtrace();
		
		$return = "";
		$i = 1;
		$func = "";
		
		foreach( $trace as $val) {
		   
			if(basename($val['file']) != basename(__FILE__)) {
		
				if($i == 1) {
					$return = basename($val['file']).' on line <b>'.$val['line'] . '</b>';
				}  
				if( 	$val['function'] == 'include' ||
						$val['function'] == 'require' ||
						$val['function'] == 'include_once' ||
						$val['function'] == 'require_once' ) {
					$func = '';
				} else {
					$func = $val['function'].'()';
				}
				if($func && $i==2) {
					$return.= " in function ".$func;
				}
				
				$i++;
				if($i > 2) {
					break;
				}
			}
		}      
		
		return $return;
	}
	
	/**
	 * Finder det rigtige antal berørte rækker
	 * eftersom MySQL ikke opdaterer rækker hvis gamle og nye værdier er ens
	 *
	 * @param resource $link
	 * @return int $intRows
	 */
	private function affected_rows(&$link) {
		$strInfo = mysql_info($link);
		$intRows = mysql_affected_rows($link);
		
		if (!$intRows) {
			preg_match('/Rows matched: (\d*)/', $strInfo, $matches);
			$intRows = intval($matches[1]);
		} // if
		
		return $intRows;
	} // function affected_rows

} // End tuksiDb klasse

?>
