<?php
/**
 * the filter class for content files
 */
namespace Phile\FilterIterator;

use Phile\Core\Container;

/**
 * Class ContentFileFilterIterator
 *
 * @package Phile\FilterIterator
 */
class ContentFileFilterIterator extends \FilterIterator
{
    /**
     * method to decide if file is filterd or not
     * @return bool
     */
    public function accept()
    {
        /**
         * @var \SplFileInfo $this
        */
        $ext = Container::getInstance()->get('Phile_Config')->get('content_ext');
        return (preg_match('/^[^\.]{1}.*' . $ext . '/', $this->getFilename()) > 0);
    }
}
