<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

class SchemaNormalizer
{
    /**
     * @param array<string, mixed> $specification
     * @param SpecVersion $version
     * @return array<string, mixed>
     */
    public function normalize(array $specification, SpecVersion $version): array
    {
        return $this->normalizeValue($specification, $version);
    }

    protected function normalizeValue(mixed $value, SpecVersion $version): mixed
    {
        if (!\is_array($value)) {
            return $value;
        }

        if ($this->isList($value)) {
            return \array_map(fn(mixed $item): mixed => $this->normalizeValue($item, $version), $value);
        }

        foreach ($value as $key => $nestedValue) {
            $value[$key] = $this->normalizeValue($nestedValue, $version);
        }

        if (!$this->isSchemaLike($value)) {
            return $value;
        }

        return $version->isOpenApi31()
            ? $this->normalizeOpenApi31Schema($value)
            : $this->normalizeOpenApi30Schema($value);
    }

    /**
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    protected function normalizeOpenApi30Schema(array $schema): array
    {
        if (\array_key_exists('type', $schema) && \is_array($schema['type'])) {
            $types = \array_values(\array_filter(
                $schema['type'],
                static fn(mixed $type): bool => $type !== 'null',
            ));

            if (\count($types) !== \count($schema['type'])) {
                $schema['nullable'] = true;
            }

            if (\count($types) === 1) {
                $schema['type'] = $types[0];
            } elseif (\count($types) > 1) {
                $schema['type'] = $types;
            } else {
                unset($schema['type']);
            }
        }

        if (\array_key_exists('const', $schema)) {
            $schema['enum'] = [$schema['const']];
            unset($schema['const']);
        }

        if ($this->hasRefSiblings($schema)) {
            $ref = $schema['$ref'];
            unset($schema['$ref']);

            return \array_merge(['allOf' => [['$ref' => $ref]]], $schema);
        }

        return $schema;
    }

    /**
     * @param array<string, mixed> $schema
     * @return array<string, mixed>
     */
    protected function normalizeOpenApi31Schema(array $schema): array
    {
        if (($schema['nullable'] ?? false) === true && \array_key_exists('$ref', $schema)) {
            $ref = $schema['$ref'];
            unset($schema['$ref'], $schema['nullable']);

            return \array_merge([
                'anyOf' => [
                    ['$ref' => $ref],
                    ['type' => 'null'],
                ],
            ], $schema);
        }

        if (($schema['nullable'] ?? false) === true) {
            $types = $schema['type'] ?? null;

            if (\is_string($types)) {
                $types = [$types];
            }

            if (\is_array($types)) {
                $types[] = 'null';
                $schema['type'] = \array_values(\array_unique($types));
            }

            unset($schema['nullable']);
        }

        return $schema;
    }

    /**
     * @param array<mixed> $value
     */
    protected function isSchemaLike(array $value): bool
    {
        if (\array_key_exists('type', $value) && $this->isSchemaType($value['type'])) {
            return true;
        }

        if ($this->isPropertiesMap($value)) {
            return false;
        }

        return \array_any(
            array: [
                'additionalProperties',
                'allOf',
                'anyOf',
                'const',
                'enum',
                'items',
                'nullable',
                'oneOf',
                'properties',
                'schema',
                'type',
                '$ref',
            ],
            callback: static fn($key) => \array_key_exists($key, $value),
        );
    }

    /**
     * @param array<mixed> $value
     */
    protected function isList(array $value): bool
    {
        return \array_is_list($value);
    }

    /**
     * @param array<mixed> $value
     */
    protected function isPropertiesMap(array $value): bool
    {
        if ($value === [] || \array_is_list($value)) {
            return false;
        }

        if (\array_all($value, fn($property) => \is_array($property) && $this->isSchemaLike($property))) {
            return true;
        }

        return !\array_any(
            array: [
                'additionalProperties',
                'allOf',
                'anyOf',
                'const',
                'description',
                'enum',
                'format',
                'items',
                'nullable',
                'oneOf',
                'properties',
                'schema',
                'title',
                '$ref',
            ],
            callback: static fn(string $schemaKeyword): bool => \array_key_exists($schemaKeyword, $value),
        );
    }

    protected function isSchemaType(mixed $value): bool
    {
        if (\is_string($value)) {
            return true;
        }

        return \is_array($value)
            && \array_is_list($value)
            && \array_all($value, static fn(mixed $type): bool => \is_string($type));
    }

    /**
     * @param array<mixed> $schema
     */
    protected function hasRefSiblings(array $schema): bool
    {
        return \array_key_exists('$ref', $schema) && \count($schema) > 1;
    }
}
