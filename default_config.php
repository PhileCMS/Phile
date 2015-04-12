<?php

/*
 * Base URL to Phile installation without trailing slash
 *
 * e.g. `http://example.com` or `http://example.com/phile`
 *
 * Default: try to resolve automatically in Router
 */
$config['base_url']       = (new Phile\Core\Router)->getBaseUrl();

$config['site_title']     = 'PhileCMS'; // Site title
$config['theme']          = 'default'; // Set the theme
$config['date_format']    = 'jS M Y'; // Set the PHP date format
$config['pages_order']    = 'meta.title:desc'; // Order pages by "title" (alpha) or "date"

// figure out the timezone
$config['timezone']       = (ini_get('date.timezone')) ? ini_get('date.timezone') : 'UTC'; // The default timezone

$config['charset']    = 'utf-8'; // charset used for HTML & Markdown files

// only extend $config['plugins'] and not overwrite it, because some core plugins
// will be added to this config option by default. So, use this option in this way:
// $config['plugins']['myCustomPlugin'] = array('active' => true);
// also notice, each plugin has its own config namespace.
// activate plugins
$config['plugins'] = array(
	// key = vendor\\pluginName (vendor lowercase, pluginName lowerCamelCase
	'mycompany\\demoPlugin'            => array('active' => false),
	'phile\\errorHandler'              => array(
		'active' => true,
		'handler' => \Phile\Plugin\Phile\ErrorHandler\Plugin::HANDLER_DEVELOPMENT
	), // the default error handler
	'phile\\parserMarkdown'            => array('active' => true), // the default parser
	'phile\\parserMeta'                => array('active' => true), // the default parser
	'phile\\templateTwig'              => array('active' => true), // the default template engine
	'phile\\phpFastCache'              => array('active' => true), // the default cache engine
	'phile\\simpleFileDataPersistence' => array('active' => true), // the default data storage engine
);

return $config;
