<?

/**
 * This class will ensure that a shellscript can only be run once. 
 * It is usually a problem if a script is running twice at the same time. 
 * The error occurs normally through crontab, but avoided through this class.
 *
 * @uses tuksiEventlog
 * @uses tuksiIni
 * @package tuksiBase
 */
 
class tuksiShell extends tuksiEventlog {
	
	/**
	 * filename of script running
	 *
	 * @var string
	 */
	private $filename   = '';
	
	/**
	 * Path of where the lock files are created.
	 *
	 * @var string
	 */
	private $lockpath = "/tmp";
	
	/**
	 * Lock the file so as not to run several times.
	 *
	 * @var string
	 */
	private $lockfile;
	
	/**
	 * LLock the file so as not to run several times.
	 *
	 * @param int $eventtypeid Eventypeid from tabel cmseventtype. (check tuksiEventlog)
	 * @param int $debugmode Debugmode 0, 1 or 2. (check tuksiEventlog)
	 */
	
	function __construct($eventtypeid, $debugmode = 0, $email = '') {
			
		$arrIni = tuksiIni::getInstance();
		
		parent::__construct($eventtypeid, $debugmode, $email);
				
		// Get the scripts PID
		$this->pid = getmypid ();
		
		// Get script filename 
		$this->filename = $_SERVER['PHP_SELF'];
		$this->filename = str_replace('/', '_', $this->filename);
		
		// Set lock filename
		$this->lockfile = $this->lockpath . "/" . $this->filename . ".LOCK";

		//$this->log('Lockfile: ' . $this->lockfile);
		
		// Checking if the script running, otherwise lock file.
		$this->lock();
	
	} // End __construct()
	
	/**
	 * Function that makes a lock under $this->lockpath, if it doesnt not exist
	 *
	 */
	function lock() {
	
		$lock_file = 1;
		if (file_exists($this->lockfile)) {
			$fh	=	fopen($this->lockfile,'r');
			$pid 	= 	fgets($fh);
		
			// Alle processer har en mappe (PID) under /proc
			$pid_path = "/proc/" . $pid;
			if (file_exists($pid_path)) {
				$lock_file = 0;
				exit();
			} 	     
		} // END lock file exists
		
		if ($lock_file) {
			$this->log("Shell", "File locked..");
		
			// Making lock file and writing PID number in file
			$fh=	fopen($this->lockfile,'w');
			fwrite($fh, $this->pid);
			fclose($fh);
		
		} // End lock file
		
	} // End lock()
	

	/**
	 * Unlock file and print debuginformation if debugmode = 2;
	 *
	 */
	function end() {
		$this->log("File unlocked..");
	 
	 	// Unlocking file again
		unlink($this->lockfile);

		// Ending eventlog too
		parent::end();
		
	} // End end();

} // End tuksi_shell

?>
