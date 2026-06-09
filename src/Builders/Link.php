<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Link as SwaggerLink;

class Link extends SpecificationBuilder
{
    public function operationRef(?string $operationRef): static
    {
        return $this->set('operationRef', $operationRef);
    }

    public function operationId(?string $operationId): static
    {
        return $this->set('operationId', $operationId);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function parameters(array $parameters): static
    {
        return $this->set('parameters', $parameters);
    }

    public function requestBody(mixed $requestBody): static
    {
        return $this->set('requestBody', $requestBody);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function server(mixed $server): static
    {
        return $this->set('server', $server);
    }

    protected function identifierField(): ?string
    {
        return 'link';
    }

    protected function annotationClass(): string
    {
        return SwaggerLink::class;
    }
}
