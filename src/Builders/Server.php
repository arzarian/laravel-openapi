<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Server as SwaggerServer;

/**
 * @property-read ?string $url
 * @property-read ?string $description
 * @property-read list<mixed> $variables
 */
class Server extends SpecificationBuilder
{
    public function url(?string $url): static
    {
        return $this->set('url', $url);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function variables(mixed ...$variables): static
    {
        return $this->set('variables', $variables ?: null);
    }

    protected function build(): array
    {
        return array_merge(parent::build(), [
            'variables' => $this->keyedBy('variables', 'serverVariable') ?: null,
        ]);
    }

    protected function annotationClass(): string
    {
        return SwaggerServer::class;
    }
}
