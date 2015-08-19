<?php

/**
 * Xcache class for caching content with xcache.
 *
 * @package tuksiCore
 */

class tuksiCacheXcache {

	/**
	 * Set cache by key with TTL.
	 *
	 * @param string $key
	 * @param mixed $content
	 * @param int $ttl
	 */
	static function set($key, $content, $ttl) {
		if (PHP_SAPI == 'cli') return true;
		return xcache_set($key, $content, $ttl);
	}
	
	/**
	 * Get content from cache by key
	 *
	 * @param string $key
	 */
	static function get($key) {
		if (PHP_SAPI == 'cli') return null;
		return xcache_isset($key) ? xcache_get($key) : null;
	}

	/**
	 * Unsets cache stored with $prefix
	 * 
	 * @param string $prefix 
	 * 
	 * @return bool success
	 */
	static function unset_by_prefix($prefix) {
		return xcache_unset_by_prefix($prefix);
	}
}
?>
