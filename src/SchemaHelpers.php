<?php

namespace Vyuldashev\LaravelOpenApi;

use OpenApi\Annotations\Schema;
use ReflectionType;

class SchemaHelpers
{
    public static function guessFromReflectionType(ReflectionType $reflectionType): Schema
    {
        switch ($reflectionType->getName()) {
            case 'int':
                return new Schema(['type' => 'integer']);
            case 'bool':
                return new Schema(['type' => 'boolean']);
        }

        return new Schema(['type' => 'string']);
    }
}
