<?php

use Phile\Core\Container;

return  [
    'storage_dir' => Container::getInstance()->get('Phile_Config')->get('storage_dir')
];
