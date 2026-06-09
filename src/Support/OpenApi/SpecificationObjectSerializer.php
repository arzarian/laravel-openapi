<?php

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use JsonSerializable;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Generator;
use Throwable;

class SpecificationObjectSerializer
{
    public function toArray(mixed $value, ?SpecVersion $version = null): mixed
    {
        if (is_array($value)) {
            return array_map(fn (mixed $item): mixed => $this->toArray($item, $version), $value);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        if ($value instanceof JsonSerializable) {
            $this->setVersion($value, $version);

            return json_decode(json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), true);
        }

        return $value;
    }

    public function properties(array $properties): array
    {
        return array_filter(
            $properties,
            static fn (mixed $value): bool => $value !== null && $value !== [] && ! Generator::isDefault($value)
        );
    }

    public function property(mixed $object, string $property): mixed
    {
        if (is_array($object)) {
            return $object[$property] ?? $object['objectId'] ?? null;
        }

        if (! is_object($object)) {
            return null;
        }

        try {
            $value = $object->{$property};

            return Generator::isDefault($value) ? null : $value;
        } catch (Throwable) {
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

        if (is_array($value)) {
            foreach ($value as $item) {
                $this->setVersion($item, $version);
            }

            return;
        }

        if (! is_object($value)) {
            return;
        }

        if ($value instanceof AbstractAnnotation) {
            $value->_context->root()->version = $version->value;
        }

        foreach (get_object_vars($value) as $property => $nestedValue) {
            if (str_starts_with($property, '_')) {
                continue;
            }

            $this->setVersion($nestedValue, $version);
        }
    }
}
