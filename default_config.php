<?php

// try to figure out the install path
$config['site_title'] = 'PhileCMS'; // Site title
$config['base_url'] = \Phile\Utility::getBaseUrl(); // use the Utility class to guess the base_url
$config['theme'] = 'default'; // Set the theme
$config['date_format'] = 'jS M Y'; // Set the PHP date format
// Twig settings
$config['twig_config'] = array(
	'cache' => false, // To enable Twig caching change this to CACHE_DIR
	'autoescape' => false, // Autoescape Twig vars
	'debug' => false // Enable Twig debug
	);
$config['pages_order_by'] = 'title'; // Order pages by "title" (alpha) or "date"
$config['pages_order'] = 'desc'; // Order pages "asc" or "desc"

// figure out the timezone
$timezone = (ini_get('date.timezone')) ? ini_get('date.timezone') : 'UTC';
$config['timezone'] = $timezone; // The default timezone

// only extend $config['plugins'] and not overwrite it, because some core plugins
// will be added to this config option by default. So, use this option in this way:
// $config['plugins']['myCustomPlugin'] = array('active' => true);
// also notice, each plugin has its own config namespace.
// activate plugins
$config['plugins'] = array(
	'phileDemoPlugin' => array('active' => true),
	'phileParserMarkdown' => array('active' => true), // the default parser
	'phileTemplateTwig' => array('active' => true), // the default template engine
);

return $config;
