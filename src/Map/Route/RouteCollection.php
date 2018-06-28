<?php

namespace Opportus\ObjectMapper\Map\Route;

use Opportus\ObjectMapper\Exception\InvalidArgumentException;

/**
 * The route collection.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var array $routes
     */
    protected $routes;

    /**
     * Constructs the route collection.
     *
     * @param  array $routes
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException
     */
    public function __construct(array $routes = array())
    {
        foreach ($routes as $route) {
            if (!$route instanceof RouteInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 1 passed to %s is invalid. Expects the argument to contain elements of type %s. Got an element of type %s.',
                    __METHOD__,
                    RouteInterface::class,
                    is_object($route) ? get_class($route) : gettype($route)
                ));
            }
        }

        $this->routes = $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes() : array
    {
        return $this->routes;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute(string $routeFqn) : RouteInterface
    {
        return $this->routes[$routeFqn] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(RouteInterface $route) : RouteCollectionInterface
    {
        $this->routes[$route->getFqn()] = $route;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRoutes(array $routes) : RouteCollectionInterface
    {
        foreach ($routes as $route) {
            if (!$route instanceof RouteInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Argument 1 passed to %s is invalid. Expects the argument to contain elements of type %s. Got an element of type %s.',
                    __METHOD__,
                    RouteInterface::class,
                    is_object($route) ? get_class($route) : gettype($route)
                ));
            }
        }

        foreach ($routes as $route) {
            $this->addRoute($route);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRoute(string $routeFqn) : RouteCollectionInterface
    {
        unset($this->routes[$routeFqn]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeRoutes() : RouteCollectionInterface
    {
        $this->routes = array();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasRoute(string $routeFqn) : bool
    {
        return isset($this->routes[$routeFqn]);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRoutes() : bool
    {
        return !empty($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->getRoute($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->addRoute($value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $this->removeRoute($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->hasRoute($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Deep clones the collection.
     */
    public function __clone()
    {
        $routes = $this->getRoutes();

        $this->removeRoutes();

        foreach ($routes as $route) {
            $this->addRoute(clone $route);
        }
    }
}

