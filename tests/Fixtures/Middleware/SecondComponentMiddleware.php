<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware;

use OpenApi\Annotations\Components;
use Vyuldashev\LaravelOpenApi\Contracts\ComponentMiddleware;

class SecondComponentMiddleware implements ComponentMiddleware
{
    public function after(Components $components): void
    {
        MiddlewareLog::$events[] = 'second-component-after';
    }
}
