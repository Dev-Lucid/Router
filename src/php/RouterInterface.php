<?php
namespace Lucid\Component\Router;

interface RouterInterface
{
    public function determineRoute($parameters);
    public function addRoute(string $action, string $type, string $classFinalName, string $classMethodName);
}