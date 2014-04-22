Phile
=====

A Markdown file-based CMS.

[Check out the starter video](http://www.youtube.com/watch?v=8GLMe371RuI), or [read the wiki for in-depth documenation](https://github.com/PhileCMS/Phile/wiki/_pages).

### Help and Community

You can check out the [PhileCMS Community on Google Plus](https://plus.google.com/u/0/communities/105363272048954062353). Ask questions there that are more general and less to do with bugs.

### Origins

This project is based on [Pico CMS v0.8](https://github.com/gilbitron/Pico/commit/aa59661ff81dd52c3a2596988372a214b0fc31b9 "0.8 Commit") by [Gilbert Pellegrom](https://github.com/gilbitron).

The desire to fork the project from Pico, came when a few community members wanted to contribute more and increase the rate of development progress.

**Note: Plugins for Pico are not interchangeable with Phile.**

### Why use this over Pico?

Here is a small list of differences in design from Pico:

* OOP based (classes)
* Events system
* Parser Overloading
* Template Engine Overloading
* [PSR-0 Compliant](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)
* Uses Composer

##### Performance with only three pages:

Page           | Pico          | Phile
-------------- | ------------- | -------------
Startpage      | 180ms         | 120ms
Sub Page Index | 117ms         | 83ms
Sub Page       | 152ms         | 85ms

##### Performance with 20 pages:

Page           | Pico          | Phile
-------------- | ------------- | -------------
Startpage      | 250ms         | 121ms
Sub Page Index | 203ms         | 80ms
Sub Page       | 237ms         | 84ms

Webgrind Profiling

![Pico Results](http://i.imgur.com/pgOS09V.png)

![Phile Results](http://i.imgur.com/jrbVf03.png)

### Contributing

Help make Phile better by checking out the GitHub repoistory and submitting pull requests.

If you find a bug please report it on the [issues page](https://github.com/PhileCMS/Phile/issues).

If you create a plugin please add it to the [Plugin Wiki](https://github.com/PhileCMS/Phile/wiki/%5BCOMMUNITY%5D-Plugins).

The Authors of PhileCMS:

* [James Doyle](https://github.com/james2doyle) - great ideas, code a lot of plugins and worked on the core
* [Frank Nägler](https://github.com/NeoBlack) - refactoring of the core and introducing OOP concepts
