<?php
use Lucid\Component\Router\Router;

class ParseTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $router = new Router();

        $result = $router->determineRoute('MyClass.view.MyMethod');
        $goodResult = ['type'=>'view', 'class'=>'MyClass', 'method'=>'MyMethod'];
        ksort($result);
        ksort($goodResult);
        $this->assertEquals(print_r($result, true), print_r($goodResult, true));

        $result = $router->determineRoute('MyClass.controller.MyMethod');
        $goodResult = ['type'=>'controller', 'class'=>'MyClass', 'method'=>'MyMethod'];
        ksort($result);
        ksort($goodResult);
        $this->assertEquals(print_r($result, true), print_r($goodResult, true));
    }

    public function testAutoRoute()
    {
        $router = new Router();
        $router->addFixedRoute('mytestroute1', 'view',       'MyClass', 'MyMethod');
        $router->addFixedRoute('mytestroute2', 'controller', 'MyClass', 'MyMethod');

        $result = $router->determineRoute('mytestroute1');
        $goodResult = ['type'=>'view', 'class'=>'MyClass', 'method'=>'MyMethod'];
        ksort($result);
        ksort($goodResult);
        $this->assertEquals(print_r($result, true), print_r($goodResult, true));

        $result = $router->determineRoute('mytestroute2');
        $goodResult = ['type'=>'controller', 'class'=>'MyClass', 'method'=>'MyMethod'];
        ksort($result);
        ksort($goodResult);
        $this->assertEquals(print_r($result, true), print_r($goodResult, true));
    }

    public function testDefaultViewProperty()
    {
        $router = new Router();

        $result = $router->determineRoute('MyClass.view');
        $goodResult = ['type'=>'view', 'class'=>'MyClass', 'method'=>'index'];
        ksort($result);
        ksort($goodResult);
        $this->assertEquals(print_r($result, true), print_r($goodResult, true));

        $router->defaultViewMethod = 'MyMethod';
        $result = $router->determineRoute('MyClass.view');
        $goodResult = ['type'=>'view', 'class'=>'MyClass', 'method'=>'MyMethod'];
        ksort($result);
        ksort($goodResult);
        $this->assertEquals(print_r($result, true), print_r($goodResult, true));
    }
}