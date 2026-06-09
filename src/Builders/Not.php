<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

class Not extends Schema
{
    public static function schema(Schema $schema): static
    {
        return static::create()->set('not', $schema);
    }
}
