<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Components as SwaggerComponents;

class Components extends SpecificationBuilder
{
    public function schemas(mixed ...$schemas): static
    {
        return $this->set('schemas', $schemas ?: null);
    }

    public function responses(mixed ...$responses): static
    {
        return $this->set('responses', $responses ?: null);
    }

    public function parameters(mixed ...$parameters): static
    {
        return $this->set('parameters', $parameters ?: null);
    }

    public function examples(mixed ...$examples): static
    {
        return $this->set('examples', $examples ?: null);
    }

    public function requestBodies(mixed ...$requestBodies): static
    {
        return $this->set('requestBodies', $requestBodies ?: null);
    }

    public function headers(mixed ...$headers): static
    {
        return $this->set('headers', $headers ?: null);
    }

    public function securitySchemes(mixed ...$securitySchemes): static
    {
        return $this->set('securitySchemes', $securitySchemes ?: null);
    }

    public function links(mixed ...$links): static
    {
        return $this->set('links', $links ?: null);
    }

    public function callbacks(mixed ...$callbacks): static
    {
        return $this->set('callbacks', $callbacks ?: null);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function build(): array
    {
        return \array_merge(parent::build(), [
            'schemas' => $this->keyedBy('schemas', 'schema') ?: null,
            'responses' => $this->keyedBy('responses', 'response') ?: null,
            'parameters' => $this->keyedBy('parameters', 'parameter') ?: null,
            'examples' => $this->keyedBy('examples', 'example') ?: null,
            'requestBodies' => $this->keyedBy('requestBodies', 'request') ?: null,
            'headers' => $this->keyedBy('headers', 'header') ?: null,
            'securitySchemes' => $this->keyedBy('securitySchemes', 'securityScheme') ?: null,
            'links' => $this->keyedBy('links', 'link') ?: null,
            'callbacks' => $this->keyedBy('callbacks', 'name') ?: null,
        ]);
    }

    protected function annotationClass(): string
    {
        return SwaggerComponents::class;
    }
}
