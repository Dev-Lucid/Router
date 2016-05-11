<?php

class ExecuteTest_View_TestClass
{
    public function method1($id)
    {
        return 'method1 called with id='.$id;
    }
}

class ExecuteTest_Controller_TestClass
{
    public function method2($id, $name)
    {
        return 'method2 called with id='.$id.',name='.$name;
    }
}

class ExecuteTest_Controller_TestClass2
{
    public $parameterPassedInConstruct;
    public function __construct($constructParameter)
    {
        $this->parameterPassedInConstruct = $constructParameter;
    }

    public function method3()
    {
        return 'parameterPassedInConstruct='.$this->parameterPassedInConstruct;
    }
}


class ExecuteTest extends \PHPUnit_Framework_TestCase
{


    public function setupRouter()
    {
        $router = new Lucid\Router\Router();
        $router->setViewClassPrefixSuffix('ExecuteTest_View_');
        $router->setControllerClassPrefixSuffix('ExecuteTest_Controller_');
        $router->allowObjects('TestClass', 'TestClass2');
        $router->allowViewMethods('TestClass', 'method1');
        $router->allowControllerMethods('TestClass', 'method2');
        $router->allowControllerMethods('TestClass2', 'method3');
        return $router;
    }

    public function testExecute1()
    {
        $router = $this->setupRouter();

        $route1 = $router->parseRoute('/TestClass/method1/5');
        $this->assertEquals('method1 called with id=5', $route1->execute());

        $route2 = $router->parseRoute('/TestClass/method2/5/joe');
        $this->assertEquals('method2 called with id=5,name=joe', $route2->execute());
    }

    public function testExecuteConstructParameters()
    {
        $router = $this->setupRouter();

        $route1 = $router->parseRoute('/TestClass2/method3');
        $this->assertEquals('parameterPassedInConstruct=test', $route1->execute('test'));
    }
}