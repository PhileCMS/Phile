<!--
Title: Welcome
Description: This description will go in the meta description tag
-->

## Welcome to Phile

Congratulations, you have successfully installed [Phile](https://github.com/PhileCMS/Phile). Phile is a Markdown based flat file CMS.

### Creating Content

Phile is a flat file CMS, this means there is no administration backend and database to deal with. You simply create `.md` files in the "content"
folder and that becomes a page. For example, this file is called `index.md` and is shown as the main landing page.

If you create a folder within the content folder (e.g. `content/sub`) and put an `index.md` inside it, you can access that folder at the URL
`http://yousite.com/sub`. If you want another page within the sub folder, simply create a text file with the corresponding name (e.g. `content/sub/page.md`)
and you will be able to access it from the URL `http://yousite.com/sub/page`. Below we've shown some examples of content locations and their corresponding URL's:

| Physical Location           | URL                   |
| --------------------------- |:----------------------|
| content/index.md            | /                     |
| content/sub.md              | /sub                  |
| content/sub/index.md        | /sub (same as above)  |
| content/sub/page.md         | /sub/page             |
| content/a/very/long/url.md  | /a/very/long/url      |

If a file cannot be found, the file `content/404.md` will be shown.

### Text File Markup

Text files are marked up using [Markdown](http://daringfireball.net/projects/markdown/syntax). They can also contain regular HTML.

At the top of text files you can place a block comment and specify certain attributes of the page. For example:

~~~ .markdown
/*
Title: Welcome
Description: This description will go in the meta description tag
Author: Joe Bloggs
Date: 2013/01/01
Robots: noindex,nofollow
*/
~~~

Phile also allows HTML style block comments:

~~~ .html
<!--
Title: Welcome
Description: This description will go in the meta description tag
Author: Joe Bloggs
Date: 2013/01/01
Robots: noindex,nofollow
-->
~~~

#### Custom Meta

You can actually create custom meta attributes by default in Phile. If you want to add a date, author, or even something else, this can be done easily. These values will be contained in the `{{ meta }}` variable in themes (see below).

#### Custom Meta Ordering

You can order pages by their custom meta attributes. Like creating an `Order` meta for each page, then you can use `$config['pages_order_by'] = "meta:order";` in your `config.php` file.

### Themes

You can create themes for your Phile installation in the "themes" folder. Check out the default theme for an example of a theme. Phile uses
[Twig](http://twig.sensiolabs.org/documentation) for it's templating engine. You can select your theme by setting the `$config['theme']` variable
in config.php to your theme folder.

All themes must include an `index.html` file to define the HTML structure of the theme. Below are the Twig variables that are available to use in your theme:

* `{{ config }}` - Contains the values you set in config.php (e.g. `{{ config.theme }}` = "default")
* `{{ base_dir }}` - The path to your Phile root directory
* `{{ base_url }}` - The URL to your Phile site
* `{{ theme_dir }}` - The path to the Phile active theme directory
* `{{ theme_url }}` - The URL to the Phile active theme directory
* `{{ content_dir }}` - The path to the content direcotry
* `{{ content_url }}` - The URL to the content directory
* `{{ site_title }}` - Shortcut to the site title (defined in config.php)
* `{{ meta }}` - Contains the meta values from the current page
  * `{{ meta.title }}`
  * `{{ meta.description }}`
* `{{ content }}` - The content of the current page (after it has been processed through Markdown)
* `{{ pages }}` - A collection of all the content in your site
  * `{{ page.title }}`
  * `{{ page.url }}`
  * `{{ page.content }}`
* `{{ current_page }}` - A page object of the current_page

Page listing example:

~~~ .html
<ul class="nav">
  {% for page in pages %}
    <li><a href="{{ page.url }}">{{ page.title }}</a></li>
  {% endfor %}
</ul>
~~~

### Config

You can override the default Phile settings (and add your own custom settings) by editing config.php in the root Phile directory. The config.php file
lists all of the settings and their defaults. To override a setting, simply uncomment it in config.php and set your custom value.

### Events

In the core we trigger a lot of events, which help to manipulate content or other stuff within a plugin.
To use the event system, you only have to register your plugin for a specific event, look at the example plugin for more an example.

The following list shows all events.

#### plugins_loaded

this event is triggered after the plugins loaded

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `plugins`               | array                              | `Plugin` classes of all loaded plugins                               |


#### config_loaded

this event is triggered after the configuration is fully loaded

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `config`                | array                              | the complete configuration                                           |

#### after\_init\_core

this event is triggered after the core is initialized

| param                   | type                 | description                                                      |
| ----------------------- |:---------------------|:-----------------------------------------------------------------|
| `response`              | \Phile\Core\Response | the response                                                     |  

#### request_uri

this event is triggered after the request uri is detected.

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `uri`                   | string                             | the requested uri (without install_path)                             |

#### after\_404

this event is triggered after a requested page is not found

#### after\_resolve\_page

this event is triggered after a request is resolved to a page

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `pageId`                | string                             | the requested page-ID                                                |  
| `page`                  | Phile\Model\Page                   | the page served                                                      |


#### before\_init\_template

this event is triggered before the the template engine is init

#### before\_render\_template

this event is triggered before the template is rendered

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `templateEngine`        | \Phile\Template\TemplateInterface  | the template engine                                                  |

#### template\_engine\_registered

this event is triggered before the template is rendered

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `engine`                | \Phile\Template\TemplateInterface  | the raw template engine                                              |
| `data`                  | array                              | the variables being sent to the template engine                      |

#### after\_render\_template

this event is triggered after the template is rendered

| param                   | type                               | description                                                          |
| ----------------------- |:-----------------------------------|:---------------------------------------------------------------------|
| `templateEngine`        | \Phile\Template\TemplateInterface  | the template engine                                                  |
| `output`                | string                             | the parsed and ready output                                          |

#### before\_read\_file\_meta

this event is triggered before the meta data is read and parsed

| param                   | type                | description                                                      |
| ----------------------- |:--------------------|:-----------------------------------------------------------------|
| `rawData`               | string              | the unparsed data                                                |
| `meta`                  | \Phile\Model\Meta   | the meta model                                                   |

#### after\_read\_file\_meta

this event is triggered after the meta data is read and parsed

| param                   | type                | description                                                      |
| ----------------------- |:--------------------|:-----------------------------------------------------------------|
| `rawData`               | string              | the unparsed data                                                |
| `meta`                  | \Phile\Model\Meta   | the meta model                                                   |

#### before\_load\_content

this event is triggered before the content is loaded

| param                   | type                | description                                                      |
| ----------------------- |:--------------------|:-----------------------------------------------------------------|
| `filePath`              | string              | the path to the file                                             |
| `page`                  | \Phile\Model\Page   | the page model                                                   |

#### after\_load\_content

this event is triggered after the content is loaded

| param                   | type                | description                                                      |
| ----------------------- |:--------------------|:-----------------------------------------------------------------|
| `filePath`              | string              | the path to the file                                             |
| `rawData`               | string              | the raw data                                                     |
| `page`                  | \Phile\Model\Page   | the page model                                                   |

#### before\_parse\_content

this event is triggered before the content is parsed

| param                   | type                | description                                                      |
| ----------------------- |:--------------------|:-----------------------------------------------------------------|
| `content`               | string              | the raw data                                                     |
| `page`                  | \Phile\Model\Page   | the page model                                                   |

#### after\_parse\_content

this event is triggered after the content is parsed

| param                   | type                | description                                                      |
| ----------------------- |:--------------------|:-----------------------------------------------------------------|
| `content`               | string              | the raw data                                                     |
| `page`                  | \Phile\Model\Page   | the page model                                                   |
