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
 * Exception thrown when the class for a route doesn't exist and can't be autoloaded
 *
 * @package Lucid.Router
 *
 */
class ClassNotFound extends \Exception
{
    public function __construct(string $viewClassName, string $controllerClassName)
    {
        $this->message = 'Neither '.$viewClassName.' or '.$controllerClassName.' exist.';
    }
}