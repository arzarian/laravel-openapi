<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use OpenApi\Annotations\Schema;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;

abstract class SchemaFactory
{
    use Referencable;

    abstract public function build(): Schema;
}
