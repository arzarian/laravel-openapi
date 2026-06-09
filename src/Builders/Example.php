<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Examples;

/**
 * @property-read ?string $example
 * @property-read ?string $summary
 * @property-read ?string $description
 * @property-read mixed $value
 * @property-read ?string $externalValue
 */
class Example extends SpecificationBuilder
{
    public function summary(?string $summary): static
    {
        return $this->set('summary', $summary);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function value(mixed $value): static
    {
        return $this->set('value', $value);
    }

    public function externalValue(?string $externalValue): static
    {
        return $this->set('externalValue', $externalValue);
    }

    protected function identifierField(): ?string
    {
        return 'example';
    }

    protected function annotationClass(): string
    {
        return Examples::class;
    }
}
