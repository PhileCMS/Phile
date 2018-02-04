<?php
/**
 * the filter class for content files
 */
namespace Phile\FilterIterator;

use Phile\Core\Registry;

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
        $ext = Registry::get('Phile_Settings')['content_ext'];
        return (preg_match('/^[^\.]{1}.*' . $ext . '/', $this->getFilename()) > 0);
    }
}
