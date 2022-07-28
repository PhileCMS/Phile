<?php

/**
 * config file for plugin
 */

$config = [
    /**
     * Default Path for File Cache
     *
     * Use full PATH like /home/username/cache
     * Keep it blank "", it will automatic setup for you
     */
    'path' => CACHE_DIR,

    /**
     * Default storage engine
     *
     * e.g. "files": $cache = phpFastCache(); <-- will be files cache
     *
     * auto, files, sqlite, auto, apc, wincache, xcache, memcache, memcached,
     */
    'storage' => 'files',

    /**
     * Additional storage options for the "files" storage:
     *
     * Common option(s) for "files":
     * - 'defaultChmod' => 0777 Change permissions on cache files. See
     *   https://www.php.net/manual/en/function.chmod.php for example values.
     */

    /**
     * Provide a custom Phpfastcache configuration object
     *
     * If used all other options are ignored.
     */
    // 'phpFastCacheConfig' => new \Phpfastcache\Config\ConfigurationOption([...]),
];

return $config;
