<?php
/**
 * The page model
 */
namespace Phile\Model;

use Phile\Core\Container;
use Phile\Core\Router;
use Phile\Core\ServiceLocator;
use Phile\Repository\Page as Repository;
use Phile\ServiceLocator\MetaInterface;

/**
 * the Model class for a page
 *
 * @author  Frank Nägler
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Model
 */
class Page
{
    /**
     * @var \Phile\Model\Meta the meta model
     */
    protected $meta;

    /**
     * @var string the content
     */
    protected $content;

    /**
     * @var string the path to the original file
     */
    protected $filePath;

    /**
     * @var null|string the raw file
     */
    protected $rawData;

    /**
     * @var \Phile\ServiceLocator\ParserInterface the parser
     */
    protected $parser;

    /**
     * @var string the pageId of the page
     */
    protected $pageId;

    /**
     * @var string The content folder, as passed to the class constructor when initiating the object.
     */
    protected $contentFolder;

    /**
     * @var string content extension
     */
    protected $contentExtension;

    /** @var Repository */
    protected $repository;

    /**
     * the constructor
     *
     * @param string $filePath
     * @param string $folder
     */
    public function __construct($filePath, $folder = null)
    {
        $settings = Container::getInstance()->get('Phile_Config')->toArray();
        $this->contentFolder = $folder ?: $settings['content_dir'];
        $this->contentExtension = $settings['content_ext'];
        $this->setFilePath($filePath);

        /**
         * @triggerEvent before_load_content this event is triggered before the content is loaded
         *
         * @param            string filePath the path to the file
         * @param \Phile\Model\Page page     the page model
         */
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'before_load_content',
            ['filePath' => &$this->filePath, 'page' => &$this]
        );
        if (file_exists($this->filePath)) {
            $this->rawData = file_get_contents($this->filePath) ?: null;
            $this->parseRawData();
        }
        /**
         * @triggerEvent after_load_content this event is triggered after the content is loaded
         *
         * @param            string filePath the path to the file
         * @param            string rawData  the raw data
         * @param \Phile\Model\Page page     the page model
         */
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'after_load_content',
            [
                'filePath' => &$this->filePath,
                'rawData' => $this->rawData,
                'page' => &$this
            ]
        );

        $this->parser = ServiceLocator::getService('Phile_Parser');
    }

    /**
     * method to get content of page, this method returned the parsed content
     *
     * @return mixed
     */
    public function getContent()
    {
        /**
         * @triggerEvent before_parse_content this event is triggered before the content is parsed
         *
         * @param            string content the raw data
         * @param \Phile\Model\Page page    the page model
         */
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'before_parse_content',
            ['content' => $this->content, 'page' => &$this]
        );
        $content = $this->parser->parse($this->content);
        /**
         * @triggerEvent after_parse_content this event is triggered after the content is parsed
         *
         * @param            string content the parsed content
         * @param \Phile\Model\Page page    the page model
         */
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'after_parse_content',
            ['content' => &$content, 'page' => &$this]
        );

        return $content;
    }

    /**
     * set content of page
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * get raw (un-parsed) page content
     *
     * @return string
     */
    public function getRawContent()
    {
        return $this->content;
    }

    /**
     * get the meta model
     *
     * @return Meta
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * parse the raw content
     */
    protected function parseRawData(): void
    {
        $this->meta = new Meta($this->rawData);

        $content = '';
        if ($this->rawData !== null) {
            /** @var MetaInterface */
            $metaParser = ServiceLocator::getService('Phile_Parser_Meta');
            $content = $metaParser->extractContent($this->rawData);
        }

        $this->content = $content;
    }

    /**
     * Sets repository this page was retrieved by/belongs to
     *
     * @param Repository $repository
     * @return $this
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Gets repository this page belongs to
     *
     * @return Repository
     */
    public function getRepository()
    {
        if ($this->repository === null) {
            $this->repository = new Repository();
        }

        return $this->repository;
    }

    /**
     * get the title of page from meta information
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getMeta()->get('title');
    }

    /**
     * get Phile $pageId
     *
     * @param  string $filePath
     * @return string
     */
    protected function buildPageId($filePath)
    {
        $pageId = str_replace($this->contentFolder, '', $filePath);
        $pageId = str_replace($this->contentExtension, '', $pageId);
        $pageId = str_replace(DIRECTORY_SEPARATOR, '/', $pageId);
        $pageId = ltrim($pageId, '/');
        $pageId = preg_replace('/(?<=^|\/)index$/', '', $pageId);
        return $pageId;
    }

    /**
     * get the url of page
     *
     * @return string
     */
    public function getUrl()
    {
        $container = Container::getInstance();
        if ($container->has('Phile_Router')) {
            $router = $container->get('Phile_Router');
        } else {
            // BC: some old 1.x plugins may use Pages before the core is initialized
            $router = new Router;
        }
        return $router->urlForPage($this->pageId, false);
    }

    /**
     * set the filepath of the page
     *
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        $this->pageId = $this->buildPageId($this->filePath);
    }

    /**
     * get the filepath of the page
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * get the folder name
     *
     * @return string
     */
    public function getFolder()
    {
        return basename(dirname($this->getFilePath()));
    }

    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * get the previous page if one exist
     *
     * @return null|\Phile\Model\Page
     */
    public function getPreviousPage()
    {
        return $this->getRepository()->getPageOffset($this, -1);
    }

    /**
     * get the next page if one exist
     *
     * @return null|\Phile\Model\Page
     */
    public function getNextPage()
    {
        return $this->getRepository()->getPageOffset($this, 1);
    }
}
