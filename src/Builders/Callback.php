<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Attachable;

/**
 * @property-read ?string $name
 * @property-read array<string, PathItem> $callbacks
 */
class Callback extends SpecificationBuilder
{
    public function name(?string $name): static
    {
        return $this->withObjectId($name);
    }

    public function expression(string $expression, PathItem $pathItem): static
    {
        $callbacks = $this->properties['callbacks'] ?? [];
        $callbacks[$expression] = $pathItem;

        return $this->set('callbacks', $callbacks);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
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
