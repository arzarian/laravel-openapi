<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Tag as SwaggerTag;

/**
 * @property-read ?string $name
 * @property-read ?string $description
 * @property-read mixed $externalDocs
 */
class Tag extends SpecificationBuilder
{
    public function name(?string $name): static
    {
        return $this->set('name', $name);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function externalDocs(mixed $externalDocs): static
    {
        return $this->set('externalDocs', $externalDocs);
    }

    protected function annotationClass(): string
    {
        return SwaggerTag::class;
    }
}
