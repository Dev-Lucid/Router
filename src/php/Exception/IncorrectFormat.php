<?php
namespace Lucid\Router\Exception;

class IncorrectFormat extends \Exception
{
    public function __construct(string $badRoute, string $delimiter)
    {
        $this->message = 'Route '.$badRoute.' is not in the correct format. Route must be a string consisting of at least two parts, separated by the following delimiter: '.$delimiter."\n\n The first part is the final name of either a view or controller class. The second part is the method name to call in that class. Whether the route refers to a view or controller is resolved via the router's configuration.";
    }
}