<?php

/**
 * Class for getting files in a directory
 * 
 * With filter function
 *
 * @package tuksiBase
 */
class tuksiReadDir {

	/**
	 * Directory to search
	 *
	 * @var string
	 */
	public $directory;
	
	/**
	 * files found in directory
	 *
	 * @var array
	 */
	var $files 			= Array();
	
	/**
	 * Errors found
	 *
	 * @var array
	 */
	var $errors 		= Array();
	
	/**
	 * Whitelist filter
	 *
	 * @var array
	 */
	var $filtersLike	= Array();
	
	/**
	 * Blacklist filter
	 *
	 * @var array
	 * 
	 */
	var $filtersNotLike	= Array();
	
	/**
	 * Enter description here...
	 *
	 * @param string $directory Directory that has the files
	 */
	function __construct($directory) {
		
		// validate directory
		if(is_dir($directory)) {
			// making a directory object and setting it in the object
			$this->directory = $directory;	
		} else {
			// setting error
			$this->error[] = "Not a valid directory";
		}
	}
	
	/**
	 * function for adding filter for positive	
	 *
	 * @param string $filterRegex
	 */
	function addFilterLike($filterRegex) {
		
		// adding filter
		$this->filtersLike[] = $filterRegex;
	}
	
	/**
	 * function for adding filter for positive	
	 *
	 * @param string $filterRegex
	 */
	function addFilterNotLike($filterRegex) {
		
		// adding filter
		$this->filtersNotLike[] = $filterRegex;
	}

	/**
	 * Apply filer on file
	 *
	 * @param string $file
	 * @return unknown
	 */
	function executeFilters(&$file) {
		
		$filterOk 	= 1;
			
		// looping LIKE filters
		foreach($this->filtersLike AS $key => $val) {
			if($filterOk) {
				if(!preg_match($val, $file)) {
					$filterOk = 0;
				}
			}
		}
		
		if($filterOk) {
			// looping NOTLIKE filters
			foreach($this->filtersNotLike AS $key => $val) {
				if($filterOk) {
					if(preg_match($val, $file)) {
						$filterOk = 0;
					}
				}
			}
		}
		
		return $filterOk;
	}

	/**
	 * Get files
	 * 
	 * @param array $donefiles
	 */
	function filter($donefiles) {
		
		// setting tempdir
		$tempDir = str_replace("//","/",$this->directory . "/");
		
		// setting directory object		
		$objDir = opendir($tempDir);
		// getting files in dir
		while (false !== ($node = readdir($objDir))) {	
            // is file
			if (is_file($tempDir.$node)) {
				
				// check file with filters.
				if($this->executeFilters($node)) {
					
					// only done files?
					if($donefiles) {
						
						if(preg_match("/\.done/", $node)) {
							
							// tempfilename 
							$tempFile = preg_replace("/\.done/", "", $node);
							// does it exist?
							if(is_file($tempDir.$tempFile)) {
								
								$this->files[] = $tempFile;
							}
						}
						
					} else {
						$this->files[] 	= $node;
					}
				}
			}
		}
		
		// closing dir
		closedir($objDir);
	} // End filter();

	// 
	/**
	 *  function for sorting by time
	 *
	 * @param bool $desc 0 is oldest first, 1 is newest first.
	 */
	function sortByTime($desc = 0) {
		
		// making temp sorted array
		$tempSortedArr = Array();

		// looping file
		foreach($this->files AS $key => $val) {
			
			if(is_file(str_replace("//","/",$this->directory . "/" . $val))) {
				// the idea is to set the timestamp of the file as the array key.
				$tempSortedArr[filemtime(str_replace("//","/",$this->directory . "/" . $val)) . $val] = $val;
			}
		}
		
		// sorting by keys
		ksort($tempSortedArr);	
		
		// setting array to files array and removing time keys
		$this->files = array_values($tempSortedArr);

		if($desc) {
			$this->files = array_reverse($this->files);
		} 
 	} // End sortByTime();

	/**
	 * function for getting filenames in array
	 *
	 * @param bool $donefiles Must be .done file for each file
	 * @param bool $desc 0 is oldest first, 1 is newest first.
	 * @return array
	 */
	function getFileNames($donefiles = 0,$desc = 0) {
	
		// executing filters	
		$this->filter($donefiles);
		
		// sorting
		$this->sortByTime($desc);
		
		// returning
		return $this->files;
	} // end getFileNames();
	
	/**
	 * function for getting full filenames in array
	 *
	 * @param bool $donefiles Must be .done file for each file
	 * @param bool $desc 0 is oldest first, 1 is newest first.
	 * @return array
	 */
	
	function getFileNamesWithPath($donefiles = 0,$desc = 0) {
	
		// executing filters	
		$this->filter($donefiles);
		
		// sorting
		$this->sortByTime($desc); 
		
		// adding path to filenames
		foreach($this->files AS $key => $val) {
			
			$this->files[$key] = str_replace("//","/",$this->directory . "/" . $val);
		}
		
		// returning
		return $this->files;
	} // End getFileNamesWithPath();

} // End tuksiReadDir()

?>