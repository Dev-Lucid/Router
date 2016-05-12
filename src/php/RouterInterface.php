<?php
namespace Lucid\Router;

interface RouterInterface
{
    public function parseRoute(string $route);
}