# Router

An extremely simple router that is nearly entirely configuration free. I wrote this because today's router's seem to require way more configuration than should be necessary in my opinion. 

IMO, things a good router should do the following, *and only the following*:

* Ensure that only classes / methods you want exposed are exposed.
* Make it so that when you look at a url, you have an excellent first guess as to which class / method it should call.

And that's it. Here's an example of the router in action:

```php
# do a little setup. 
$router = new Lucid\Router\Router();
$router->setViewClassPrefixSuffix("App\View\", ''); # note: this is the default
$router->setControllerClassPrefixSuffix("App\Controller\", ''); # note: this is the default
$router->allowObjects('*');
$router->allowViewMethods('*', 'viewOne', 'viewList');
$router->allowControllerMethods('*', 'saveChanges', 'deleteOne');

$route1 = $router->parseRoute('/Users/viewOne/3');
error_log($route1->class.'->'.$route1->method);
 # This should write this to the error log: App\View\Users->viewOne

$route2 = $router->parseRoute('/Users/viewList');
error_log($route2->class.'->'.$route2->method);
 # This should write this to the error log: App\View\Users->viewList

$route3 = $router->parseRoute('/Users/saveChanges/3');
error_log($route3->class.'()->'.$route3->method);
 # This should write this to the error log: App\Controller\Users->saveChanges


$route4 = $router->parseRoute('/Users/methodNotFound');
 # this should throw a Lucid\Router\Exception\MethodNotFound exception

```


The return value from ->parseRoute is an instance of Lucid\Router\Route, which has three public properties:
* ->class: a string that contains the fully qualified class name for the route
* ->method: a string that contains the method name of the class for the route
* ->parameters: an associative array of the additional parameters in the route. The names of these parameters are configurable. By default, the first additional parameter is stored into an index named 'id', the second is named 'name', and from there on out the indices are named 'parameter#', where # is the ordinal position of the parameter in the url starting from 0. So, /Users/viewOne/34/joe/blow would result in a route that would look like this:
* 

```php
Lucid\Router\Route Object
(
    [class] => Users
    [method] => viewOne
    [parameters] => Array
        (
            [id] => 34
            [name] => joe
            [parameter2] => blow
        )

)
```

Note that instances of Lucid\Router\Route have a public method ->execute(...$constructParameters) which will attempt to instantiate the class and call the method with the parameters from the url, but this is probably not useful for most cases. You're much better off gluing together a dependency injection container with the Route object so that your view/controller classes can specify their dependencies via __construct parameters.

## Configuration

### Allowing Objects/Methods
There are 3 main things you'll likely want to set to use this router, and all 3 can be set to allow anything.  All 3 are set using setter methods:

* Which classes are allowed via routes. This is set by calling ->setAllowedObjects(...string $names). If you call this and pass '*' as a value, then the router will allow any class name
* Which view methods are allowed via routes. This is set by calling ->setAllowedViewMethods(string $objectName, ...$methodNames). 
* Which controller methods are allowed via routes. This is set by calling ->setAllowedControllerMethods(string $objectName, ...$methodNames).
 
Here's a detailed example of all 3 methods being used in a fairly restricted configuration:

```php

# First, instantiate the router
$router = new Lucid\Router\Router();

# Allow classes with a final name 'Users' or 'Products'.
# Anything else will throw a ForbiddenObject exception
$router->allowObjects('Users', 'Products');

# For the Users view class, only allow methods 'viewOne', and 'changePassword'
# Accessing any other method will throw a ForbiddenMethod exception
$router->allowViewMethods('Users', 'viewOne', 'changePassword);

# For the Users controller class, only allow methods 'save', and 'delete'
# Accessing any other method will throw a ForbiddenMethod exception
$router->allowControllerMethods('Users', 'save', 'delete);

# For the Products view class, only allow methods 'viewOne' and 'viewSimilar'
# Accessing any other method will throw a ForbiddenMethod exception
$router->allowViewMethods('Products', 'viewOne', 'viewSimilar');

# For the Products controller class, only allow methods 'save', and 'addToCart'
# Accessing any other method will throw a ForbiddenMethod exception
$router->allowControllerMethods('Products', 'save', 'addToCart);
```


### Configuring Namespaces
By default, the class name for a view is prefixed with 'App\View\' to make it fully qualifed, and the class name for a controller is prefixed with 'App\Controller'. You can change these defaults by calling two setter methods:

* ->setViewClassPrefixSuffix(string $prefix = '', string $suffix = '');
* ->setControllerClassPrefixSuffix(string $prefix = '', string $suffix = '');

Here's an example of them being used:

```php 
# first, instantiate the router
$router = new Lucid\Router\Router();
$router->allowObjects('*');
$router->allowViewMethods('*', 'viewOne', 'viewList');

# Let's see the default fully qualified name first:
$route = $router->parseRoute('/Users/viewOne');
echo($route->class);
# this should echo: App\View\User

# now let's change the configuration to customize the namespace for views:
$router->setViewClassPrefixSuffix("MyApp\MyViews\");
$route = $router->parseRoute('/Users/viewOne');
echo($route->class);
# this should echo: MyApp\MyViews\User
```

Changing the namespace for controllers works similarly:

```php 
# first, instantiate the router
$router = new Lucid\Router\Router();
$router->allowObjects('*');
$router->allowControllerMethods('*', 'saveChanges', 'deleteOne');

# Let's see the default fully qualified name first:
$route = $router->parseRoute('/Users/saveChanges');
echo($route->class);
# this should echo: App\Controller\User

# now let's change the configuration to customize the namespace for controllers:
$router->setControllerClassPrefixSuffix("MyApp\MyControllers\");
$route = $router->parseRoute('/Users/saveChanges');
echo($route->class);
# this should echo: MyApp\ MyControllers\User
```

### Configuring Parameter Names

In the default configuration, the first two parameters are named 'id' and 'name', and everything after that is simply stored as parameter# where # is their ordinal index in the route starting from 0. So, given a route like /Users/viewOne/34/joe/blow, the route object would look something like this:

```php
Lucid\Router\Route Object
(
    [class] => Users
    [method] => viewOne
    [parameters] => Array
        (
            [id] => 34
            [name] => joe
            [parameter2] => blow
        )

)
```

The default names of parameters can be set using the ->setParameterNames(...$names) function. For example:

```php 
$router = new Lucid\Router\Router();
$router->allowObjects('*');
$router->allowViewMethods('*', 'viewOne');
$router->setParameterNames('data_id');
print_r($router->parseRoute('/Users/viewOne/34/joe/blow'));
```

You should get output that looks like this:


```php
Lucid\Router\Route Object
(
    [class] => Users
    [method] => viewOne
    [parameters] => Array
        (
            [data_id] => 34
            [parameter1] => joe
            [parameter2] => blow
        )

)
```

## Suggested Configurations:

### Maximally permissive
This is a dead simple configuration that will get your project going, but really ought to be locked down later on:
```php
$router = new Lucid\Router\Router();
$router->allowObjects('*');
$router->allowViewMethods('*', '*');
$router->allowControllerMethods('*', '*');
```

Note, in this configuration it's possible to throw an AmbiguousMethod exception. If for example your Users view class and Users controller class both implement a method named 'doSomething', it isn't clear which one should be instantiated given a url like '/Users/doSomething'. 

### Mostly configuration free

Here's a configuration I like to use that relies on you naming your CRUD methods the same for all classes:

```php
$router = new Lucid\Router\Router();
$router->allowObjects('*');
$router->allowViewMethods('*', 'viewOne', 'viewList');
$router->allowControllerMethods('*', 'save', 'delete');
```

In this configuration, you could add new classes (ex: App\View\Products and App\ControllerProducts) and routes will automatically work for them. For example:

* /Products/viewOne will automatically allow/map to App\View\Products->viewOne
* /Products/viewList will automatically allow/map to App\View\Products->viewList
* /Products/save will automatically allow/map to App\Controller\Products->save
* /Products/delete will automatically allow/map to App\ Controller\Products->delete

### Mostly configuration free, with a few specifics

This is basically the same as the previous configuration, with some specifics added in. Note: If you've got object-specific rules for which methods are allowed, those are *always* used over rules defined for '*'.

```php
$router = new Lucid\Router\Router();
$router->allowObjects('*', 'Authentication');
$router->allowViewMethods('*', 'viewOne', 'viewList');
$router->allowControllerMethods('*', 'save', 'delete');
$router->allowViewMethods('Authentication', 'loginForm', 'resetPasswordForm');
$router->allowControllerMethods('Authentication', 'processLogin', 'processLogout', 'processPasswordReset');
```

In this configuration, you could add new classes (ex: App\View\Products and App\ControllerProducts) and routes will automatically work. Additionally, we've configured a view/controller named Authentication with some specific view / controller methods. 

## Useful Files 
I also included a couple useful files in the /useful folder:

* apache\_mod\_rewrite.conf: an example of how to configure mod_rewrite for apache to work with this class. 
* php\_inbuilt\_server.php: an example of how to use php's inbuilt server with rewriting to work with this class 


## What's wrong with other routers
