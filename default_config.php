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
 * page order
 *
 * Order pages by "title" (alpha) or "date"
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
 * include core plugins
 */
$config['plugins'] = [
	/**
	 * error handler
	 */
	'phile\\errorHandler' => [
		'active' => true,
		'handler' => \Phile\Plugin\Phile\ErrorHandler\Plugin::HANDLER_DEVELOPMENT
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
