<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

abstract class ExtensionFactory
{
    abstract public function key(): string;

    /**
     * @return string|null|array|\JsonSerializable|\OpenApi\Annotations\AbstractAnnotation
     */
    abstract public function value();
}
