<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware;

use OpenApi\Annotations\PathItem;
use Vyuldashev\LaravelOpenApi\Contracts\PathMiddleware;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class FirstPathMiddleware implements PathMiddleware
{
    public function before(RouteInformation $routeInformation): void
    {
        MiddlewareLog::$events[] = 'first-path-before';
    }

    public function after(PathItem $pathItem): PathItem
    {
        MiddlewareLog::$events[] = 'first-path-after';

        return $pathItem;
    }
}
