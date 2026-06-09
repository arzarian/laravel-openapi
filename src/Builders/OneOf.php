<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

class OneOf extends SchemaComposition
{
    protected static function compositionKey(): string
    {
        return 'oneOf';
    }
}
