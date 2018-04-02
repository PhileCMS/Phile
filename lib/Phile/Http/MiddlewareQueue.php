<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Http;

use IteratorAggregate;
use IteratorIterator;
use Psr\Http\Server\MiddlewareInterface;
use SplPriorityQueue;
use Traversable;

/**
 * Middleware queue
 */
class MiddlewareQueue implements IteratorAggregate
{
    public const DEFAULT_PRIORITY = 100;
    
    /** @var int counter for FIFO order for items with same priority */
    protected $serial = PHP_INT_MAX;

    /** @var SplPriorityQueue middleware */
    protected $queue;

    public function __construct()
    {
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Adds middleware to queue
     *
     * @param MiddlewareInterface $middleware
     * @param int $priority
     * @return self
     */
    public function add(MiddlewareInterface $middleware, int $priority = self::DEFAULT_PRIORITY): self
    {
        $this->queue->insert($middleware, [$priority, $this->serial--]);
        return $this;
    }

    /**
     * Implements IteratorAggregate
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new IteratorIterator($this->queue);
    }
}
