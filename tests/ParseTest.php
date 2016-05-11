<?php

class ParseTest_View_TestClass
{
    public function method1()
    {
    }

    public function method3()
    {
    }
}

class ParseTest_Controller_TestClass
{
    public function method2()
    {
    }

    public function method3()
    {
    }
}

class ParseTest_View_TestClass2
{
    public function method1()
    {
    }
}

class ParseTest extends \PHPUnit_Framework_TestCase
{
    public function setupReallyAmbiguousRouter()
    {
        $router = new Lucid\Router\Router();
        $router->setViewClassPrefixSuffix('ParseTest_View_');
        $router->setControllerClassPrefixSuffix('ParseTest_Controller_');
        $router->allowObjects('*');
        $router->allowViewMethods('*', '*');
        $router->allowControllerMethods('*', '*');
        return $router;
    }

    public function setupLockedDownRouter()
    {
        $router = new Lucid\Router\Router();
        $router->setViewClassPrefixSuffix('ParseTest_View_');
        $router->setControllerClassPrefixSuffix('ParseTest_Controller_');
        $router->allowObjects('TestClass');
        $router->allowViewMethods('TestClass', 'method1');
        $router->allowControllerMethods('TestClass', 'method2', 'method4');
        return $router;
    }

    public function testParse()
    {
        $router = $this->setupReallyAmbiguousRouter();

        $route = $router->parseRoute('TestClass/method1');
        $this->assertEquals('ParseTest_View_TestClass', $route->class);
        $this->assertEquals('method1', $route->method);

        $route = $router->parseRoute('TestClass/method2');
        $this->assertEquals('ParseTest_Controller_TestClass', $route->class);
        $this->assertEquals('method2', $route->method);
    }

    public function testAmbiguous()
    {
        $router = $this->setupReallyAmbiguousRouter();
        $this->setExpectedException(Lucid\Router\Exception\AmbiguousRequest::class);
        $router->parseRoute('TestClass/method3');
    }

    public function testNotFound()
    {
        $router = $this->setupReallyAmbiguousRouter();
        $this->setExpectedException(Lucid\Router\Exception\ClassNotFound::class);
        $router->parseRoute('/TestClassDoesntExist/method3');
    }

    public function testRouteFormat1()
    {
        $router = $this->setupReallyAmbiguousRouter();
        $this->setExpectedException(Lucid\Router\Exception\IncorrectFormat::class);
        $router->parseRoute('/TestClassDoesntExist->method3');
    }
    public function testRouteFormat2()
    {
        $router = $this->setupReallyAmbiguousRouter();
        $this->setExpectedException(Lucid\Router\Exception\IncorrectFormat::class);
        $router->parseRoute('/view.TestClass.method2');
    }

    public function testForbiddenObject()
    {
        $router1 = $this->setupReallyAmbiguousRouter();
        $route = $router1->parseRoute('TestClass2/method1');
        $this->assertEquals('ParseTest_View_TestClass2', $route->class);
        $this->assertEquals('method1', $route->method);

        $router2 = $this->setupLockedDownRouter();
        $this->setExpectedException(Lucid\Router\Exception\ForbiddenObject::class);
        $route = $router2->parseRoute('TestClass2/method1');
    }

    public function testForbiddenMethod()
    {
        $router1 = $this->setupReallyAmbiguousRouter();
        $route = $router1->parseRoute('TestClass2/method1');
        $this->assertEquals('ParseTest_View_TestClass2', $route->class);
        $this->assertEquals('method1', $route->method);

        $router2 = $this->setupLockedDownRouter();

        $route = $router2->parseRoute('TestClass/method1');
        $this->assertEquals('ParseTest_View_TestClass', $route->class);
        $this->assertEquals('method1', $route->method);

        $route = $router2->parseRoute('TestClass/method2');
        $this->assertEquals('ParseTest_Controller_TestClass', $route->class);
        $this->assertEquals('method2', $route->method);

        $this->setExpectedException(Lucid\Router\Exception\ForbiddenMethod::class);
        $route = $router2->parseRoute('TestClass/method3');
    }

    public function testMethodNotFound()
    {
        $router1 = $this->setupLockedDownRouter();
        $this->setExpectedException(Lucid\Router\Exception\MethodNotFound::class);
        $route = $router1->parseRoute('TestClass/method4');
    }
}