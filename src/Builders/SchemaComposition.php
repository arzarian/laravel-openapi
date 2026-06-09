<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

abstract class SchemaComposition extends Schema
{
    abstract protected static function compositionKey(): string;
    public static function of(mixed ...$schemas): static
    {
        return static::create()->schemas(...$schemas);
    }

    public function schemas(mixed ...$schemas): static
    {
        return $this->set(static::compositionKey(), $schemas ?: null);
    }
}
