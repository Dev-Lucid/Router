<?php
namespace Lucid\Router\Exception;

class AmbiguousRequest extends \Exception
{
    public function __construct(string $viewClassName, string $controllerClassName, string $methodName)
    {
        $this->message = 'Ambiguous request. Method ->'.$methodName.'() exists in both '.$viewClassName.' and '.$controllerClassName.'. You may resolve this by either tightening the rules for which methods can be called in your router, or by renaming one of these methods to remove the ambiguity.';
    }
}