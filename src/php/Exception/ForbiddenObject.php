<?php
namespace Lucid\Router\Exception;

class ForbiddenObject extends \Exception
{
    public function __construct(string $viewName, string $controllerName)
    {
        $this->message = 'Router configuration does not allow either '.$viewName.' or '.$controllerName.' to be called via a route.';
    }
}