<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi;

use OpenApi\Annotations\Schema;

class SchemaHelpers
{
    public static function guessFromReflectionType(\ReflectionType $reflectionType): Schema
    {
        if (!$reflectionType instanceof \ReflectionNamedType) {
            return new Schema(['type' => 'string']);
        }

        return match ($reflectionType->getName()) {
            'int' => new Schema(['type' => 'integer']),
            'bool' => new Schema(['type' => 'boolean']),
            default => new Schema(['type' => 'string']),
        };
    }
}
