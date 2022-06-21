<?php
/**
 * the page repository class
 */
namespace Phile\Repository;

use Phile\Core\Container;
use Phile\Core\ServiceLocator;
use Phile\Core\Utility;
use Phile\ServiceLocator\CacheInterface;

/**
 * the Repository class for pages
 *
 * @author  Frank Nägler
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Repository
 */
class Page
{
    /**
     * @var array the settings array
     */
    protected $settings;

    /**
     * @var array object storage for initialized objects, to prevent multiple loading of objects.
     */
    protected $storage = [];

    /**
     * @var CacheInterface the cache implementation
     */
    protected $cache;

    /**
     * the constructor
     */
    public function __construct(?array $settings = null)
    {
        if ($settings === null) {
            $settings = Container::getInstance()->get('Phile_Config')->toArray();
        }
        $this->settings = $settings;
        $this->cache = ServiceLocator::getService('Phile_Cache');
    }

    /**
     * find a page by path
     *
     * @param string $pageId
     * @param string $folder
     *
     * @return null|\Phile\Model\Page
     */
    public function findByPath($pageId, $folder = null)
    {
        $folder = $folder ?: $this->settings['content_dir'];
        // be merciful to lazy third-party-usage and accept a leading slash
        $pageId = ltrim($pageId, '/');
        // 'sub/' should serve page 'sub/index'
        if ($pageId === '' || substr($pageId, -1) === '/') {
            $pageId .= 'index';
        }

        $file = $folder . $pageId . $this->settings['content_ext'];
        if (!file_exists($file)) {
            if (substr($pageId, -6) === '/index') {
                // try to resolve sub-directory 'sub/' to page 'sub'
                $pageId = substr($pageId, 0, strlen($pageId) - 6);
            } else {
                // try to resolve page 'sub' to sub-directory 'sub/'
                $pageId .= '/index';
            }
            $file = $folder . $pageId . $this->settings['content_ext'];
        }
        if (!file_exists($file)) {
            return null;
        }

        $file = str_replace('/', DS, $file);
        $folder = str_replace('/', DS, $folder);

        return $this->getPage($file, $folder);
    }

    /**
     * find all pages (*.md) files and returns an array of Page models
     *
     * @param array  $options
     * @param string $folder
     *
     * @return PageCollection of \Phile\Model\Page objects
     */
    public function findAll(array $options = array(), $folder = null)
    {
        $folder = $folder ?: $this->settings['content_dir'];
        return new PageCollection(
            function () use ($options, $folder): array {
                $options += $this->settings;
                // ignore files with a leading '.' in its filename
                $files = Utility::getFiles($folder, '\Phile\FilterIterator\ContentFileFilterIterator');
                $pages = [];
                $notFoundPage = $this->settings['not_found_page'] . $this->settings['content_ext'];
                foreach ($files as $file) {
                    if (str_replace($folder, '', $file) == $notFoundPage) {
                        // jump to next page if file is the 404 page
                        continue;
                    }
                    $pages[] = $this->getPage($file, $folder);
                }

                if (empty($options['pages_order'])) {
                    return $pages;
                }

                // parse search criteria
                $sorting = [];
                $terms = preg_split('/\s+/', $options['pages_order'], -1, PREG_SPLIT_NO_EMPTY);
                foreach ($terms as $term) {
                    $sub = explode('.', $term);
                    if (count($sub) > 1) {
                        $type = array_shift($sub);
                    } else {
                        $type = null;
                    }
                    $sub = explode(':', $sub[0]);
                    if (count($sub) === 1) {
                        $sub[1] = 'asc';
                    }
                    $sorting[] = array('type' => $type, 'key' => $sub[0], 'order' => $sub[1], 'string' => $term);
                }

                if (empty($sorting)) {
                    return $pages;
                }

                // prepare search criteria for array_multisort
                $sortHelper = [];
                foreach ($sorting as $sort) {
                    $key = $sort['key'];
                    $column = array();
                    foreach ($pages as $page) {
                        /**
                         * @var \Phile\Model\Page $page
                         */
                        $meta = $page->getMeta();
                        if ($sort['type'] === 'page') {
                            $method = 'get' . ucfirst($key);
                            $value = $page->$method();
                        } elseif ($sort['type'] === 'meta') {
                            $value = $meta->get($key);
                        } else {
                            trigger_error(
                                "Page order '{$sort['string']}' was ignored. Type '{$sort['type']}' not recognized.",
                                E_USER_WARNING
                            );
                            continue 2;
                        }
                        $column[] = $value;
                    }
                    $sortHelper[] = $column;
                    $sortHelper[] = constant('SORT_' . strtoupper($sort['order']));
                }
                $sortHelper[] = &$pages;

                call_user_func_array('array_multisort', $sortHelper);

                return $pages;
            }
        );
    }

    /**
     * return page at offset from $page in applied search order
     *
     * @param  \Phile\Model\Page $page
     * @param  int               $offset
     * @return null|\Phile\Model\Page
     */
    public function getPageOffset(\Phile\Model\Page $page, $offset = 0)
    {
        $pages = $this->findAll();
        $order = array();
        foreach ($pages as $p) {
            $order[] = $p->getFilePath();
        }
        $key = array_search($page->getFilePath(), $order) + $offset;
        if (!isset($order[$key])) {
            return null;
        }
        return $this->getPage($order[$key]);
    }

    /**
     * get page from cache or filepath
     *
     * @param string $filePath
     * @param string|null $folder
     *
     * @return \Phile\Model\Page
     */
    protected function getPage(string $filePath, ?string $folder = null): \Phile\Model\Page
    {
        $folder = $folder ?: $this->settings['content_dir'];
        $key = 'Phile_Model_Page_' . md5($filePath);
        if (isset($this->storage[$key])) {
            return $this->storage[$key];
        }

        if ($this->cache->has($key)) {
            $page = $this->cache->get($key);
        } else {
            $page = new \Phile\Model\Page($filePath, $folder);
            $this->cache->set($key, $page);
        }
        $page->setRepository($this);
        $this->storage[$key] = $page;

        return $page;
    }
}
