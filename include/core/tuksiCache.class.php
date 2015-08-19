<?php

/**
 * Caching factory class
 * 
 * Uses configuration/cache.ini to deside with caching system to use.
 * 
 * File and Xcache system is implemented.
 * 
 * @package tuksiCore
 *
 */
class tuksiCache {

	static $cache_on;
	static $cache_type;

	function makecachefile($key) {

		$cache_file = dirname(__FILE__) . '/../../cache/';
		// Cache file
		
		$cache_file.= md5($key);

		return $cache_file;
	}

	static function checkCacheStatus() {

		if (!isset(self::$cache_on)) {
			$arrIni = tuksiIni::getIni();

			self::$cache_on = $arrIni['cache']['active'];
			self::$cache_type = $arrIni['cache']['type'];
			tuksiDebug::log("Check cache (" . self::$cache_type ."): " . self::$cache_on);
		}

		return self::$cache_on;
	}

	/**
	 * Set cache by key with TTL.
	 *
	 * Factory for choosen caching system
	 * 
	 * @param string $key
	 * @param string $content
	 * @param int $ttl
	 */
	static function set($key, $content, $ttl, array $arrOptions = array()) {

		if (!self::checkCacheStatus())
			return;

		// Sorry 5.3.0
		// $class = 'tuksi_cache_' . self::$cache_type;
		//return call_user_func($class .'::set', $key, $content, $ttl);
		
		switch (self::$cache_type) {
			case('memcached') : 
											$arrIni = tuksiIni::getIni();
											$prefix = $arrIni['cache']['key_prefix'];
										
											tuksiCacheMemcached::set($prefix . '_' . $key, $content, $ttl, $arrOptions); break;
			case('xcache') : 
											$arrIni = tuksiIni::getIni();
											$prefix = $arrIni['cache']['key_prefix'];
										
											tuksiCacheXcache::set($prefix . '_' . $key, $content, $ttl); break;
			case('file') : tuksiCacheFile::set($key, $content, $ttl); break;
		}

	}

	/**
	 * Get content from cache by key
	 *
	 * Factory for choosen caching system
	 * 
	 * @param string $key
	 */
	static function get($key) {
		if (!self::checkCacheStatus())
			return;

	
		switch (self::$cache_type) {
			case('memcached') : 
											$arrIni = tuksiIni::getIni();
											$prefix = $arrIni['cache']['key_prefix'];
										
											return tuksiCacheMemcached::get($prefix . '_' . $key); break;
			case('xcache') : 
											$arrIni = tuksiIni::getIni();
											$prefix = $arrIni['cache']['key_prefix'];
										
											return tuksiCacheXcache::get($prefix . '_' . $key); break;
			case('file') : return tuksiCacheFile::get($key); break;
		}
		return '';
		
	}
}
?>
