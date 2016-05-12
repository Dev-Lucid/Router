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
 * Exception thrown when the method for a route isn't allowed by the router's configuration
 *
 * @package Lucid.Router
 *
 */
class ForbiddenMethod extends \Exception
{
    public function __construct(string $viewClassName, string $controllerClassName, string $methodName)
    {
        $this->message = 'Router configuration does not allow either '.$viewClassName.'->'.$methodName.' or '.$controllerClassName.'->'.$methodName.' to be called';
    }
}