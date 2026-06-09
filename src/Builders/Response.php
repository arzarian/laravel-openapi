<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Response as SwaggerResponse;

class Response extends SpecificationBuilder
{
    protected const STATUSES = [
        'ok' => [200, 'OK'],
        'created' => [201, 'Created'],
        'movedPermanently' => [301, 'Moved Permanently'],
        'movedTemporarily' => [302, 'Moved Temporarily'],
        'badRequest' => [400, 'Bad Request'],
        'unauthorized' => [401, 'Unauthorized'],
        'forbidden' => [403, 'Forbidden'],
        'notFound' => [404, 'Not Found'],
        'unprocessableEntity' => [422, 'Unprocessable Entity'],
        'tooManyRequests' => [429, 'Too Many Requests'],
        'internalServerError' => [500, 'Internal Server Error'],
    ];

    public static function ok(?string $objectId = null): static
    {
        return static::fromStatus('ok', $objectId);
    }

    public static function created(?string $objectId = null): static
    {
        return static::fromStatus('created', $objectId);
    }

    public static function movedPermanently(?string $objectId = null): static
    {
        return static::fromStatus('movedPermanently', $objectId);
    }

    public static function movedTemporarily(?string $objectId = null): static
    {
        return static::fromStatus('movedTemporarily', $objectId);
    }

    public static function badRequest(?string $objectId = null): static
    {
        return static::fromStatus('badRequest', $objectId);
    }

    public static function unauthorized(?string $objectId = null): static
    {
        return static::fromStatus('unauthorized', $objectId);
    }

    public static function forbidden(?string $objectId = null): static
    {
        return static::fromStatus('forbidden', $objectId);
    }

    public static function notFound(?string $objectId = null): static
    {
        return static::fromStatus('notFound', $objectId);
    }

    public static function unprocessableEntity(?string $objectId = null): static
    {
        return static::fromStatus('unprocessableEntity', $objectId);
    }

    public static function tooManyRequests(?string $objectId = null): static
    {
        return static::fromStatus('tooManyRequests', $objectId);
    }

    public static function internalServerError(?string $objectId = null): static
    {
        return static::fromStatus('internalServerError', $objectId);
    }

    protected static function fromStatus(string $name, ?string $objectId = null): static
    {
        [$statusCode, $description] = static::STATUSES[$name];

        return static::create($objectId)
            ->statusCode($statusCode)
            ->description($description);
    }

    public function statusCode(?int $statusCode): static
    {
        return $this->set('statusCode', $statusCode);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function headers(mixed ...$headers): static
    {
        return $this->set('headers', $headers ?: null);
    }

    public function content(mixed ...$content): static
    {
        return $this->set('content', $content ?: null);
    }

    public function links(mixed ...$links): static
    {
        return $this->set('links', $links ?: null);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function build(): array
    {
        $properties = parent::build();
        unset($properties['statusCode']);

        return \array_merge($properties, [
            'content' => $this->keyedBy('content', 'mediaType') ?: null,
            'headers' => $this->keyedBy('headers', 'header') ?: null,
            'links' => $this->keyedBy('links', 'link') ?: null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function identifierProperties(): array
    {
        return [
            'response' => $this->identifierValue(),
        ];
    }

    #[\Override]
    protected function identifierValue(): mixed
    {
        return $this->properties['statusCode'] ?? $this->objectId;
    }

    protected function identifierField(): ?string
    {
        return 'response';
    }

    protected function annotationClass(): string
    {
        return SwaggerResponse::class;
    }
}
