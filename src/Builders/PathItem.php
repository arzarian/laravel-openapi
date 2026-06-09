<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\PathItem as SwaggerPathItem;

class PathItem extends SpecificationBuilder
{
    public function get(mixed $operation): static
    {
        return $this->set('get', $operation);
    }

    public function post(mixed $operation): static
    {
        return $this->set('post', $operation);
    }

    public function put(mixed $operation): static
    {
        return $this->set('put', $operation);
    }

    public function patch(mixed $operation): static
    {
        return $this->set('patch', $operation);
    }

    public function delete(mixed $operation): static
    {
        return $this->set('delete', $operation);
    }

    public function options(mixed $operation): static
    {
        return $this->set('options', $operation);
    }

    public function head(mixed $operation): static
    {
        return $this->set('head', $operation);
    }

    public function trace(mixed $operation): static
    {
        return $this->set('trace', $operation);
    }

    public function route(?string $route): static
    {
        return $this->set('path', $route);
    }

    public function summary(?string $summary): static
    {
        return $this->set('summary', $summary);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function operations(mixed ...$operations): static
    {
        return $this->set('operations', $operations ?: null);
    }

    public function servers(mixed ...$servers): static
    {
        return $this->set('servers', $servers ?: null);
    }

    public function parameters(mixed ...$parameters): static
    {
        return $this->set('parameters', $parameters ?: null);
    }

    protected function annotationClass(): string
    {
        return SwaggerPathItem::class;
    }
}
