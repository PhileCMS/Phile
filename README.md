PhileCMS
========

[![Software License](https://img.shields.io/packagist/l/phile-cms/phile.svg)](https://github.com/PhileCMS/Phile/blob/master/LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/phile-cms/phile.svg)](https://packagist.org/packages/phile-cms/phile)
[![Build Status](https://travis-ci.org/PhileCMS/Phile.svg?branch=master)](https://travis-ci.org/PhileCMS/Phile)
[![Code Quality](https://img.shields.io/scrutinizer/g/PhileCMS/Phile.svg)](https://scrutinizer-ci.com/g/PhileCMS/Phile/?branch=master)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/PhileCMS/Phile.svg)](https://scrutinizer-ci.com/g/PhileCMS/Phile/?branch=master)


A Markdown file-based CMS.

[Check out the starter video](http://www.youtube.com/watch?v=8GLMe371RuI) or [read the wiki for in-depth documentation](https://github.com/PhileCMS/Phile/wiki/_pages).

## Gitter Developer & Support-System

[![Gitter chat](https://badges.gitter.im/PhileCMS/Phile.png)](https://gitter.im/PhileCMS/Phile)

## waffle.io board

[![Stories in Ready](https://badge.waffle.io/PhileCMS/Phile.svg?label=ready&title=Ready)](http://waffle.io/PhileCMS/Phile)

## Why use PhileCMS?

The desire to fork the project from Pico, came when a few community members wanted to contribute more and increase the rate of development progress.

Here is a small list of differences in design from typical flat-file CMSs:

* OOP based (classes)
* [Events system](https://github.com/PhileCMS/Phile/wiki/%5BDEVELOPER%5D-Event-System)
* [Uses Composer](https://github.com/PhileCMS/Phile/blob/master/composer.json)
* Overload core modules (plugins)
    * Parser (default: [Markdown](https://github.com/michelf/php-markdown))
    * Template Engine (default: [Twig](http://twig.sensiolabs.org/))
    * Cache (default: [phpFastCache](https://github.com/khoaofgod/phpfastcache))
    * Error handler (default: system handler)
    * Meta parser (default: system parser)
    * Simple Data Persistence (default: system data storage)
* Per-Plugin `config.php` files
* *Namespaced plugins* so classes can have the same name
* [PSR-0 Compliant](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)

## Installation

##### Requirements

* PHP `>=5.4.0`
* Apache with `mod_rewrite` enabled

##### Quick Start

You can install the latest version either by downloading it from <http://philecms.com/> or use composer:

```shell
composer create-project --no-dev phile-cms/phile
```

Copy it to your server web root. Open the root of PhileCMS on you web server. If you are using the default MAMP install, that would be [http://localhost:8888/Phile](http://localhost:8888/Phile). Using WAMP, [http://localhost/Phile](http://localhost/Phile). If you are using something else, you probably know how to run a typical PHP site.

On first run you will hit a setup page. **Follow the instructions on the [setup page](https://github.com/PhileCMS/Phile/blob/master/content/setup.md) to complete the installation**.

[For detailed instructions please see the Installation wiki page](https://github.com/PhileCMS/Phile/wiki/%5BHOW-TO%5D-Installation).

## Plugins

Phile can be extended with a wide variety of plugins. You can also overwrite the default plugins in order to add extra functionality, speed, or features.

Help make Phile better by checking out the GitHub repository and submitting pull requests.

##### With composer

To install via composer, you would run:

```php
composer require phile/rss-feed:dev-master
```

##### With git

While sitting in your root PhileCMS folder, you can run:

```shell
git clone https://github.com/PhileCMS/phileRSSFeed plugins/phile/rssFeed
```

##### You've downloaded the .zip

You can clone or download the plugin into the `plugins/{namespace}/{pluginClassName}` folder. **Example**: [phileAdmin](https://github.com/james2doyle/phileAdmin) would be installed into `plugins/phile/adminPanel`.

## Getting Help

You can [read the wiki](https://github.com/PhileCMS/Phile/wiki) if you are looking for examples and [read the intro-docs](http://philecms.com/docs.html) for more development information. If you are looking for some general help and have some questions, please post in the [Google Plus Community](https://plus.google.com/u/0/communities/105363272048954062353 "PhileCMS Community").

If you *find a bug* please report it on the [issues page](https://github.com/PhileCMS/Phile/issues), but remember to include a bunch of details and also what someone can do to re-create the issue.

Issues with plugins should be reported **on the offending plugins homepage** this goes for themes as well.

## Contributing

Help make PhileCMS better by checking out the GitHub repository and submitting pull requests.

If you create a plugin please add it to the [Plugin Wiki](https://github.com/PhileCMS/Phile/wiki/%5BCOMMUNITY%5D-Plugins).

### The Authors of PhileCMS

* [James Doyle](https://github.com/james2doyle) - great ideas, developed many plugins and worked on the core
* [Frank Nägler](https://github.com/NeoBlack) - refactoring of the core and introducing OOP concepts
