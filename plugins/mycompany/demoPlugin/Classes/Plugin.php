<?php
/**
 * Plugin class
 */

/*
 * The namespace structure is Phile\Plugin\<vendor>\<plugin-name>
 *
 * - the namespace always starts with Phile\Plugin
 * - the vendor name in lowercase is the folder name under plugins directory
 * - the sub-folder in lowerCamelCase is the plugin name
 *
 * So if your folder is: plugins/mycompany/myPluginName/
 * then your namespace should be: Phile\Plugin\Mycompany\MyPluginName
 */
namespace Phile\Plugin\Mycompany\DemoPlugin;

// import useful Phile classes for easy access
use Phile\Core\Response;
use Phile\Core\Session;
use Phile\Core\Utility;

/**
 * Class Plugin - the class name is always "Plugin"
 *
 * @author  PhileCMS
 * @license http://opensource.org/licenses/MIT
 */
class Plugin extends \Phile\Plugin\AbstractPlugin {

	/**
	 * subscribe to Phile events with methods of this class
	 *
	 * In this example we subscribe to "before_parse_content" and
	 * "outputPluginSettings" will be called.
	 */
	protected $events = ['before_parse_content' => 'outputPluginSettings'];

	/**
	 * the method we assigned to the 'before_parse_content' event
	 *
	 * in this example we output this plugins' settings on the top of every page
	 *
	 * @param null|array $data depends on the particular event (see Phile's event docs)
	 */
	public function outputPluginSettings($data = null) {
		// you can access this plugins' config in $this->settings
		$settings = $this->settings;

		$content =  $data['content'];
		$content = $this->printPhpAsMarkdown($settings) . $content;

		$page = $data['page'];
		$page->setContent($content);
	}

	/**
	 * plugin helper method for printing PHP as markdown code
	 */
	protected function printPhpAsMarkdown($input) {
		return "\n```php\n" . trim(print_r($input, true), "\n") . "\n```\n";
	}

}
