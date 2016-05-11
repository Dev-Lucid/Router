<?php
namespace Lucid\Router\Exception;

class ClassNotFound extends \Exception
{
    public function __construct(string $viewClassName, string $controllerClassName)
    {
        $this->message = 'Neither '.$viewClassName.' or '.$controllerClassName.' exist.';
    }
}