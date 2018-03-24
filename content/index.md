<!--
Title: Welcome
Description: This description will go in the meta description tag
-->

# Welcome to Phile

Congratulations, you have successfully installed [Phile](https://github.com/PhileCMS/Phile). Phile is a Markdown based flat file CMS.

## Configuration

You can override the default Phile settings (and add your own custom settings) by editing  "config/config.php".

The "config/default.php" file lists all of the settings and their defaults. To override a setting, simply set it in "config.php" with your own value.

## Creating Content

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

## Text File Markup

Text files are marked up using [Markdown](http://daringfireball.net/projects/markdown/syntax). They can also contain regular HTML.

At the top of text files you can place a block comment and specify certain meta attributes of the page. For example:

```markdown
<!--
Title: Welcome
Description: This description will go in the meta description tag
Author: Joe Bloggs
Date: 2013/01/01
Robots: noindex,nofollow
-->
```

Aside from HTML `<!-- … -->` style comments as shown above Phile also allows for `/* … */` or YAML `--- … ---` block comments.

## Page Ordering

You can order pages by their attributes. For example for an custom ordering create an `Order` meta attribute on each page, then use `$config['pages_order'] = "meta.order:asc";` in your `config.php` file.

## Themes & Templates

You can create themes for your Phile installation in the "themes" folder. Check out the default theme for an example of a theme.

Create a new theme by duplicating the default theme folder and renaming it. Then activate it by setting `$config['theme']` to that name.

All themes must include an `index.html` file to define the HTML structure of the theme. Phile uses [Twig](http://twig.sensiolabs.org/documentation) for it's templating engine. Below are the Twig variables that are available to use in your theme:

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

```twig
<ul class="nav">
  {% for page in pages %}
    <li><a href="{{ page.url }}">{{ page.title }}</a></li>
  {% endfor %}
</ul>
```

## Further Customization and Plugins

For further information on customization and plugins check out the documentation on the [Phile homepage][homepage]. 

[homepage]: https://philecms.github.io/