<?php
/**
 * Template engine class
 */
namespace Phile\Plugin\Phile\TemplateTwig\Template;

use Phile\Core\Container;
use Phile\Core\Registry;
use Phile\Model\Page;
use Phile\ServiceLocator\TemplateInterface;

/**
 * Class Twig
 *
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TemplateTwig\Template
 */
class Twig implements TemplateInterface
{
    /**
     * @var array the config for twig
     */
    protected $config;

    /**
     * @var Page the page model
     */
    protected $page;

    /**
     * @var string theme name
     */
    protected $theme;

    /**
     * @var string path to theme directory
     */
    protected $themesDir;

    /**
     * the constructor
     *
     * @param array $config the configuration
     */
    public function __construct(array $config = [])
    {
        $this->theme = $config['theme'];
        $this->themesDir = $config['themes_dir'];
        unset($config['theme'], $config['themes_dir']);
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrentPage(Page $page)
    {
        $this->page = $page;
    }

    /**
     * method to render the page/template
     *
     * @return mixed|string
     */
    public function render()
    {
        $engine = $this->getEngine();
        $vars = $this->getTemplateVars();
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'template_engine_registered',
            ['engine' => &$engine, 'data' => &$vars]
        );
        return $this->doRender($engine, $vars);
    }

    /**
     * wrapper to call the render engine
     *
     * @param \Twig\Environment $engine
     * @param array $vars
     * @return string
     */
    protected function doRender(\Twig\Environment $engine, array $vars): string
    {
        try {
            $template = $this->getTemplateFileName();
        } catch (\RuntimeException $e) {
            return $e->getMessage();
        }
        return $engine->render($template, $vars);
    }

    /**
     * get template engine
     *
     * @return \Twig\Environment
     */
    protected function getEngine()
    {
        $loader = new \Twig\Loader\FilesystemLoader($this->getTemplatePath());
        $twig = new \Twig\Environment($loader, $this->config);

        // load the twig debug extension if required
        if (!empty($this->config['debug'])) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        return $twig;
    }

    /**
     * get template file name
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getTemplateFileName(): string
    {
        $template = $this->page->getMeta()->get('template');
        if (empty($template)) {
            $template = 'index';
        }
        if (!empty($this->config['template-extension'])) {
            $template .= '.' . $this->config['template-extension'];
        }
        $templatePath = $this->getTemplatePath($template);
        if (!file_exists($templatePath)) {
            throw new \RuntimeException(
                "Template file '{$templatePath}' not found.",
                1427990135
            );
        }
        return $template;
    }

    /**
     * get file path to (sub-path) in theme-path
     *
     * @param  string $sub
     * @return string
     */
    protected function getTemplatePath(string $sub = ''): string
    {
        $themePath = $this->themesDir . $this->theme;
        if (!empty($sub)) {
            $themePath .= '/' . ltrim($sub, DIRECTORY_SEPARATOR);
        }
        return $themePath;
    }

    /**
     * get template vars
     *
     * @return array
     * @throws \Exception
     */
    protected function getTemplateVars(): array
    {
        $defaults = [
            'content' => $this->page->getContent(),
            'meta' => $this->page->getMeta(),
            'current_page' => $this->page,
            'pages' => $this->page->getRepository()->findAll(),
        ];

        /**
         * @var array $templateVars
         */
        $templateVars = Registry::get('templateVars');
        $templateVars += $defaults;

        return $templateVars;
    }
}
