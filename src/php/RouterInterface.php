<?php
namespace Lucid\Component\Router;

interface RouterInterface
{
    public function determineRoute(string $route);
    public function addFixedRoute(string $action, string $viewOrController, string $className, string $classMethodName);
}