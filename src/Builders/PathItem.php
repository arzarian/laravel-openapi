<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\PathItem as SwaggerPathItem;

/**
 * @property-read ?Operation $get
 * @property-read ?Operation $post
 * @property-read ?Operation $put
 * @property-read ?Operation $patch
 * @property-read ?Operation $delete
 * @property-read ?Operation $options
 * @property-read ?Operation $head
 * @property-read ?Operation $trace
 * @property-read ?string $path
 * @property-read ?string $summary
 * @property-read ?string $description
 * @property-read list<Operation> $operations
 * @property-read list<Server> $servers
 * @property-read list<Parameter> $parameters
 */
class PathItem extends SpecificationBuilder
{
    public function get(Operation $operation): static
    {
        return $this->set('get', $operation);
    }

    public function post(Operation $operation): static
    {
        return $this->set('post', $operation);
    }

    public function put(Operation $operation): static
    {
        return $this->set('put', $operation);
    }

    public function patch(Operation $operation): static
    {
        return $this->set('patch', $operation);
    }

    public function delete(Operation $operation): static
    {
        return $this->set('delete', $operation);
    }

    public function options(Operation $operation): static
    {
        return $this->set('options', $operation);
    }

    public function head(Operation $operation): static
    {
        return $this->set('head', $operation);
    }

    public function trace(Operation $operation): static
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

    public function operations(Operation ...$operations): static
    {
        return $this->set('operations', $operations ?: null);
    }

    public function servers(Server ...$servers): static
    {
        return $this->set('servers', $servers ?: null);
    }

    public function parameters(Parameter ...$parameters): static
    {
        return $this->set('parameters', $parameters ?: null);
    }

    protected function annotationClass(): string
    {
        return SwaggerPathItem::class;
    }
}
