<?php
/*
 * This file is part of the Lucid Container package.
 *
 * (c) Mike Thorn <mthorn@devlucid.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lucid\Router;

/**
 *
 * A class containing a class, method, and parameters for that method that were parsed from a url.
 *
 * @package Lucid.Router
 *
 */
class Route
{
    /**
     *
     * A string that contains the fully qualified name of the class for the route
     *
     * @var string
     *
     */
    public $class = null;

    /**
     *
     * A string that contains the name of a the method for the route
     *
     * @var string
     *
     */
    public $method = null;

    /**
     *
     * A string that contains additional parameters that were parsed from a url
     *
     * @var array
     *
     */
    public $parameters = [];

    public function __construct(string $class, string $method, array $parameters=[])
    {
        $this->class      = $class;
        $this->method     = $method;
        $this->parameters = $parameters;
    }

    /**
     * Instantiates the route's class and calls the route's methods using the parameters found in the url
     *
     * @return mixed
     *
     */
    public function execute(...$constructParameters)
    {
        $class      = $this->class;
        $method     = $this->method;
        $parameters = array_values($this->parameters);

        $instance = new $class(...$constructParameters);
        return $instance->$method(...$parameters);
    }
}
