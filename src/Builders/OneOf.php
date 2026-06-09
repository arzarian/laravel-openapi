<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

class OneOf extends SchemaComposition
{
    protected static function compositionKey(): string
    {
        return 'oneOf';
    }
}
