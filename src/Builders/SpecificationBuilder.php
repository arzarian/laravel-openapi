<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Generator;

/**
 * @property-read ?string $objectId
 * @property-read ?string $ref
 * @property-read array<string, mixed> $x
 */
abstract class SpecificationBuilder implements \JsonSerializable
{
    protected ?string $ref = null;

    /** @var array<string, mixed> */
    protected array $properties = [];

    /** @var array<string, mixed> */
    protected array $extensions = [];

    abstract protected function annotationClass(): string;

    final public function __construct(protected ?string $objectId = null)
    {
    }

    public function __get(string $name): mixed
    {
        return match (true) {
            $name === 'objectId' => $this->objectId,
            $name === 'ref' => $this->ref,
            $name === 'x' => $this->extensions,
            \str_starts_with($name, 'x-') => $this->extensions[\substr($name, 2)] ?? null,
            $name === $this->identifierField() => $this->identifierValue(),
            default => $this->properties[$name] ?? null,
        };
    }

    public function __isset(string $name): bool
    {
        return $this->__get($name) !== null;
    }

    public static function create(?string $objectId = null): static
    {
        return new static($objectId);
    }

    public static function ref(string $ref, ?string $objectId = null): static
    {
        return static::create($objectId)->withRef($ref);
    }

    public function objectId(?string $objectId): static
    {
        return $this->withObjectId($objectId);
    }

    public function withObjectId(?string $objectId): static
    {
        $instance = clone $this;
        $instance->objectId = $objectId;

        return $instance;
    }

    public function withRef(?string $ref): static
    {
        $instance = clone $this;
        $instance->ref = $ref;

        return $instance;
    }

    public function x(string $key, mixed $value = true): static
    {
        $instance = clone $this;

        if (\str_starts_with($key, 'x-')) {
            $key = \substr($key, 2);
        }

        $instance->extensions[$key] = $value;

        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        if ($this->ref !== null) {
            return ['$ref' => $this->ref];
        }

        return $this->filter($this->build());
    }

    public function toAnnotation(): AbstractAnnotation
    {
        $class = $this->annotationClass();

        $annotation = new $class($this->toArray());

        if (!$annotation instanceof AbstractAnnotation) {
            throw new \UnexpectedValueException('Specification builder annotation class must extend AbstractAnnotation.');
        }

        return $annotation;
    }

    public function toJson(int $options = 0): string
    {
        return \json_encode($this->toArray(), $options) ?: '';
    }

    public function jsonSerialize(): \stdClass
    {
        return (object)$this->toArray();
    }

    protected function set(string $key, mixed $value): static
    {
        $instance = clone $this;
        $instance->properties[$key] = $value;

        return $instance;
    }

    /**
     * @return array<string, mixed>
     */
    protected function build(): array
    {
        return \array_merge(
            $this->identifierProperties(),
            $this->properties,
            ['x' => $this->extensions ?: null],
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function identifierProperties(): array
    {
        $field = $this->identifierField();

        return $field === null ? [] : [$field => $this->objectId];
    }

    protected function identifierValue(): mixed
    {
        return $this->objectId;
    }

    protected function identifierField(): ?string
    {
        return null;
    }

    /**
     * @return array<string, mixed>
     * @param string $property
     * @param string $keyField
     */
    protected function keyedBy(string $property, string $keyField): array
    {
        $mapped = [];

        foreach ($this->properties[$property] ?? [] as $item) {
            $key = $item->{$keyField} ?? null;

            if ($key !== null) {
                $value = $this->normalize($item, true);

                if (\is_array($value)) {
                    unset($value[$keyField]);
                }

                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }

    protected function normalize(mixed $value, bool $stripIdentifier = false): mixed
    {
        if ($value instanceof self) {
            $data = $value->toArray();

            if ($stripIdentifier && $value->identifierField() !== null) {
                unset($data[$value->identifierField()]);
            }

            return $data;
        }

        if ($value instanceof AbstractAnnotation) {
            return \json_decode($value->toJson(), true);
        }

        if ($value instanceof \JsonSerializable) {
            $json = \json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

            return $json === false ? null : \json_decode($json, true);
        }

        if (\is_array($value)) {
            $mapped = [];

            foreach ($value as $itemKey => $item) {
                $mapped[$itemKey] = $this->normalize($item, $stripIdentifier);
            }

            return $mapped;
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $properties
     * @return array<string, mixed>
     */
    protected function filter(array $properties): array
    {
        $filtered = [];

        foreach ($properties as $key => $value) {
            $value = $this->normalize($value, true);

            if ($value === null || $value === [] || Generator::isDefault($value)) {
                continue;
            }

            $filtered[$key] = $value;
        }

        return $filtered;
    }
}
