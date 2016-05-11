<?php

class DataTest_View_TestClass
{
    public function method1()
    {
    }

    public function method3()
    {
    }
}

class DataTest_Controller_TestClass
{
    public function method2()
    {
    }

    public function method3()
    {
    }
}

class DataTest extends \PHPUnit_Framework_TestCase
{


    public function setupRouter()
    {
        $router = new Lucid\Router\Router();
        $router->setViewClassPrefixSuffix('ParseTest_View_');
        $router->setControllerClassPrefixSuffix('ParseTest_Controller_');
        $router->allowObjects('TestClass');
        $router->allowViewMethods('TestClass', 'method1');
        $router->allowControllerMethods('TestClass', 'method2', 'method4');
        return $router;
    }

    public function testData1()
    {
        $router = $this->setupRouter();

        $route = $router->parseRoute('/TestClass/method1/5');
        $this->assertEquals('ParseTest_View_TestClass', $route->class);
        $this->assertEquals('method1', $route->method);
        $this->assertEquals('5', $route->parameters['id']);


        $route = $router->parseRoute('/TestClass/method1/5/joe');
        $this->assertEquals('5', $route->parameters['id']);
        $this->assertEquals('joe', $route->parameters['name']);

        $route = $router->parseRoute('/TestClass/method1/5/joe/blow');
        $this->assertEquals('5', $route->parameters['id']);
        $this->assertEquals('joe', $route->parameters['name']);
        $this->assertEquals('blow', $route->parameters['parameter2']);
    }
}