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
 * Exception thrown when the string passed to ->parseRoute does not contain at least two parts.
 *
 * @package Lucid.Router
 *
 */
class IncorrectFormat extends \Exception
{
    public function __construct(string $badRoute, string $delimiter)
    {
        $this->message = 'Route '.$badRoute.' is not in the correct format. Route must be a string consisting of at least two parts, separated by the following delimiter: '.$delimiter."\n\n The first part is the final name of either a view or controller class. The second part is the method name to call in that class. Whether the route refers to a view or controller is resolved via the router's configuration.";
    }
}