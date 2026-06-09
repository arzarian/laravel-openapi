<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Response as ResponseBuilder;

abstract class ResponseFactory
{
    use Referencable;

    /**
     * @return ResponseBuilder|\OpenApi\Annotations\Response|array
     */
    abstract public function build();
}
