<?php

use Phile\Bootstrap;
use Phile\Core\Utility;

/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 * @deprecated since 2015-05-01, file will be removed
 */

require_once __DIR__ . '/lib/Phile/Bootstrap.php';

Bootstrap::getInstance()->initializeBasics();

echo Utility::generateSecureToken(64);

echo "<br><br><br>(Please note: this file is deprecated and will be removed in an upcoming Phile release.)";
