<?php
/**
 * Local Phile configuration.
 *
 * This configuration allows to customize Phile. It overwrites
 * settings made in defaults.php or plugin-defaults. See those
 * for additional Phile configuration settings.
 */
$config = [];

/**
 * encryption key
 */
$config['encryptionKey'] = '';

/**
 * page title
 */
$config['site_title'] = 'PhileCMS';

/**
 * default theme
 */
$config['theme'] = 'default';

/**
 * Activate the persistent cache for better performance.
 */
//$config['plugins']['phile\\phpFastCache'] = ['active' => true, 'storage' => 'files'];

/**
 * Use the demo-plugin as a starting point to write your own plugins.
 */
//$config['plugins']['mycompany\\demoPlugin'] = ['active' => true];

return $config;
