<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware;

use OpenApi\Annotations\PathItem;
use Vyuldashev\LaravelOpenApi\Contracts\PathMiddleware;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class SecondPathMiddleware implements PathMiddleware
{
    public function before(RouteInformation $routeInformation): void
    {
        MiddlewareLog::$events[] = 'second-path-before';
    }

    public function after(PathItem $pathItem): PathItem
    {
        MiddlewareLog::$events[] = 'second-path-after';

        return $pathItem;
    }
}
