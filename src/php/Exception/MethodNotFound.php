<?php
/*
 * This file is part of the Lucid Container package.
 *
 * (c) Mike Thorn <mthorn@devlucid.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lucid\Router\Exception;

/**
 *
 * Exception thrown when the view or controller class does not contain the route's method.
 *
 * @package Lucid.Router
 *
 */
class MethodNotFound extends \Exception
{
    public function __construct(string $viewClass, string $controllerClass, string $method)
    {
        $this->message = 'Neither '.$viewClass.' or '.$controllerClass.' have a method named '.$method.'.';
    }
}