<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Schema as SchemaBuilder;

abstract class SchemaFactory
{
    use Referencable;

    /**
     * @return SchemaBuilder|\OpenApi\Annotations\Schema|array
     */
    abstract public function build();
}
