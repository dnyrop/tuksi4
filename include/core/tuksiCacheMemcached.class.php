<?php

/**
 * Memcached class for caching content with xcache.
 *
 * @package tuksiCore
 */

class tuksiCacheMemcached {

	
	static private $instance = null;
	private $objMemcache = null;

	public function __construct() {
	  $this->objMemcache = new Memcache;
		$this->connect();
	
	}
	
	/**
	 * Set cache by key with TTL.
	 *
	 * @param string $key
	 * @param mixed $content
	 * @param int $ttl
	 */
	static function set($key, $content, $ttl, array $arrOptions = array()) {
		$objCache = tuksiCacheMemcached::getInstance();
		
		if(array_key_exists('flag', $arrOptions)) {
			$arrSetCacheArray = array('key' => $key, 'var' => $content, 'expire' => $ttl, 'flag' => $arrOptions['flag']);
		} else {
			$arrSetCacheArray = array('key' => $key, 'var' => $content, 'expire' => $ttl);
		}

		$objCache->setCache($arrSetCacheArray);
	}
	
	/**
	 * Get content from cache by key
	 *
	 * @param string $key
	 */
	static function get($key) {
		$objCache = tuksiCacheMemcached::getInstance();
		return $objCache->getCache(array('key' => $key));
		
	}

    // this implements the 'singleton' design pattern.

	static public function getInstance() {   

        if (is_null(self::$instance)) {

            $c = __CLASS__;

            self::$instance = new $c;

        } // if

        return self::$instance;

    } // getInstance


	/**
	 * sets cache item
	 * returns boolean status
	 * 
	 * $arrCache = array($key,$data,$flag,$expire)
	 */
	public function setCache($arrCache = array()) {

		// check for disabled cache
		if($this->strEnv == "dev" && $this->arrSettings["dev"]['disable_cache'] == 1) {
		
			return;
			
		} 
		
		// key and var must be set
		if( strlen( $arrCache['key'] ) > 4 && isset($arrCache['var']) && intval( $arrCache['expire'] ) ){
		
			// check expire value
			if($arrCache['expire'] < 10) {
			
				$arrCache['expire'] = 10;
				
			}

			$strCachekey = $arrCache['key'] . " @ ".$this->strHost;
			$arrCachekey = array($arrCache['key'] . " @ ".$this->strHost);
			if (!isset($arrCache['flag'])) {
				$arrCache['flag'] =0;
			}
			
			return $this->objMemcache->set  ( $strCachekey , $arrCache['var'] , $arrCache['flag'] , $arrCache['expire'] );
		
		} else {
			
			// $arrCache['key'] && $arrCache['var'] && $arrCache['expire'] was not set
			return 0;
		
		} // if
		
	}


	/**
	 * gets cache item
	 * returns array
	 */
	public function getCache($arrCache = array()) {

		// check for disabled cache
		if($this->strEnv == "dev" && $this->arrSettings["dev"]['disable_cache'] == 1) {
		
			return;
			
		} // if

		// keys must be set
		if($arrCache['key']) {
			
			$strCachekey = $arrCache['key'] . " @ ".$this->strHost;

			return $this->objMemcache->get  ( $strCachekey );
		
		} else {
			
			return 0;
			
		} // if

	}

	/**
	 * gets cache item
	 * returns boolean status
	 */
	public function deleteCache($arrCache = array()) {
	
		if($arrCache['key']) {

			$strCachekey = $arrCache['key'] . " @ ".$this->strHost;
			
			return $this->objMemcache->delete  ( $strCachekey );
			
		} else {
		
			return 0;
			
		} // if
	}
	

	/**
	 * marks all cahce items as expired
	 * returns boolean status
	 */
	public function flushCache() {
	
		return $this->objMemcache->flush ();
		
	}
	
	
	/**
	 * gets cache item
	 * returns boolean status
	 *
	 * $arrCache['type'] can be: {reset, malloc, maps, cachedump, slabs, items, sizes}
	 */
	public function getExtendedStats($arrStats = array()) {
	
		// type must be set
		if( $arrStats['type'] ) {
		
			return $this->objMemcache->getExtendedStats  ( $arrStats['type'] );
			
		} else {
		
			return 0;
			
		} // if
	}


	/**
	 * connects to cache cluster
	 * returns boolean status
	 */
	public function connect() {
	
		// get settings
		$this->arrSettings = parse_ini_file(dirname(__FILE__)."/../../configuration/memcache.ini", true);

		$this->strHost = $_SERVER['HTTP_HOST'];
				
		// get valid hosts
		$valid_hosts = explode(",",$this->arrSettings['prod']['valid_hosts']);
		
		// check enviroment so the right cacheserver is used
		if( in_array( $this->strHost , $valid_hosts ) ) {
		
			$this->strEnv = "prod";
		
		} else {

			$this->strEnv = "dev";
		
		} // if
		
		// get servers
		$this->servers = explode(",",$this->arrSettings[$this->strEnv]['servers']);
				
		foreach($this->servers as $server) {
		
			$this->objMemcache->addServer  ( $server );
		
		} // foreach
		
	}


	// code below this point is not in use

	// todo: close connection

	/**
	 * closes connection to cache cluster
	 * returns boolean status
	 */
	private function closeConnection() {

		foreach($this->servers as $server) {
		
			$this->objMemcache->close( $server );
		
		} // foreach

		return;
		
	}

}
?>
