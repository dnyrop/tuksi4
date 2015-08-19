<?php
/**
 * Tuksi standard klasse
 *
 * @package tuksiCore
 */

class tuksiDebug {
	
	static private $instance;
	
	private $arrLog = array();
	private $arrWarning = array();
	private $arrError = array();
	private $arrSQL = array();
	private $arrTpl = array();
	private $errorlog = false;
	private $timer = true;
	private $starttime;
	private $SQLExecTime = 0;
	private $tplExecTime = 0;
	private $isActive = false;
	
	function __construct() {
		
		if(tuksiIni::$arrIni['debug']['active']){
			$this->isActive = true;	
		} else {
			$this->isActive = false;	
		}
		
		$this->starttime = $this->getTime();
	
	}
	
	static function getInstance() {
		if (!self::$instance){
			self::$instance = new tuksiDebug();
		}
		return self::$instance;
	}
	

	static function add($error) {
		$objDebug = tuksiDebug::getInstance();

		$objDebug->warning($error);
	}

	public function isActive(){
		return $this->isActive;
	}
	
	public function disable(){
		$this->isActive = false;
	}
	public function enable(){
		$this->isActive = true;
	}
	
	static function log($message,$name = "") {
		$objDebug = tuksiDebug::getInstance();

		$objDebug->addLog($message, $name);

	}
	
	public function addLog($message,$name = "") {
		if($this->isActive) {
			$this->arrLog[] = $this->getLogItem($message,$name);
		}	
	}
	
	static function warning($message,$name = "") {
		$objDebug = tuksiDebug::getInstance();

		$objDebug->addWarning($message, $name);

	}
	
	public function addWarning($message, $name = "") {
		if($this->isActive) {
			$this->arrWarning[] = $this->getLogItem($message,$name);
		}
	}
	
	public function addRawWarning($arrData) {
		if($this->isActive) {
			$this->arrWarning[] = $arrData;
		}
	}
	
	
	public function hasWarning(){
		if(count($this->arrWarning) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	static function error($message,$name = "",$sql = false) {
		
		$objDebug = tuksiDebug::getInstance();

		$objDebug->addError($message, $name,true,$sql);
	}
	
	public function addError($message,$name = "",$sql = false) {
		if($this->isActive) {
			$this->arrError[] = $this->getLogItem($message,$name,$sql);
		}
	}
	
	public function hasError(){
		if(count($this->arrError) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function sql($arrData) {
		if($this->isActive) {
			$this->SQLExecTime+= $arrData['exectime'];
			$arrData['time'] = round($this->getTime() - $this->starttime,5);
			$arrData['exectime'] = round($arrData['exectime'],5);
			//try to make the SQL look nice
			if($arrData['sql']) {
				$arrData['sql'] = preg_replace("/( from | where | inner\ join | order )/i","<br>$1",$arrData['sql']);
				$arrData['sql'] = preg_replace("/(select | from | where | inner | join | order by | outer | sql_no_cache | as | and )/i"," <strong>$1</strong> ",$arrData['sql']);
			}
			$this->arrSQL[] = $arrData;
			
			if(count($arrData['alert']) > 0) {
				$this->addRawWarning($arrData);
			}
		}
	}
	public function tpl($tplname,$startTime,$execTime){
		if($this->isActive) {	
			$this->tplExecTime+= $execTime;
			$arrData['time'] = round($this->getTime() - $this->starttime,5);
			$arrData['exectime'] = round($execTime,5);
			$arrData['trace'] = $this->trace();
			$arrData['name'] = $tplname;
			$this->arrTpl[] = $arrData;
		}
	}

	static function eventlog($typeid,$cmsuserid,$tablename = '',$relationid=0,$contentAreaID=0,$comment=""){
	
		$objDB = tuksiDB::getInstance();	
		$sqlIns="INSERT INTO cmseventlog (cmseventlogtypeid,cmsuserid,relationid,tablename,value1,content,dateadded) ";
		$sqlIns.=" VALUES ('".$typeid."','".$cmsuserid."','".$relationid."','".$tablename."','".$contentAreaID."','".$comment."',NOW()) ";
		$objDB->write($sqlIns);
		
	}	
	
	function addTimerToDebug() {

		if ($this->timer) {
			$endtime = $this->getTime();
			$this->totalTime = $endtime - $this->starttime;
			$this->log($endtime - $this->starttime,"Time used");
		}
	}
	
	/**
	 * Afslutter Tuksi_debug ved at sende e-mail og/eller geme debug data til database.
	 *
	 */
	public function end() {
		if($this->isActive) {
			$this->log($this->SQLExecTime,"Total time used on SQLs");
			$this->addTimerToDebug();
			$this->checkDuplicateSQL();
		}
		//insert function to e-mail on errors when in production env
		
	} // End end();
	
	private function checkDuplicateSQL(){
		
		$arrUni = array();

		$arrMulti = array();
		foreach($this->arrSQL as $arrSql) {
			
			$md5 = md5($arrSql['sql']);
			
			if(isset($arrUni[$md5])) {
				if(isset($arrMulti[$md5])) {
					$arrMulti[$md5]['nb']+= 1;
					$arrMulti[$md5]['trace'][] = $arrSql['trace'];
				} else {
					$arrMulti[$md5] = array('nb' => 2,
																	'sql' => $arrSql['sql'],
																	'trace' => array($arrUni[$md5]['trace'],$arrSql['trace']));
				}
			} else {
				$arrUni[$md5] = $arrSql;
			}
			//$strSQL.=  $arrSql['trace'] . "<br>" . $arrSql['sql']."<hr>";
		}
		//$strMulti = "";
		
		if (isset($arrMulti) && count($arrMulti)) {
			foreach ($arrMulti as $arr) {
				$arrData = array(	'name' => 'SQL occurs more than once (' . $arr['nb'] . ')',
													'trace' => "<br />" . join("<br />",$arr['trace']),
													'message' => $arr['sql']);
				
				$this->addRawWarning($arrData);
				
				//$strMulti.=  "Count: ".$arr['nb']."<br>".$arr['sql']."<br>".join("<br>",$arr['trace'])."<hr>";
			}
		}
	}
	
	public function fetch() {
		
		if($this->isActive) {
		
			$this->end();
			
			$arrAll = array(	'log' => $this->arrLog,
									'sql' => $this->arrSQL,
									'warning' => $this->arrWarning,
									'error' => $this->arrError,
									'tpl' => $this->arrTpl,
									'info' => array(		'num_warnings' => count($this->arrWarning),
															'num_errors' => count($this->arrError),
															'num_sql' => count($this->arrSQL),
															'num_tpl' => count($this->arrTpl),
															'time_sql' => $this->SQLExecTime,
															'time_tpl' => $this->tplExecTime,
															'time_total' => $this->totalTime));
			
			return $arrAll;
		}
	}
	
	private function getLogItem($message,$name = "",$sql = false) {
		
		if ($this->errorlog) {
			error_log($name . " : " . $data);
		}
		
		if ($this->timer) {
			$time = round($this->getTime() - $this->starttime,5);
		}
		
		$arrLogItem = array(	"name" => $name, 
													"message" => $message, 
													"time" => $time, 
													"trace" => $this->trace($sql),
													"line" => count($this->arrLog) + 1);
									
									
		return $arrLogItem;							
	}
	
	public function getTime() {
		$timeparts = explode(" ",microtime());
		return $timeparts[1].substr($timeparts[0],1);
	}
	
	private function trace($sql = false) {
		   
	   $trace = debug_backtrace();
	   
	   $return = "";
	   $i = 1;
	   $func = "";
	   
	   foreach( $trace as $val) {
	   	
	   	if(basename($val['file']) != basename(__FILE__)) {
	   		
	   		if($sql) {
	   			if($val['class'] == 'tuksiDebug' || $val['class'] == 'tuksiDBMysql' || $val['class'] == 'tuksiDB_superclass') {
	   				continue;
	   			}
	   		}
	   		
	   		if($i == 1) {
	   			$return = basename($val['file']).' on line <b>'.$val['line'] . '</b>';
	   		}  
				
	   		if( $val['function'] == 'include' ||
						$val['function'] == 'require' ||
						$val['function'] == 'include_once' ||
						$val['function'] == 'require_once' ) {
					$func = '';
	   		} else {
					$func = $val['function'].'()';
				}
				if($func && $i==2) {
	   			$return.= " in function ".$func;
	   			break;
	   		}
				$i++;
				if($i > 2) {
					break;
				}
	   	}
	   }      
	   return $return;
	}
}
?>
