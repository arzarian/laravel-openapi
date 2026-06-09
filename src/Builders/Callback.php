<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Attachable;

/**
 * @property-read ?string $name
 * @property-read array<string, mixed> $callbacks
 */
class Callback extends SpecificationBuilder
{
    public function name(?string $name): static
    {
        return $this->withObjectId($name);
    }

    public function expression(string $expression, mixed $pathItem): static
    {
        $callbacks = $this->properties['callbacks'] ?? [];
        $callbacks[$expression] = $pathItem;

        return $this->set('callbacks', $callbacks);
    }

    protected function build(): array
    {
        return $this->properties['callbacks'] ?? [];
    }

    protected function identifierField(): ?string
    {
        return 'name';
    }

    protected function annotationClass(): string
    {
        return Attachable::class;
    }
}
