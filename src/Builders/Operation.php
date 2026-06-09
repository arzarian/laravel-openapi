<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Delete;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Head;
use OpenApi\Annotations\Options;
use OpenApi\Annotations\Patch;
use OpenApi\Annotations\Post;
use OpenApi\Annotations\Put;
use OpenApi\Annotations\Trace;

/**
 * @property-read ?string $method
 * @property-read ?string $summary
 * @property-read ?string $description
 * @property-read list<string> $tags
 * @property-read mixed $externalDocs
 * @property-read ?string $operationId
 * @property-read ?bool $deprecated
 * @property-read list<mixed> $parameters
 * @property-read mixed $requestBody
 * @property-read list<mixed> $responses
 * @property-read list<mixed> $callbacks
 * @property-read list<array<string, mixed>> $security
 * @property-read list<mixed> $servers
 */
class Operation extends SpecificationBuilder
{
    protected ?string $method = null;

    public static function get(?string $objectId = null): static
    {
        return static::create($objectId)->method('get');
    }

    public static function post(?string $objectId = null): static
    {
        return static::create($objectId)->method('post');
    }

    public static function put(?string $objectId = null): static
    {
        return static::create($objectId)->method('put');
    }

    public static function patch(?string $objectId = null): static
    {
        return static::create($objectId)->method('patch');
    }

    public static function delete(?string $objectId = null): static
    {
        return static::create($objectId)->method('delete');
    }

    public static function options(?string $objectId = null): static
    {
        return static::create($objectId)->method('options');
    }

    public static function head(?string $objectId = null): static
    {
        return static::create($objectId)->method('head');
    }

    public static function trace(?string $objectId = null): static
    {
        return static::create($objectId)->method('trace');
    }

    public function method(?string $method): static
    {
        $instance = clone $this;
        $instance->method = $method;

        return $instance;
    }

    public function summary(?string $summary): static
    {
        return $this->set('summary', $summary);
    }

    public function action(?string $method): static
    {
        return $this->method($method);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function tags(string ...$tags): static
    {
        return $this->set('tags', $tags ?: null);
    }

    public function externalDocs(mixed $externalDocs): static
    {
        return $this->set('externalDocs', $externalDocs);
    }

    public function operationId(?string $operationId): static
    {
        return $this->set('operationId', $operationId);
    }

    public function deprecated(?bool $deprecated = true): static
    {
        return $this->set('deprecated', $deprecated);
    }

    public function parameters(mixed ...$parameters): static
    {
        return $this->set('parameters', $parameters ?: null);
    }

    public function requestBody(mixed $requestBody): static
    {
        return $this->set('requestBody', $requestBody);
    }

    public function responses(mixed ...$responses): static
    {
        return $this->set('responses', $responses ?: null);
    }

    public function callbacks(mixed ...$callbacks): static
    {
        return $this->set('callbacks', $callbacks ?: null);
    }

    public function security(array ...$security): static
    {
        return $this->set('security', $security ?: null);
    }

    public function noSecurity(): static
    {
        return $this->set('security', [[]]);
    }

    public function servers(mixed ...$servers): static
    {
        return $this->set('servers', $servers ?: null);
    }

    public function toAnnotation(): AbstractAnnotation
    {
        $class = match ($this->method) {
            'post' => Post::class,
            'put' => Put::class,
            'patch' => Patch::class,
            'delete' => Delete::class,
            'options' => Options::class,
            'head' => Head::class,
            'trace' => Trace::class,
            default => Get::class,
        };

        return new $class($this->toArray());
    }

    public function __get(string $name): mixed
    {
        if ($name === 'method') {
            return $this->method;
        }

        return parent::__get($name);
    }

    protected function build(): array
    {
        return array_merge(parent::build(), [
            'responses' => $this->keyedBy('responses', 'response') ?: null,
            'callbacks' => $this->keyedBy('callbacks', 'name') ?: null,
        ]);
    }

    protected function annotationClass(): string
    {
        return Get::class;
    }
}
