<?php

namespace Vyuldashev\LaravelOpenApi\Contracts;

use OpenApi\Annotations\Components;

interface ComponentMiddleware
{
    public function after(Components $components): void;
}
