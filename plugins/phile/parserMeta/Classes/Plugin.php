<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ParserMeta;

use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Phile\ParserMeta\Parser\Meta;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMeta
 */
class Plugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

    /**
     * onPluginsLoaded method
     *
     * @param array $data
     * @return void
     */
    public function onPluginsLoaded($data)
    {
        ServiceLocator::registerService(
            'Phile_Parser_Meta',
            new Meta($this->settings)
        );
    }
}
