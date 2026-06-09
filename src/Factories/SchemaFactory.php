<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Schema as SchemaBuilder;

abstract class SchemaFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    /**
     * @return SchemaBuilder|\OpenApi\Annotations\Schema|array<string, mixed>
     */
    abstract public function build();

    public static function ref(?string $objectId = null): SchemaBuilder
    {
        $ref = static::makeRef($objectId);

        if (!$ref instanceof SchemaBuilder) {
            throw new \UnexpectedValueException('Schema factory refs must resolve to a schema builder.');
        }

        return $ref;
    }
}
