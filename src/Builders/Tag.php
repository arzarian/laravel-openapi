<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Tag as SwaggerTag;

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
