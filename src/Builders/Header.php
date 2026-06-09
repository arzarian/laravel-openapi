<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Header as SwaggerHeader;

/**
 * @property-read ?string $header
 * @property-read ?string $description
 * @property-read ?bool $required
 * @property-read ?Schema $schema
 * @property-read mixed $example
 * @property-read list<Example> $examples
 * @property-read list<MediaType> $content
 * @property-read ?bool $deprecated
 * @property-read ?bool $allowEmptyValue
 * @property-read ?string $style
 * @property-read ?bool $explode
 * @property-read ?bool $allowReserved
 */
class Header extends SpecificationBuilder
{
    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function required(?bool $required = true): static
    {
        return $this->set('required', $required);
    }

    public function schema(Schema $schema): static
    {
        return $this->set('schema', $schema);
    }

    public function example(mixed $example): static
    {
        return $this->set('example', $example);
    }

    public function examples(Example ...$examples): static
    {
        return $this->set('examples', $examples ?: null);
    }

    public function content(MediaType ...$content): static
    {
        return $this->set('content', $content ?: null);
    }

    public function deprecated(?bool $deprecated = true): static
    {
        return $this->set('deprecated', $deprecated);
    }

    public function allowEmptyValue(?bool $allowEmptyValue = true): static
    {
        return $this->set('allowEmptyValue', $allowEmptyValue);
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
            'examples' => $this->keyedBy('examples', 'example') ?: null,
            'content' => $this->keyedBy('content', 'mediaType') ?: null,
        ]);
    }

    protected function identifierField(): ?string
    {
        return 'header';
    }

    protected function annotationClass(): string
    {
        return SwaggerHeader::class;
    }
}
