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
 * The actual Router class. has setter methods for configuration, and ->parseRoute()
 *
 * @package Lucid.Router
 *
 */
class Router implements RouterInterface
{
    /**
     *
     * A string used to explode routes into parts
     *
     * @var string
     *
     */
    protected $delimiter = '/';

    /**
     *
     * An array of the default names of additional parameters found in a url
     *
     * @var array
     *
     */
    protected $parameterNames = ['id','name'];

    /**
     *
     * The prefix used for view classes to fully qualify them
     *
     * @var string
     *
     */
    protected $viewClassPrefix = 'App\\View\\';

    /**
     *
     * The suffix used for view classes to fully qualify them
     *
     * @var string
     *
     */
    protected $viewClassSuffix = '';

    /**
     *
     * The prefix used for controller classes to fully qualify them
     *
     * @var string
     *
     */
    protected $controllerClassPrefix = 'App\\Controller\\';

    /**
     *
     * The suffix used for controller classes to fully qualify them
     *
     * @var string
     *
     */
    protected $controllerClassSuffix = '';

    /**
     *
     * An array of allowed objects
     *
     * @var array
     *
     */
    protected $objects = [];

    /**
     *
     * An array of arrays of allowed methods for views. The key for the first array is the final
     * name of a specific view class, or '*'. The value of the inner array is the name of the
     * allowed method.
     *
     * @var array
     *
     */
    protected $viewMethods = [];

    /**
     *
     * An array of arrays of allowed methods for controllers. The key for the first array is the final
     * name of a specific controller class, or '*'. The value of the inner array is the name of the
     * allowed method.
     *
     * @var array
     *
     */
    protected $controllerMethods = [];

    /**
     *
     * Constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     *
     * Sets the delimiter to use when exploding routes
     *
     * @return null
     *
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     *
     * Sets the default names for parameters found in the url when parsing
     *
     * @return null
     *
     */
    public function setParameterNames(...$names)
    {
        $this->parameterNames = $names;
    }

    /**
     *
     * Sets protected properties $viewClassPrefix and $viewClassSuffix
     *
     * @return null
     *
     */
    public function setViewClassPrefixSuffix(string $prefix = '', string $suffix = '')
    {
        $this->viewClassPrefix = $prefix;
        $this->viewClassSuffix = $suffix;
    }

    /**
     *
     * Sets protected properties $controllerClassPrefix and $controllerClassSuffix
     *
     * @return null
     *
     */
    public function setControllerClassPrefixSuffix(string $prefix = '', string $suffix = '')
    {
        $this->controllerClassPrefix = $prefix;
        $this->controllerClassSuffix = $suffix;
    }

    /**
     * Adds new objects to protected array $objects
     *
     * @return null
     *
     */
    public function allowObjects(...$allowedObjects)
    {
        foreach ($allowedObjects as $object) {
            $this->objects[$object] = true;
        }
    }

    /**
     * Adds allowed view methods for an object
     *
     * @return null
     *
     */
    public function allowViewMethods(string $object, ...$allowedMethods)
    {
        if (isset($this->viewMethods[$object]) === false) {
            $this->viewMethods[$object] = [];
        }

        foreach ($allowedMethods as $allowedMethod) {
            $this->viewMethods[$object][$allowedMethod] = true;
        }
    }

    /**
     * Adds allowed controller methods for an object
     *
     * @return null
     *
     */
    public function allowControllerMethods(string $object, ...$allowedMethods)
    {
        if (isset($this->controllerMethods[$object]) === false) {
            $this->controllerMethods[$object] = [];
        }

        foreach ($allowedMethods as $allowedMethod) {
            $this->controllerMethods[$object][$allowedMethod] = true;
        }
    }

    /**
     * Parses a string, determines what class and method the route should map to, and returns an
     * instance of Lucid\Router\Route
     *
     * @return Lucid\Router\Route
     *
     */
    public function parseRoute(string $route) : Route
    {
        # removing a leading / if necessary
        if (strpos($route, $this->delimiter) === 0){
            $route = substr($route, 1);
        }

        # split the route using the configured delimiter, throw an exception if there aren't
        # at least two parts to the route string
        $routeParts = explode($this->delimiter, $route);
        if (count($routeParts) < 2) {
            throw new Exception\IncorrectFormat($route, $this->delimiter);
        }

        $object = array_shift($routeParts);
        $method = array_shift($routeParts);
        $parameters = [];

        # loop over remaining parts of the route and build the parameters array
        $index = 0;
        while(count($routeParts) > 0){
            $name = $this->parameterNames[$index] ?? 'parameter'.$index;
            $parameters[$name] = array_shift($routeParts);
            $index++;
        }

        # Determine whether this refers to a view or a controller method. Start by disallowing both,
        # allow if we find matching rules
        $allowView       = false;
        $allowController = false;
        $viewClass       = $this->viewClassPrefix . $object . $this->viewClassSuffix;
        $controllerClass = $this->controllerClassPrefix . $object . $this->controllerClassSuffix;

        if (isset($this->objects[$object]) === false && isset($this->objects['*']) === false) {
            throw new Exception\ForbiddenObject($viewClass, $controllerClass);
        }

        # first, check if both classes exist. If neither exists, throw exception
        if (class_exists($viewClass) === false && class_exists($controllerClass) === false) {
            throw new Exception\ClassNotFound($viewClass, $controllerClass);
        }

        # if the view class exists, ensure that the method is allowed to be called
        if (class_exists($viewClass) === true) {
            if (isset($this->viewMethods[$object]) === true) {
                if (isset($this->viewMethods[$object][$method]) === true || isset($this->viewMethods[$object]['*']) === true) {
                    $allowView = true;
                }
            } elseif (isset($this->viewMethods['*']) === true) {
                if (isset($this->viewMethods['*'][$method]) === true || isset($this->viewMethods['*']['*']) === true) {
                    $allowView = true;
                }
            }
        }

        # if the controller class exists, ensure that the method is allowed to be called
        if (class_exists($controllerClass) === true) {
            if (isset($this->controllerMethods[$object]) === true) {
                if (isset($this->controllerMethods[$object][$method]) === true || isset($this->controllerMethods[$object]['*']) === true) {
                    $allowController = true;
                }
            } elseif (isset($this->controllerMethods['*']) === true) {
                if (isset($this->controllerMethods['*'][$method]) === true || isset($this->controllerMethods['*']['*']) === true) {
                    $allowController = true;
                }
            }
        }

        if ($allowController === false && $allowView === false) {
            throw new Exception\ForbiddenMethod($viewClass, $controllerClass, $method);
        }


        if (class_exists($controllerClass) === true && method_exists($controllerClass, $method) === false) {
            $allowController = false;
        }
        if (class_exists($viewClass) === true && method_exists($viewClass, $method) === false) {
            $allowView = false;
        }
        if ($allowController === false && $allowView === false) {
            throw new Exception\MethodNotFound($viewClass, $controllerClass, $method);
        }

        if ($allowController === true && $allowView === true) {
            throw new Exception\AmbiguousRequest($viewClass, $controllerClass, $method);
        }

        if ($allowView) {
            $routeObject = new Route($viewClass, $method, $parameters);
        } elseif ($allowController) {
            $routeObject = new Route($controllerClass, $method, $parameters);
        }

        return $routeObject;
    }
}