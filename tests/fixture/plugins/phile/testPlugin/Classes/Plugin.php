<?php
/**
 * test class used in Phile's unit tests
 */

namespace Phile\Plugin\Phile\TestPlugin;

use Phile\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{

    protected $events = [
        'phile\testPlugin.testEvent' => 'onTestEvent',
        'phile\testPlugin.testEvent-missingMethod' => 'missingMethod'
    ];

    protected $settings = ['A' => 'X', 'B' => 'X', 'C' => 'C'];

    /**
     * Testing accessor for protected $settings
     *
     * @return array The settings array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * accessor for easy testing
     *
     * @param string $path
     *
     * @return string
     */

    public function getPluginPath(string $path = ''): string
    {
        return parent::getPluginPath($path);
    }

    protected function onTestEvent()
    {
    }
}
