<?php

namespace Opportus\ObjectMapper\Map\Route;

/**
 * The route collection interface.
 *
 * @version 1.0.0
 * @package Opportus\ObjectMapper\Map\Route
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface RouteCollectionInterface extends \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Gets the routes from the collection.
     *
     * @return array
     */
    public function getRoutes() : array;

    /**
     * Gets a route from the collection.
     *
     * @param  string $routeFqn
     * @return Opportus\ObjectMapper\Map\Route\RouteInterface
     */
    public function getRoute(string $routeFqn) : RouteInterface;

    /**
     * Adds a route to the collection.
     *
     * @param  Opportus\ObjectMapper\Map\Route\RouteInterface $route
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function addRoute(RouteInterface $route) : RouteCollectionInterface;

    /**
     * Adds the routes to the collection.
     *
     * @param  array $routes
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     * @throws Opportus\ObjectMapper\Exception\InvalidArgumentException When the param contains a type of element which is not an instance of RouteInterface
     */
    public function addRoutes(array $routes) : RouteCollectionInterface;

    /**
     * Removes a route from the collection.
     *
     * @param  string $routeFqn
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function removeRoute(string $routeFqn) : RouteCollectionInterface;

    /**
     * Removes the routes from the collection.
     *
     * @return Opportus\ObjectMapper\Map\Route\RouteCollectionInterface
     */
    public function removeRoutes() : RouteCollectionInterface;

    /**
     * Checks if the collection has a route.
     *
     * @param  string $routeFqn
     * @return bool
     */
    public function hasRoute(string $routeFqn) : bool;

    /**
     * Checks if the collection has any routes.
     *
     * @return bool
     */
    public function hasRoutes() : bool;
}

