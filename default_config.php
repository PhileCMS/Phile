<?php

// try to figure out the install path
$config['site_title'] = 'PhileCMS'; // Site title
$config['base_url'] = \Phile\Utility::getBaseUrl(); // use the Utility class to guess the base_url
$config['theme'] = 'default'; // Set the theme
$config['date_format'] = 'jS M Y'; // Set the PHP date format
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
    'phileParserMeta' => array('active' => true), // the default parser
    'phileTemplateTwig' => array('active' => true), // the default template engine
    'philePhpFastCache' => array('active' => true), // the default cache engine
    'phileSimpleFileDataPersistence' => array('active' => true), // the default data storage engine
);

return $config;
