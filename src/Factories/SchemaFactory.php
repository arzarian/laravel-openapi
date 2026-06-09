<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Schema as SchemaBuilder;

abstract class SchemaFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    public static function ref(?string $objectId = null): SchemaBuilder
    {
        return static::makeRef($objectId);
    }

    /**
     * @return SchemaBuilder|\OpenApi\Annotations\Schema|array
     */
    abstract public function build();
}
