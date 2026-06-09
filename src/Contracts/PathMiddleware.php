<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Contracts;

use OpenApi\Annotations\PathItem;
use Vyuldashev\LaravelOpenApi\RouteInformation;

interface PathMiddleware
{
    public function before(RouteInformation $routeInformation): void;

    public function after(PathItem $pathItem): PathItem;
}
