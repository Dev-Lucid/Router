<?php
namespace Lucid\Router;

class Route
{
    public $class      = null;
    public $method     = null;
    public $parameters = [];

    public function __construct(string $class, string $method, array $parameters=[])
    {
        $this->class = $class;
        $this->method = $method;
        $this->parameters = $parameters;
    }

    public function execute(...$constructParameters)
    {
        $class = $this->class;
        $method = $this->method;
        $parameters = array_values($this->parameters);
        $object = new $class(...$constructParameters);
        return $object->$method(...$parameters);
    }
}
