<?php

use Phile\Bootstrap;
use Phile\Core\Utility;

/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

require_once __DIR__ . '/lib/Phile/Bootstrap.php';

Bootstrap::getInstance()->initializeBasics();

echo Utility::generateSecureToken(64);
