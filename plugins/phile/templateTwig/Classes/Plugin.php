<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\TemplateTwig;

use Phile\Core\Container;
use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Phile\TemplateTwig\Template\Twig;

/**
 * Class Plugin
 * Default Phile template engine
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TemplateTwig
 */
class Plugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

    /**
     * Registers Twig as template service
     *
     * @param array $data
     * @return void
     */
    public function onPluginsLoaded($data)
    {
        $phile = Container::getInstance()->get('Phile_Config');
        $settings = $this->settings + [
            'theme' => $phile->get('theme'),
            'themes_dir' => $phile->get('themes_dir')
        ];
        ServiceLocator::registerService('Phile_Template', new Twig($settings));
    }
}
