<?php

$config = array(
	/*
	 * Default storage
	 * if you set this storage => "files", then $cache = phpFastCache(); <-- will be files cache
	 */
	"storage"   =>  "auto", // files, sqlite, auto, apc, wincache, xcache, memcache, memcached,

	/*
	 * Default Path for Cache on HDD
	 * Use full PATH like /home/username/cache
	 * Keep it blank "", it will automatic setup for you
	 */
	"path"      =>  ROOT_DIR . "temp/cache/" , // default path for files
	"securityKey"   =>  "", // default will good. It will create a path by PATH/securityKey

	/*
	 * FallBack Driver
	 * Example, in your code, you use memcached, apc..etc, but when you moved your web hosting
	 * The new hosting don't have memcached, or apc. What you do? Set fallback that driver to other driver.
	 */
	"fallback"  =>  array(
		"example"   =>  "files",
		"memcache"  =>  "files",
		"apc"       =>  "sqlite",
	),

	/*
	 * .htaccess protect
	 * default will be  true
	 */
	"htaccess"  =>  true,

	/*
	 * Default Memcache Server for all $cache = phpFastCache("memcache");
	 */
	"server"        =>  array(
		array("127.0.0.1",11211,1),
		//  array("new.host.ip",11211,1),
	),
);

return $config;