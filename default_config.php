<?php

// try to figure out the install path
$config['site_title']       = 'PhileCMS';			            // Site title
$config['base_url']         = \Phile\Utility::getBaseUrl(); 	// Override base URL (e.g. http://example.com)
$config['install_path']     = '';                               // if you installed phile in a subfolder, e.g. http://example.com/phile/ set this option to "phile/"
$config['theme']            = 'default'; 			            // Set the theme (defaults to "default")
$config['date_format']      = 'jS M Y';		                    // Set the PHP date format
$config['twig_config'] = array(			// Twig settings
	'cache' => false,					// To enable Twig caching change this to CACHE_DIR
	'autoescape' => false,				// Autoescape Twig vars
	'debug' => false					// Enable Twig debug
);
$config['pages_order_by']   = 'title';	        // Order pages by "title" (alpha) or "date"
$config['pages_order']      = 'asc';	        // Order pages "asc" or "desc"
$config['timezone']         = 'Europe/Berlin'; 	// The default timezone

// only extend $config['plugins'] and not overwrite it, because some core plugins
// will be added to this config option by default. So, use this option in this way:
// $config['plugins']['myCustomPlugin'] = array('active' => true);
// also notice, each plugin has its own config namespace.
// activate plugins
$config['plugins'] = array();

$config['plugins']['phileDemoPlugin'] = array('active' => true);

return $config;