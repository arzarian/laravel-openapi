<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Attachable;

/**
 * @property-read ?string $securityScheme
 * @property-read list<string> $scopes
 */
class SecurityRequirement extends SpecificationBuilder
{
    public function securityScheme(?string $securityScheme): static
    {
        return $this->withObjectId($securityScheme);
    }

    public function scopes(string ...$scopes): static
    {
        return $this->set('scopes', $scopes);
    }

    public function toArray(): array
    {
        if ($this->objectId === null) {
            return [];
        }

        return [$this->objectId => $this->properties['scopes'] ?? []];
    }

    protected function annotationClass(): string
    {
        return Attachable::class;
    }
}
