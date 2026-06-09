<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Generator;

class SpecificationObjectSerializer
{
    public function toArray(mixed $value, ?SpecVersion $version = null): mixed
    {
        if (\is_array($value)) {
            return \array_map(fn(mixed $item): mixed => $this->toArray($item, $version), $value);
        }

        if (\is_object($value) && \method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        if ($value instanceof \JsonSerializable) {
            $this->setVersion($value, $version);

            $json = \json_encode($value, \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE);

            return $json === false ? null : \json_decode($json, true);
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $properties
     * @return array<string, mixed>
     */
    public function properties(array $properties): array
    {
        return \array_filter(
            $properties,
            static fn(mixed $value): bool => $value !== null && $value !== [] && ! Generator::isDefault($value),
        );
    }

    public function property(mixed $object, string $property): mixed
    {
        if (\is_array($object)) {
            return $object[$property] ?? null;
        }

        if (! \is_object($object)) {
            return null;
        }

        try {
            $value = $object->{$property};

            return Generator::isDefault($value) ? null : $value;
        } catch (\Throwable) {
            return null;
        }
    }

    public function componentName(mixed $object, string $keyField): ?string
    {
        return $this->property($object, $keyField) ?? $this->property($object, 'objectId');
    }

    protected function setVersion(mixed $value, ?SpecVersion $version): void
    {
        if ($version === null) {
            return;
        }

        if (\is_array($value)) {
            foreach ($value as $item) {
                $this->setVersion($item, $version);
            }

            return;
        }

        if (! \is_object($value)) {
            return;
        }

        if ($value instanceof AbstractAnnotation) {
            $root = $value->_context?->root();

            if ($root !== null) {
                $root->version = $version->value;
            }
        }

        foreach (\get_object_vars($value) as $property => $nestedValue) {
            if (\str_starts_with((string)$property, '_')) {
                continue;
            }

            $this->setVersion($nestedValue, $version);
        }
    }
}
