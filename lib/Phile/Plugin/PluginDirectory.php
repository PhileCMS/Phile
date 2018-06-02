<?php
/**
 * @author PhileCMS
 * @link https://philecms.github.io/
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin
 */

namespace Phile\Plugin;

use Phile\Exception\PluginException;

/**
 * Represents a dire directory with plugin folders
 */
class PluginDirectory
{
    /**
     * File path to directory
     *
     * @var string
     */
    protected $path;

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        spl_autoload_register([$this, 'autoload']);
    }

    /**
     * Gets directory path
     *
     * @return string path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Tries to create new a Plugin class from plugin in directory
     *
     * @param string $pluginKey
     * @return AbstractPlugin|null
     */
    public function newPluginInstance(string $pluginKey): ?AbstractPlugin
    {
        list($vendor, $pluginName) = explode('\\', $pluginKey);
        // uppercase first letter convention
        $className = 'Phile\\Plugin\\' . ucfirst($vendor) . '\\' . ucfirst($pluginName) . '\\Plugin';
        if (!file_exists($this->filenameForClass($className))) {
            return null;
        };

        $plugin = new $className;
        if (($plugin instanceof AbstractPlugin) === false) {
            throw new PluginException(
                "the plugin '{$pluginKey}' is not an instance of \\Phile\\Plugin\\AbstractPlugin",
                1398536526
            );
        }
        return $plugin;
    }

    /**
     * Class auto-loader plugin namespace
     *
     * @param string $className
     * @return void
     */
    public function autoload(string $className): void
    {
        if (strpos($className, "Phile\\Plugin\\") !== 0) {
            return;
        }
        $filename = $this->filenameForClass($className);
        if (file_exists($filename)) {
            include $filename;
        }
    }

    /**
     * Creates file-path to class-file in plugin directory
     *
     * @param string $className
     * @return string the file path
     */
    protected function filenameForClass(string $className): string
    {
        $className = str_replace('Phile\\Plugin\\', '', $className);
        $classNameParts = explode('\\', $className);
        $pluginVendor = lcfirst(array_shift($classNameParts));
        $pluginName = lcfirst(array_shift($classNameParts));
        $classPath = array_merge(
            [$pluginVendor, $pluginName, 'Classes'],
            $classNameParts
        );

        return $this->path . implode('/', $classPath) . '.php';
    }
}
