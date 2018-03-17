<?php
/**
 * Phile default setup
 *
 * Don't do changes here but overwrite them in your local config.
 *
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */
$config = [];

/**
 * Base URL to Phile installation without trailing slash
 *
 * e.g. `http://example.com` or `http://example.com/phile`
 *
 * Default: try to resolve automatically in Router
 */
$config['base_url'] = (new Phile\Core\Router)->getBaseUrl();

/**
 * page title
 */
$config['site_title'] = 'PhileCMS';

/**
 * default theme
 */
$config['theme'] = 'default';

/**
 * date format as PHP date format
 */
$config['date_format'] = 'jS M Y';

/**
 * Set page order
 *
 * Format <type>.<attribute>:<order>
 * type:
 * - "page" page attribute
 * - "meta" page meta attribute
 * order:
 * - "asc" (default) ascending order
 * - "desc" descending order
 *
 * Orders are chainable to for sub-ordering.
 *
 * Examples:
 * - "meta.title" sort by meta title ascending
 * - "page.folder:asc meta.date:desc" sort by folder-name first and by date withing folders
 */
$config['pages_order'] = 'meta.title:desc';

/**
 * timezone
 */
$config['timezone'] = (ini_get('date.timezone')) ? ini_get('date.timezone') : 'UTC';

/**
 * charset used for HTML and Markdown files
 */
$config['charset'] = 'utf-8';

/**
 * set PHP error reporting
 */
$config['display_errors'] = 0;

/**
 * content directory
 */
$config['content_dir'] = ROOT_DIR . 'content' . DS;

/**
 * extension of content files
 */
$config['content_ext'] = '.md';

/**
 * Not found page.
 */
$config['not_found_page'] = '404';

/**
 * include core plugins
 */
$config['plugins'] = [
    /**
     * error handler
     */
    'phile\\errorHandler' => [
        'active' => true,
        'handler' => 'development'
    ],
    /**
     * setup check
     */
    'phile\\setupCheck' => ['active' => true],
    /**
     * parser
     */
    'phile\\parserMarkdown' => ['active' => true],
    /**
     * meta-tag parser
     */
    'phile\\parserMeta' => [
        'active' => true,
        /**
         * Set meta-data format.
         *
         * - 'Phile' (default) Phile legacy format
         * - 'YAML' YAML
         *
         * Phile is going to switch to YAML for parsing meta tags. But if you
         * want to use YAML today you can change the format here.
         */
        'format' => 'Phile'
    ],
    /**
     * template engine
     */
    'phile\\templateTwig' => ['active' => true],
    /**
     * Sets the cache engine.
     *
     * For easy development the cache is set to a storage that's non-persistent
     * and in memory only. In production this should be set to a persistent
     * storage.
     *
     * See http://www.phpfastcache.com/ for available cache engines. The easiest
     * solution is to use files ('storage' => 'Files').
     */
    'phile\\phpFastCache' => ['active' => true, 'storage' => 'Memstatic'],
    /**
     * persistent data storage
     */
    'phile\\simpleFileDataPersistence' => ['active' => true],
];

return $config;
