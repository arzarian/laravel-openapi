<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

abstract class SchemaComposition extends Schema
{
    public static function of(mixed ...$schemas): static
    {
        return static::create()->schemas(...$schemas);
    }

    public function schemas(mixed ...$schemas): static
    {
        return $this->set(static::compositionKey(), $schemas ?: null);
    }

    abstract protected static function compositionKey(): string;
}
