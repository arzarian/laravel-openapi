<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody as RequestBodyBuilder;

abstract class RequestBodyFactory
{
    use Referencable;

    /**
     * @return RequestBodyBuilder|\OpenApi\Annotations\RequestBody|array
     */
    abstract public function build();
}
