<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Xml as SwaggerXml;

class Xml extends SpecificationBuilder
{
    public function name(?string $name): static
    {
        return $this->set('name', $name);
    }

    public function namespace(?string $namespace): static
    {
        return $this->set('namespace', $namespace);
    }

    public function prefix(?string $prefix): static
    {
        return $this->set('prefix', $prefix);
    }

    public function attribute(?bool $attribute = true): static
    {
        return $this->set('attribute', $attribute);
    }

    public function wrapped(?bool $wrapped = true): static
    {
        return $this->set('wrapped', $wrapped);
    }

    protected function annotationClass(): string
    {
        return SwaggerXml::class;
    }
}
