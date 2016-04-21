<?php
namespace Lucid\Component\Router;

class Router implements RouterInterface
{
    protected $logger            = null;
    protected $fixedRoutes       = [];
    public $autoRouteViews       = true;
    public $autoRouteControllers = true;
    public $defaultViewMethod    = 'index';

    public function __construct($logger=null)
    {
        if (is_null($logger)) {
            $this->logger = new \Lucid\Component\BasicLogger\BasicLogger();
        } else {
            if (is_object($logger) === false || in_array('Psr\\Log\\LoggerInterface', class_implements($logger)) === false) {
                throw new \Exception('Router contructor parameter $logger must either be null, or implement Psr\\Log\\LoggerInterface. If null is passed, then an instance of Lucid\\Component\\BasicLogger\\BasicLogger will be instantiated instead, and all messages will be passed along to error_log();');
            }
            $this->logger = $logger;
        }
    }

    public function determineRoute(string $route) : array
    {
        $routeFormatMessage = 'Incorrect format for route: '.$route.'. A route must contain 2-3 parts, separated by a period. The first part is either the name of a controller class or view class, the second part is either \'controller\' or \'view\', and the third part is the name of the method to be called of that class. For example, MyClass.controller.controllerMethodName, or MyClass.view.viewMethodName. If the name is ommitted for a view, the method name will be assumed to be the $defaultViewMethod property of the router object.';

        if (isset($this->fixedRoutes[$route]) === true) {
            return $this->fixedRoutes[$route];
        }

        $splitRoute = explode('.', $route);
        if (count($splitRoute) == 2 && $splitRoute[1] == 'view') {
            $splitRoute[] = $this->defaultViewMethod;
        }
        if (count($splitRoute) !=  3) {
            throw new \Exception($routeFormatMessage);
        }

        $routeArray = [];
        $routeArray['class']  = array_shift($splitRoute);
        $routeArray['type']   = array_shift($splitRoute);
        $routeArray['method'] = array_shift($splitRoute);

        if ($routeArray['type'] != 'view' && $routeArray['type'] != 'controller') {
            throw new \Exception($routeFormatMessage);
        }

        if($routeArray['type'] == 'view' && $this->autoRouteViews === false) {
            throw new \Exception('Could not find static route for '.$route.', and $router->autoRoutingViews === false. ');
        }
        if($routeArray['type'] == 'controller' && $this->autoRouteControllers === false) {
            throw new \Exception('Could not find static route for '.$route.', and $router->autoRoutingControllers === false. ');
        }

        return $routeArray;
    }

    public function addFixedRoute(string $route, string $viewOrController, string $classFinalName,  string $className)
    {
        $this->fixedRoutes[$route] = ['type'=>$viewOrController, 'class'=>$classFinalName, 'method'=>$className];
        return $this;
    }
}