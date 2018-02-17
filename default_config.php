<?php

/**
 * Phile default config.
 *
 * Don't do changes here but overwrite them in your local config.
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
     * cache engine
     */
    'phile\\phpFastCache' => ['active' => true],
    /**
     * persistent data storage
     */
    'phile\\simpleFileDataPersistence' => ['active' => true],
];

return $config;
