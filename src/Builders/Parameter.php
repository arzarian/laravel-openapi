<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Parameter as SwaggerParameter;

/**
 * @property-read ?string $name
 * @property-read ?string $parameter
 * @property-read ?string $in
 * @property-read ?string $description
 * @property-read ?bool $required
 * @property-read ?bool $deprecated
 * @property-read ?bool $allowEmptyValue
 * @property-read ?string $style
 * @property-read ?bool $explode
 * @property-read ?bool $allowReserved
 * @property-read mixed $schema
 * @property-read mixed $example
 * @property-read list<mixed> $examples
 * @property-read list<mixed> $content
 */
class Parameter extends SpecificationBuilder
{
    public const IN_QUERY = 'query';
    public const IN_HEADER = 'header';
    public const IN_PATH = 'path';
    public const IN_COOKIE = 'cookie';

    public const STYLE_MATRIX = 'matrix';
    public const STYLE_LABEL = 'label';
    public const STYLE_FORM = 'form';
    public const STYLE_SIMPLE = 'simple';
    public const STYLE_SPACE_DELIMITED = 'spaceDelimited';
    public const STYLE_PIPE_DELIMITED = 'pipeDelimited';
    public const STYLE_DEEP_OBJECT = 'deepObject';

    public static function query(?string $objectId = null): static
    {
        return static::create($objectId)->in(static::IN_QUERY);
    }

    public static function header(?string $objectId = null): static
    {
        return static::create($objectId)->in(static::IN_HEADER);
    }

    public static function path(?string $objectId = null): static
    {
        return static::create($objectId)->in(static::IN_PATH);
    }

    public static function cookie(?string $objectId = null): static
    {
        return static::create($objectId)->in(static::IN_COOKIE);
    }

    public function name(?string $name): static
    {
        return $this->set('name', $name);
    }

    public function in(?string $in): static
    {
        return $this->set('in', $in);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function required(?bool $required = true): static
    {
        return $this->set('required', $required);
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

    public function schema(mixed $schema): static
    {
        return $this->set('schema', $schema);
    }

    public function example(mixed $example): static
    {
        return $this->set('example', $example);
    }

    public function examples(mixed ...$examples): static
    {
        return $this->set('examples', $examples ?: null);
    }

    public function content(mixed ...$content): static
    {
        return $this->set('content', $content ?: null);
    }

    protected function build(): array
    {
        return array_merge(parent::build(), [
            'content' => $this->keyedBy('content', 'mediaType') ?: null,
            'examples' => $this->keyedBy('examples', 'example') ?: null,
        ]);
    }

    protected function identifierField(): ?string
    {
        return 'parameter';
    }

    protected function annotationClass(): string
    {
        return SwaggerParameter::class;
    }
}
