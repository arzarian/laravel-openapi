<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

class AnyOf extends SchemaComposition
{
    protected static function compositionKey(): string
    {
        return 'anyOf';
    }
}
