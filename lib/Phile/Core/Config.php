<?php

namespace Phile\Core;

/**
 * Config class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Config
{
    /** @var boolean configuration is writable */
    protected $isLocked = false;
    
    public function __construct(array $values = [])
    {
        Registry::set('Phile_Settings', []);
        $this->set($values);
    }

    /**
     * Getter for configuration values
     *
     * @param string $key single key
     * @return mixed
     */
    public function get($key = null)
    {
        $config = Registry::get('Phile_Settings');

        if (array_key_exists($key, $config)) {
            return $config[$key];
        }

        $constant = strtoupper($key);
        if (defined($constant)) {
            return constant($constant);
        }

        return null;
    }

    public function has($key)
    {
        $config = Registry::get('Phile_Settings');
        return array_key_exists($key, $config);
    }

    /**
     * Return configuration as PHP-array
     *
     * @return array
     */
    public function toArray()
    {
        return Registry::get('Phile_Settings');
    }

    /**
     * Setter for configuration values
     *
     * @param string|array $key set single key value; sell all if array
     * @param mixed $value
     */
    public function set($key, $value = null)
    {
        $config = Registry::get('Phile_Settings');

        if ($this->isLocked) {
            throw new \LogicException(
                sprintf('Phile-configuration is locked. Can\' set key "%s"', $key),
                1518440759
            );
        }

        if ($value === null && is_array($key)) {
            $config = $key;
        } else {
            $config[$key] = $value;
        }

        Registry::set('Phile_Settings', $config);
    }

    /**
     * Recursively merges a configuration over the existing configuration
     *
     * @param array $values configuration to merge
     */
    public function merge(array $values)
    {
        $old = $this->toArray();
        $new = array_replace_recursive($old, $values);
        $this->set($new);
    }

    /**
     * Creates an array of template variables derived from the configuration
     *
     * @return array
     */
    public function getTemplateVars()
    {
        return [
            'base_dir' => rtrim($this->get('root_dir'), '/'),
            'base_url' => $this->get('base_url'),
            'config' => $this->toArray(),
            'content_dir' => $this->get('content_dir'),
            'content_url' => $this->get('base_url') . '/' . basename($this->get('content_dir')),
            'site_title' => $this->get('site_title'),
            'theme_dir' => $this->get('themes_dir') . $this->get('theme'),
            'theme_url' => $this->get('base_url') . '/'
                . basename($this->get('themes_dir')) . '/' . $this->get('theme'),
        ];
    }

    /**
     * Locks configuration into read-only mode
     */
    public function lock()
    {
        $this->isLocked = true;
    }
}
