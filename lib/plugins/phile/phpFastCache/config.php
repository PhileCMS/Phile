<?php
/**
 * config file for plugin
 */
$config = [
	/**
	 * Default storage engine
	 *
	 * e.g. "files": $cache = phpFastCache(); <-- will be files cache
	 *
	 * auto, files, sqlite, auto, apc, wincache, xcache, memcache, memcached,
	 */
	'storage' => 'auto',

	/**
	 * Default Path for File Cache
	 *
	 * Use full PATH like /home/username/cache
	 * Keep it blank "", it will automatic setup for you
	 */
	'path' => CACHE_DIR,

	/**
	 * Permissions for file storage
	 */
//    'default_chmod' => 0777, // For security, please use 0666 for module and 0644 for cgi.


//	"securityKey" => "auto", // default will good. It will create a path by PATH/securityKey

	/*
	 * FallBack Driver
	 * Example, in your code, you use memcached, apc..etc, but when you moved your web hosting
	 * The new hosting don't have memcached, or apc. What you do? Set fallback that driver to other driver.
	 */
//    "fallback"  => "files",

	/*
	 * .htaccess protect
	 * default will be  true
	 */
//	"htaccess"    => true,

	/*
	 * Default Memcache Server for all $cache = phpFastCache("memcache");
	 */
	/*
	"memcache"        =>  array(
		array("127.0.0.1",11211,1),
		//  array("new.host.ip",11211,1),
	),
	*/

];

return $config;
