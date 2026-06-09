<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\MediaType as SwaggerMediaType;

/**
 * @property-read ?string $mediaType
 * @property-read mixed $schema
 * @property-read mixed $example
 * @property-read list<mixed> $examples
 * @property-read list<mixed> $encoding
 */
class MediaType extends SpecificationBuilder
{
    public const MEDIA_TYPE_APPLICATION_JSON = 'application/json';
    public const MEDIA_TYPE_APPLICATION_PDF = 'application/pdf';
    public const MEDIA_TYPE_IMAGE_JPEG = 'image/jpeg';
    public const MEDIA_TYPE_IMAGE_PNG = 'image/png';
    public const MEDIA_TYPE_TEXT_CALENDAR = 'text/calendar';
    public const MEDIA_TYPE_TEXT_PLAIN = 'text/plain';
    public const MEDIA_TYPE_TEXT_XML = 'text/xml';
    public const MEDIA_TYPE_APPLICATION_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    public static function json(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_APPLICATION_JSON);
    }

    public static function pdf(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_APPLICATION_PDF);
    }

    public static function jpeg(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_IMAGE_JPEG);
    }

    public static function png(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_IMAGE_PNG);
    }

    public static function calendar(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_TEXT_CALENDAR);
    }

    public static function plainText(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_TEXT_PLAIN);
    }

    public static function xml(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_TEXT_XML);
    }

    public static function formUrlEncoded(?string $objectId = null): static
    {
        return static::create($objectId)->mediaType(static::MEDIA_TYPE_APPLICATION_X_WWW_FORM_URLENCODED);
    }

    public function mediaType(?string $mediaType): static
    {
        return $this->set('mediaType', $mediaType);
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

    public function encoding(mixed ...$encoding): static
    {
        return $this->set('encoding', $encoding ?: null);
    }

    protected function build(): array
    {
        return array_merge(parent::build(), [
            'examples' => $this->keyedBy('examples', 'example') ?: null,
            'encoding' => $this->keyedBy('encoding', 'property') ?: null,
        ]);
    }

    protected function annotationClass(): string
    {
        return SwaggerMediaType::class;
    }
}
