<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\ExternalDocumentation;

/**
 * @property-read ?string $description
 * @property-read ?string $url
 */
class ExternalDocs extends SpecificationBuilder
{
    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function url(?string $url): static
    {
        return $this->set('url', $url);
    }

    protected function annotationClass(): string
    {
        return ExternalDocumentation::class;
    }
}
