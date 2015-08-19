<?php

/**
 * Enter description here...
 *
 * @package tuksiBase
 */

class tuksiFtp extends tuksiReadDir {
		
		#######
		# Defining variables
		#######

		var $ftpHost;
		var $ftpHostPath;
		var $ftpLocalPath;
		var $ftpUsername;
		var $ftpPassword;
		var $ftpPassive;
		var $ftpConnId;
		var $ftpConn;
		var $debug 			= Array("debug" => Array(), "error" => Array());
		
		#######	
		# Constructor
		#######
		function __construct() {
			
		}
		
		#######
		# connection functions
		######
		
		function con_validate() {
			
			$boolOk = 1;
			
			// checking paths and connection
			if(!$this->ftpHost) { $this->debug("No host",1); $boolOk = 0; }
			
			if(!$this->ftpUsername) { $this->debug("No username",1); $boolOk = 0; }
			
			if(!$this->ftpPassword) { $this->debug("No password",1); $boolOk = 0; }
			
			return  $boolOk;
		}
		
		// function for setting remote host
		function setHost($ftpHost,$ftpUsername,$ftpPassword,$ftpPassive = 1) {
			
			$this->ftpHost 		= $ftpHost;
			$this->ftpUsername 	= $ftpUsername;
			$this->ftpPassword 	= $ftpPassword;
			$this->ftpPassive 	= $ftpPassive;
			
		}
		
		function connect() {
			
			if($this->con_validate()) {
			
				// adding debug
				$this->debug("Connecting to " . $this->ftpHost);
				
				// making connection
				$this->ftpConnId = @ftp_connect($this->ftpHost);
				
				// validation connection id
				if($this->ftpConnId) {
					
					// adding debug
					$this->debug("Logging in");
					
					// logging in
					$this->ftpConn = @ftp_login($this->ftpConnId, $this->ftpUsername, $this->ftpPassword);
					
					// validation connection
					if($this->ftpConn) {
						
						// adding debug
						$this->debug("Setting passivemode to " . $this->ftpPassive);
						
						// setting passive mode
						ftp_pasv($this->ftpConnId, $this->ftpPassive);
						
					} else {
						// adding error
						$this->debug("Could not login",1);
					}
					
				} else {
				
					// adding error
					$this->debug("Host not found",1);
				}
			}
		}
		
		function disconnect() {
		
			if($this->ftpConnId) {
				ftp_close($this->ftpConnId);
			}
		}
		
		#######
		# Paths functions
		#######
		
		// function for setting remote path
		function setHostPath($ftpHostPath) {
			
			$this->ftpHostPath = $ftpHostPath;
			
		}
		
		// function for setting local path
		function setLocalPath($ftpLocalPath) {
			
			parent::__construct($ftpLocalPath);
			$this->ftpLocalPath = $ftpLocalPath;
		}
		
		#######
		# Transfer functions
		#######
		
		// validate function for transferring
		function ftp_validate() {
			
			$boolOk = 1;
			
			// checking paths and connection
			if(!$this->ftpHostPath) { $this->debug("No path at host",1); $boolOk = 0; }
			
			if(!$this->ftpLocalPath) { $this->debug("No local path",1); $boolOk = 0; }
			
			if(!$this->ftpConnId) { $this->debug("No connection",1); $boolOk = 0; }
			
			if(!$this->ftpConn) { $this->debug("Could not connect",1); $boolOk = 0; }
			
			return  $boolOk;
		}
		
		// function for putting files to host
		function ftpPut($trashFolder = "", $doneTag = 1, $cleanUp = 1) {
			
			// connectiong
			$this->connect();
			
			// validation
			if($this->ftp_validate()) {
			
				// getting array with files to transfer
				$tempFilesWithPath 	= $this->getFileNamesWithPath();
				$tempFiles 			= $this->getFileNames();
				
				foreach($tempFilesWithPath AS $key => $val) {
					// transferring
					if(ftp_put($this->ftpConnId, str_replace("//","/",$this->ftpHostPath . "/" . $tempFiles[$key]), $val, FTP_ASCII)) {
						
						// adding debug
						$this->debug("Transfer $val OK");
						
						// addning file with donetag
						if($doneTag) {
						
							// creating temp file
							$tempFile = tempnam("/tmp", $tempFiles[$key]);
														
							if(ftp_put($this->ftpConnId, str_replace("//","/",$this->ftpHostPath . "/" . $tempFiles[$key] . ".done"), $tempFile, FTP_ASCII)) {
								$this->debug("{$val}.done created");
							} else {
								$this->debug("{$val}.done could not be created",1);
							}
							
							// removing tempfile
							unlink($tempFile);
						}
						
						// should we move to trash
						if(is_dir($trashFolder)) {
							
							if($cleanUp) {
								
								// copy file
								if(@copy($val,  str_replace("//","/",$trashFolder . "/" . $tempFiles[$key]))) {
									$this->debug("$val moved to $trashFolder");
								} else {
									$this->debug("$val could not be moved to trash", 1);
								}
							}
							
						} else if($trashFolder) {
							$this->debug("Can't find trash folder",1);
						}
						
						// should we clean up?
						if($cleanUp) {
							
							if(@unlink($val)) {
								$this->debug("source $val deleted");
							} else {
								$this->debug("could not delete $val",1);
							}
							
							if($doneTag) {
								if(@unlink($val . ".done")) {
									$this->debug("source {$val}.done deleted");
								} else {
									$this->debug("could not delete {$val}.done",1);
								}
							}
						}
						
					} else {
						
						// adding error
						$this->debug("Transfering $val failed", 1);
					}
				}
			}
			
			// disconnecting
			$this->disconnect();
		}
		
		#######
		# Debug
		######
		
		// Function for adding errors
		function debug($string, $error = 0) {
			
			if($error) {
				// adding error
				$this->addError($string);
			} else {
				// adding debug
				$this->addDebug($string);
			}
		}
		
		// Function for adding errors
		function addError($errorString) {
			
			// adding error
			$this->debug["error"][] = $errorString;
		}
		
		// Function for checking if any errors
		function error() {
			
			// are there any errors
			return count($this->debug["error"]);
		}
		
		// Function for adding errors
		function addDebug($string) {
			
			// adding debug
			$this->debug["debug"][] = $string;
		}
		
		// Function for adding errors
		function showDebug() {
			
			print "<b>Debug:</b><br>";
			
			// printing debug
			foreach($this->debug["debug"] AS $key => $val) {
				print "&nbsp;&nbsp;$val<br>";
			}
			
			print "<b>Error:</b><br>";
			
			// printing errors
			foreach($this->debug["error"] AS $key => $val) {
				print "&nbsp;&nbsp;$val<br>";
			}
		}
	}

?>
