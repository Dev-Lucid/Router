<?php
use Lucid\Component\Router\Router;

class ParseTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $router = new Router();

        $result = $router->determineRoute('MyClass.view.MyMethod');
        $this->assertEquals(print_r($result, true), print_r(['type'=>'view', 'class'=>'MyClass', 'method'=>'MyMethod'], true));
        #$this->assertEqual(is_array($container->getArray()));
    }

    public function testAutoRoute()
    {
    }
}