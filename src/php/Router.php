<?php
namespace Lucid\Router;

class Router
{
    protected $delimiter             = '/';
    protected $parameterNames        = ['id','name'];

    protected $viewClassPrefix       = 'App\\Controller\\';
    protected $viewClassSuffix       = '';
    protected $controllerClassPrefix = 'App\\Controller\\';
    protected $controllerClassSuffix = '';

    protected $objects               = [];
    protected $viewMethods           = [];
    protected $controllerMethods     = [];

    public function __construct()
    {
    }

    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function setParameterNames(...$names)
    {
        $this->parameterNames = $names;
    }

    public function setViewClassPrefixSuffix(string $prefix = '', string $suffix = '')
    {
        $this->viewClassPrefix = $prefix;
        $this->viewClassSuffix = $suffix;
    }

    public function setControllerClassPrefixSuffix(string $prefix = '', string $suffix = '')
    {
        $this->controllerClassPrefix = $prefix;
        $this->controllerClassSuffix = $suffix;
    }

    public function allowObjects(...$allowedObjects)
    {
        foreach ($allowedObjects as $object) {
            $this->objects[$object] = true;
        }
    }

    public function allowViewMethods(string $object, ...$allowedMethods)
    {
        if (isset($this->viewMethods[$object]) === false) {
            $this->viewMethods[$object] = [];
        }

        foreach ($allowedMethods as $allowedMethod) {
            $this->viewMethods[$object][$allowedMethod] = true;
        }
    }

    public function allowControllerMethods(string $object, ...$allowedMethods)
    {
        if (isset($this->controllerMethods[$object]) === false) {
            $this->controllerMethods[$object] = [];
        }

        foreach ($allowedMethods as $allowedMethod) {
            $this->controllerMethods[$object][$allowedMethod] = true;
        }
    }

    public function parseRoute(string $route) : Route
    {
        if (strpos($route, $this->delimiter) === 0){
            $route = substr($route, 1);
        }

        $routeParts = explode($this->delimiter, $route);
        if (count($routeParts) < 2) {
            throw new Exception\IncorrectFormat($route, $this->delimiter);
        }

        $object = array_shift($routeParts);
        $method = array_shift($routeParts);
        $parameters = [];
        $index = 0;
        while(count($routeParts) > 0){
            $name = $this->parameterNames[$index] ?? 'parameter'.$index;
            $parameters[$name] = array_shift($routeParts);
            $index++;
        }

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