#!/bin/sh

/usr/local/zend/bin/phpdoc -d ./lib -d ./plugins --ignore "plugins/phile/phpFastCache/lib/phpfastcache/*" -t ./docs/