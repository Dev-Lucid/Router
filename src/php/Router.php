<?php
namespace Lucid\Component\Router;

class Router implements RouterInterface
{
    protected $logger = null;
    public    $autoRouting = true;
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
        if (isset($this->fixedRoutes[$action])) {
            return $this->fixedRoutes[$action];
        }

        if ($this->autoRouting === false) {
            throw new \Exception('Could not locate a route for action '.$action.', and router->autoRouting was set to false. Either enable autoRouting ($router->autoRouting = true;), or add a route for this action: $router->addRoute($action, $(\'view\' or \'controller\'), $final class name for that view or controller), $(method name of that view or controller)');
        }

        $splitAction = explode('.', $action);
        if (count($splitAction) != 2) {
            throw new \Exception('Incorrect format for action: '.$action.'. An action must contain two parts, separated by a period. The leftside part is either a controller name or the word \'view\', and the rightside part is either a method of the controller, or the name of the view to load.');
        }

        if ($splitAction[0] == 'view') {
            return ['type'=>'view', 'class'=>$splitAction[1], 'method'=>'render'];
        } else {
            return ['type'=>'controller', 'class'=>$splitAction[0], 'method'=>$splitAction[1]];
        }
    }

    public function addRoute(string $action, string $type, string $classFinalName, string $classMethodName)
    {
        $this->fixedRoutes[$action] = ['type'=>$type, 'class'=>$classFinalName, 'method'=>$classMethodName];
    }
}