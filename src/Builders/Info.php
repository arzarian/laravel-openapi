<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Info as SwaggerInfo;

/**
 * @property-read ?string $title
 * @property-read ?string $description
 * @property-read ?string $termsOfService
 * @property-read ?Contact $contact
 * @property-read ?License $license
 * @property-read ?string $version
 */
class Info extends SpecificationBuilder
{
    public function title(?string $title): static
    {
        return $this->set('title', $title);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function termsOfService(?string $termsOfService): static
    {
        return $this->set('termsOfService', $termsOfService);
    }

    public function contact(Contact $contact): static
    {
        return $this->set('contact', $contact);
    }

    public function license(License $license): static
    {
        return $this->set('license', $license);
    }

    public function version(?string $version): static
    {
        return $this->set('version', $version);
    }

    protected function annotationClass(): string
    {
        return SwaggerInfo::class;
    }
}
