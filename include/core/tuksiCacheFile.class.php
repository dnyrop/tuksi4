<?php

/**
 * Cache content til file
 *
 * @package tuksiCore
 */
class tuksiCacheFile {

	function makecachefile($key) {

		$cache_file = dirname(__FILE__) . '/../../cache/';
		// Cache file
		
		$cache_file.= md5($key);

		return $cache_file;
	}

	/**
	 * Set cache by key with TTL.
	 *
	 * @param string $key
	 * @param string $content
	 * @param int $ttl
	 */
	static function set($key, $content, $ttl) {

		$cache_file = tuksiCacheFile::makeCacheFile($key, '');	
		$cache_file_tmp = $cache_file . '.' . microtime();

		// Version 2
		
		// Opening the file in read/write mode 
	    $h = fopen($cache_file,'a+'); 
	    if (!$h) throw new Exception('Could not write to cache'); 
	
	    flock($h,LOCK_EX); // exclusive lock, will get released when the file is closed 
	
	    fseek($h,0); // go to the start of the file 
	
	    // truncate the file 
	    ftruncate($h,0); 
	
	    // Serializing along with the TTL 
	    $data = serialize(array(time()+$ttl, $content)); 
	    if (fwrite($h,$data)===false) { 
	      throw new Exception('Could not write to cache'); 
	    } 
	    fclose($h);	 

	}
	
	/**
	 * Get content from cache by key
	 *
	 * @param string $key
	 */
	static function get($key) {
	
		$cache_file = tuksiCacheFile::makeCacheFile($key);
		
		
		if (!file_exists($cache_file)) 
			return false; 
		
		$h = fopen($cache_file,'r'); 
		$data = ''; 
		if (!$h) {
			return false; 
		}
		
		// Getting a shared lock  
		flock($h, LOCK_SH); 
		
		while (!feof($h)) {
			$data.=fread($h,4096); 
			
		}
			
		fclose($h); 
		
		$data = @unserialize($data); 
		if (!$data) { 
		
		 // If unserializing somehow didn't work out, we'll delete the file 
		 unlink($cache_file); 
		 return false; 
		
		} 
		
		if (!isset($data[0]) || time() > $data[0]) { 
		
		 // Unlinking when the file was expired 
		 unlink($cache_file); 
		 return false; 
		
		} 
		return $data[1]; 
		
		
		}
}
?>