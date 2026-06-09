<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Encoding as SwaggerEncoding;

/**
 * @property-read ?string $property
 * @property-read ?string $contentType
 * @property-read list<Header> $headers
 * @property-read ?string $style
 * @property-read ?bool $explode
 * @property-read ?bool $allowReserved
 */
class Encoding extends SpecificationBuilder
{
    public function property(?string $property): static
    {
        return $this->withObjectId($property);
    }

    public function contentType(?string $contentType): static
    {
        return $this->set('contentType', $contentType);
    }

    public function headers(Header ...$headers): static
    {
        return $this->set('headers', $headers ?: null);
    }

    public function style(?string $style): static
    {
        return $this->set('style', $style);
    }

    public function explode(?bool $explode = true): static
    {
        return $this->set('explode', $explode);
    }

    public function allowReserved(?bool $allowReserved = true): static
    {
        return $this->set('allowReserved', $allowReserved);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function build(): array
    {
        return \array_merge(parent::build(), [
            'headers' => $this->keyedBy('headers', 'header') ?: null,
        ]);
    }

    protected function identifierField(): ?string
    {
        return 'property';
    }

    protected function annotationClass(): string
    {
        return SwaggerEncoding::class;
    }
}
