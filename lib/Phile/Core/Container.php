<?php
/**
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Psr\Container\ContainerInterface;
use Phile\Exception\ContainerNotFoundException as NotFoundException;

/**
 * Implements a simple PSR-11 container
 */
class Container implements ContainerInterface
{
    protected $config = [];

    /** @var ContainerInterface */
    protected static $instance;

    /** @var array raw items */
    protected $raw = [];

    /** @var array evaluated items */
    protected $build = [];

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Sets instance for static usage
     *
     * @param ContainerInterface $instance
     */
    public static function setInstance(ContainerInterface $instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Gets instance for static usage
     *
     * @return ContainerInterface
     */
    public static function getInstance(): ContainerInterface
    {
        return self::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return array_key_exists($id, $this->raw);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(
                sprintf('Item "%s" not found in Container.', $id),
                1519111836
            );
        }
        if (!is_callable($this->raw[$id])) {
            return $this->raw[$id];
        }
        if (!isset($this->build[$id])) {
            $this->build[$id] = call_user_func($this->raw[$id], $this);
            $this->typeCheck($id, $this->build[$id]);
        }
        return $this->build[$id];
    }

    /**
     * Set an object
     *
     * @param string $id Identifier for the object to set
     * @param mixed $object Object to set
     */
    public function set($id, $object)
    {
        if (!is_callable($object)) {
            $this->typeCheck($id, $object);
        }
        $this->raw[$id] = $object;
    }

    /**
     * Check object-type matches required type for that ID
     *
     * @param string $id
     * @param mixed $object
     * @return void
     * @throws \Phile\Exception\ContainerException
     */
    protected function typeCheck($id, $object)
    {
        if (!isset($this->config['types'][$id])) {
            return;
        }
        $requirement = $this->config['types'][$id];
        if (get_class($object) === $requirement || is_subclass_of($object, $requirement)) {
            return;
        }
        $msg = sprintf('The Container-item "%s" must implement the interface: "%s"', $id, $requirement);
        throw new \Phile\Exception\ContainerException($msg, 1398536617);
    }
}
