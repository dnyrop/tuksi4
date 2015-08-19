<?

/**
 * tuksiEventlog.
 * 
 * Used for logging different things til cmseventlog
 * 
 * Most used with tuksiShell.
 *
 * @uses tuksiDB
 * @uses tuksiIni
 * @package tuksiBase
 */
class tuksiEventlog {
	private $eventname = '';
	private $email;
	private $logpath;
	private $log = '';
	private $errorlog = '';
	private $statusid = 0;
	private $debugmode = 0;
	private $starttime;
		
	/**
	 * Enter description here...
	 *
	 * debugmode = 1 : No e-mail.
	 * debugmode = 2 : No DB inserts and e-mails.
	 * 
	 * statusid = 1: Notice (Inserts log into DB)
	 * statusid = 2: Error (Inserts log into DB and mail to defined email)
	 * 	
	 * @param int $eventtypeid Row id from cmseventtype
	 * @param int $debugmode
	 * @param string $email Overrides e-mail in tuksiIni -> debug -> email
	 */
	function __construct($eventtypeid, $debugmode = 0, $email = '') {
		
		$this->starttime = microtime(true);
		
	    $this->eventtypeid = $eventtypeid;
	    $this->debugmode = $debugmode;
	     
	    if ($email <> '') {
	    	$this->email = $email;
	    } else {
	    	$arrIni = tuksiIni::getIni();
	    	$this->email = $arrIni['debug']['email'];
	    }
	    
	} // end __construct();

	/**
	 * Run this at the end of the script.
	 * 
	 * Inserts log into database if statusid = 1 || 2.
	 * Mails if statusid = 2.
	 * 
	 * Prints log if debugmode = 2.
	 *
	 * @param string $log
	 * @param int $statusid
	 */
    function end($log= "", $statusid = 0) {

		if ($log)
			$this->log($log, $statusid);
		
		if (isset($this->logerror)) {
	      $this->logtmp = "Errors found\n";
	      $this->logtmp.= "-----------------\n";
	      $this->logtmp.= $this->logerror;
	      $this->logtmp.= "\n-----------------\n";
	      $this->logtmp.= $this->log;
	      $this->log = $this->logtmp;
		}

        if ($this->statusid > 0 && (!$this->debugmode || $this->debugmode == 1) ) {
        	
        	$objDB = tuksiDB::getInstance();
        	
            $sqlInsert = "INSERT INTO cmseventlog (cmseventlogtypeid, dateadded, content, statusid) ";
            $sqlInsert.= " VALUES ('{$this->eventtypeid}',now(), '" . mysql_escape_string($this->log) . "','{$this->statusid}')";
    
            $arrReturn = $objDB->write($sqlInsert);

            if ($this->logpath) {

                $date_month = date("Ym");

                @mkdir($this->logpath . "/" . $this->eventtypeid);
                $logfile = $this->logpath . "/" . $this->eventtypeid . "/" . $date_month . ".log";

                $arrLines = explode("\n", $this->log);
                $fh = fopen($logfile, "a+");
                foreach ($arrLines as &$line) {
                        $line = date("Ymd His") . " {$this->statusid} " . $line . "\n";
                        fwrite($fh, $line);
                }
                fclose($fh);

            }
        }
        if ($this->email && ($this->statusid > 1) && !$this->debugmode) {
        	
    		$objDB = tuksiDB::getInstance();
    		
            $sql = "SELECT name FROM cmseventlogtype WHERE id = '{$this->eventtypeid}'";
            $arrReturn = $objDB->fetch($sql);
            
            if ($arrReturn['num_rows'] > 0) {
            	$eventname = $arrReturn['data']['name'];
            } else {
            	$eventname = '';
            }
            
            mail($this->email, $this->eventname, $this->log);
        }

		if ($this->debugmode)
			print $this->log;
    } // end end();

    /**
     * Log an event
     *
     * @param string $log
     * @param int $statusid
     */
    function log($log, $statusid = 0) {
    	$time_used =  round(microtime(true) - $this->starttime, 3);
    	
    	
		if ($statusid == 2) {
			$this->logerror .= "[" . count(explode("\n", $this->log)) . "] " . $log. "\n";
			$logentry= "[ERROR] " . $time_used . ' ' . $log. "\n";
		} else
			$logentry = $time_used . ' ' . $log. "\n";
		
		$this->log .= $logentry;
		
		if ($this->debugmode == 1) {
			print $logentry;
		}
		
		if ($statusid > $this->statusid)
			$this->statusid = $statusid;
    } // End log();
} // End class tuksiEventlog

?>
