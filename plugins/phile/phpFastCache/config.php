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
    'storage' => 'files',

    /**
     * Default Path for File Cache
     *
     * Use full PATH like /home/username/cache
     * Keep it blank "", it will automatic setup for you
     */
    'path' => CACHE_DIR,

    /**
     * Permissions for file storage
     *
     * For security, please use 0666 for module and 0644 for cgi.
     */
    // 'default_chmod' => 0777,

    /**
     * default will good. It will create a path by PATH/securityKey
     */
    // "securityKey" => "auto",

    /**
     * Default Memcache Server for Memcache
     */
    /*
    'servers' => [
        [
            'host' =>'127.0.0.1',
            'port' => 11211,
              // 'sasl_user' => false, // optional
              // 'sasl_password' => false // optional
        ]
    ],
     */
];

return $config;
