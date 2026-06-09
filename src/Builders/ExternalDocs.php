<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\ExternalDocumentation;

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
