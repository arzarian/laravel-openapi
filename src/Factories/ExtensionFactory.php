<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

abstract class ExtensionFactory
{
    abstract public function key(): string;

    /**
     * @return string|array<string, mixed>|\JsonSerializable|\OpenApi\Annotations\AbstractAnnotation|null
     */
    abstract public function value();
}
