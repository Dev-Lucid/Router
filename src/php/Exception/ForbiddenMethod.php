<?php
namespace Lucid\Router\Exception;

class ForbiddenMethod extends \Exception
{
    public function __construct(string $viewClassName, string $controllerClassName, string $methodName)
    {
        $this->message = 'Router configuration does not allow either '.$viewClassName.'->'.$methodName.' or '.$controllerClassName.'->'.$methodName.' to be called';
    }
}