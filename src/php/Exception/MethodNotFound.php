<?php
namespace Lucid\Router\Exception;

class MethodNotFound extends \Exception
{
    public function __construct(string $viewClass, string $controllerClass, string $method)
    {
        $this->message = 'Neither '.$viewClass.' or '.$controllerClass.' have a method named '.$method.'.';
    }
}