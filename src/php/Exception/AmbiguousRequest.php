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
 * Exception thrown when Router cannot determine if a method should be called from
 * the view class or the controller class
 *
 * @package Lucid.Router
 *
 */
class AmbiguousRequest extends \Exception
{
    public function __construct(string $viewClassName, string $controllerClassName, string $methodName)
    {
        $this->message = 'Ambiguous request. Method ->'.$methodName.'() exists in both '.$viewClassName.' and '.$controllerClassName.'. You may resolve this by either tightening the rules for which methods can be called in your router, or by renaming one of these methods to remove the ambiguity.';
    }
}