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
 * Exception thrown when neither the view class or controller class for a route is allowed by the router's configuration
 *
 * @package Lucid.Router
 *
 */
class ForbiddenObject extends \Exception
{
    public function __construct(string $viewName, string $controllerName)
    {
        $this->message = 'Router configuration does not allow either '.$viewName.' or '.$controllerName.' to be called via a route.';
    }
}