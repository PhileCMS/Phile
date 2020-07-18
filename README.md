# PhileCMS

[![Software License](https://img.shields.io/packagist/l/phile-cms/phile.svg)](https://github.com/PhileCMS/Phile/blob/master/LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/phile-cms/phile.svg)](https://packagist.org/packages/phile-cms/phile)
[![Build Status](https://travis-ci.org/PhileCMS/Phile.svg?branch=master)](https://travis-ci.org/PhileCMS/Phile)
[![Code Quality](https://img.shields.io/scrutinizer/g/PhileCMS/Phile.svg)](https://scrutinizer-ci.com/g/PhileCMS/Phile/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PhileCMS/Phile.svg)](https://scrutinizer-ci.com/g/PhileCMS/Phile/?branch=master)

A Markdown file-based CMS written in PHP.

[Check out the starter video](http://www.youtube.com/watch?v=8GLMe371RuI) or [read the wiki for in-depth documentation](https://philecms.github.io/wiki/).

## Why use PhileCMS?

Phile was forked from Pico when a few community members wanted to contribute more and increase the rate of development. Here is a small list of differences:

* Object Oriented Design (classes)
* Events system
* Replacable core modules (plugins)
    * Parser (default: [Markdown](https://github.com/michelf/php-markdown))
    * Template Engine (default: [Twig](http://twig.sensiolabs.org/))
    * Cache (default: [phpFastCache](https://github.com/khoaofgod/phpfastcache))
    * Error handler (default: [Whoops](http://filp.github.io/whoops/))
    * Meta parser (default: Phile parser)
    * Simple Data Persistence (default: Phile file-based)
* Per-Plugin `config.php` files
* Namespaced plugins so classes can have the same name
* PSR-0, PSR-1/2, PSR-7/15

## Quick Start

Requirements:

* PHP 7.2.0+
* a web-server

Download the release from <https://philecms.github.io/>, copy the files to your web-server and open the root directory in the browser.

Or use composer and run it locally:

```shell
composer create-project --no-dev phile-cms/phile;
cd Phile;
php -S localhost:8080;
```

On a fresh installation you will see a page with a setup instructions. Follow them. For detailed instructions please see the [installation help](https://philecms.github.io/wiki/%5BHOW-TO%5D-Installation.html).

## Plugins

Phile can be extended with [a wide variety of plugins](https://philecms.github.io/wiki/%5BCOMMUNITY%5D-Plugins.html).

Generally a plugin can always be installed manually: clone or download the plugin into `plugins/{vendor}/{pluginName}` folder. **Example**: [phileRss](https://github.com/PhileCMS/phileRSSFeed/) would be installed into `plugins/phile/rssFeed`.

Many plugins also allow composer installation:

```php
composer require phile/rss-feed
```

Always check the plugin readme for specialised installation instruction.

## Getting Help

[![Gitter chat](https://badges.gitter.im/PhileCMS/Phile.png)](https://gitter.im/PhileCMS/Phile)

You can [read the wiki](https://philecms.github.io/wiki/) if you are looking for examples more development information.

If you *find a bug* please report it on the [issues page](https://github.com/PhileCMS/Phile/issues), but remember to include a bunch of details and also what someone can do to re-create the issue.

Issues with plugins should be reported *on the offending plugins homepage* this goes for themes as well.

## Contributing

Help make PhileCMS better by checking out the GitHub repository and submitting pull requests.
See [developer guildlines](https://philecms.github.io/wiki/%5BDEVELOPER%5D-Developer-Guidelines.html) for more information.

