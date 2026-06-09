<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Contact as SwaggerContact;

/**
 * @property-read ?string $name
 * @property-read ?string $url
 * @property-read ?string $email
 */
class Contact extends SpecificationBuilder
{
    public function name(?string $name): static
    {
        return $this->set('name', $name);
    }

    public function url(?string $url): static
    {
        return $this->set('url', $url);
    }

    public function email(?string $email): static
    {
        return $this->set('email', $email);
    }

    protected function annotationClass(): string
    {
        return SwaggerContact::class;
    }
}
