<?php
/*
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Exception;

use \Psr\Container\NotFoundExceptionInterface;

/**
 * Container Exception
 */
class ContainerNotFoundException extends AbstractException implements NotFoundExceptionInterface
{
}
