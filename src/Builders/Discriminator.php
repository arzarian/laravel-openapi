<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Discriminator as SwaggerDiscriminator;

/**
 * @property-read ?string $propertyName
 * @property-read array<string, string> $mapping
 */
class Discriminator extends SpecificationBuilder
{
    public function propertyName(?string $propertyName): static
    {
        return $this->set('propertyName', $propertyName);
    }

    /**
     * @param array<string, string> $mapping
     */
    public function mapping(array $mapping): static
    {
        return $this->set('mapping', $mapping);
    }

    protected function annotationClass(): string
    {
        return SwaggerDiscriminator::class;
    }
}
