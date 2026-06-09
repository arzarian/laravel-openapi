<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\License as SwaggerLicense;

/**
 * @property-read ?string $name
 * @property-read ?string $url
 * @property-read ?string $identifier
 */
class License extends SpecificationBuilder
{
    public function name(?string $name): static
    {
        return $this->set('name', $name);
    }

    public function url(?string $url): static
    {
        return $this->set('url', $url);
    }

    public function identifier(?string $identifier): static
    {
        return $this->set('identifier', $identifier);
    }

    protected function annotationClass(): string
    {
        return SwaggerLicense::class;
    }
}
