<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use OpenApi\Annotations\Response;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;

abstract class ResponseFactory
{
    use Referencable;

    abstract public function build(): Response;
}
