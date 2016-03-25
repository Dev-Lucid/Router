<?php
namespace Lucid\Component\Router;

class Router implements RouterInterface
{
    protected $logger = null;
    public    $autoRoutingViews       = true;
    public    $autoRoutingControllers = true;
    protected $fixedRoutes = [];


    public function __construct($logger)
    {
        if (is_null($logger)) {
            $this->logger = new \Lucid\Component\BasicLogger\BasicLogger();
        } else {
            if (is_object($logger) === false || in_array('Psr\\Log\\LoggerInterface', class_implements($logger)) === false) {
                throw new \Exception('Factory contructor parameter $logger must either be null, or implement Psr\\Log\\LoggerInterface. If null is passed, then an instance of Lucid\\Component\\BasicLogger\\BasicLogger will be instantiated instead, and all messages will be passed along to error_log();');
            }
            $this->logger = $logger;
        }
    }

    public function determineRoute($action)
    {
        $routeFormatMessage = 'Incorrect format for action: '.$action.'. An action must contain 2-3 parts, separated by a period. The first part is either the string controller or view, the second part is either a name of the controller or view class (without the namespace), and the third part is the name of the method to be called of that class. If the name is ommitted for a view, the method name will be assumed to be ->render().';

        if (isset($this->fixedRoutes[$action])) {
            return $this->fixedRoutes[$action];
        }

        $splitAction = explode('.', $action);
        if (count($splitAction) < 2 || count($splitAction) > 3) {
            throw new \Exception($routeFormatMessage);
        }

        $route = [];
        $route['type'] = array_shift($splitAction);
        if ($route['type'] != 'view' && $route['type'] != 'controller') {
            throw new \Exception($routeFormatMessage);
        }

        if($route['type'] == 'view' && $this->autoRoutingViews === false) {
            throw new \Exception('Could not find static route for '.$action.', and $router->autoRoutingViews === false. ');
        }
        if($route['type'] == 'controller' && $this->autoRoutingControllers === false) {
            throw new \Exception('Could not find static route for '.$action.', and $router->autoRoutingControllers === false. ');
        }

        $route['class'] = array_shift($splitAction);
        if (count($splitAction) > 0) {
            $route['method'] = array_shift($splitAction);
        } else {
            if($route['type'] == 'controller') {
                throw new \Exception('Incorrect format for action: '.$action.'. An action must contain 2-3 parts, separated by a period. The first part is either the string controller or view, the second part is either a name of the controller or view class (without the namespace), and the third part is the name of the method to be called of that class. If the name is ommitted for a view, the method name will be assumed to be ->render().');
            }
            $route['method'] = 'render';
        }

        return $route;
    }

    public function addRoute(string $action, string $type, string $classFinalName, string $classMethodName)
    {
        $this->fixedRoutes[$action] = ['type'=>$type, 'class'=>$classFinalName, 'method'=>$classMethodName];
    }
}