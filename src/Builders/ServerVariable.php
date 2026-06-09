<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\ServerVariable as SwaggerServerVariable;

class ServerVariable extends SpecificationBuilder
{
    public function enum(mixed ...$enum): static
    {
        return $this->set('enum', $enum ?: null);
    }

    public function default(mixed $default): static
    {
        return $this->set('default', $default);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    protected function identifierField(): ?string
    {
        return 'serverVariable';
    }

    protected function annotationClass(): string
    {
        return SwaggerServerVariable::class;
    }
}
