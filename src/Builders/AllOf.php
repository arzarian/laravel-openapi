<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

class AllOf extends SchemaComposition
{
    protected static function compositionKey(): string
    {
        return 'allOf';
    }
}
