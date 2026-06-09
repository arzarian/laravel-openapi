<?php

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

class SchemaNormalizer
{
    public function normalize(array $specification, SpecVersion $version): array
    {
        return $this->normalizeValue($specification, $version);
    }

    protected function normalizeValue(mixed $value, SpecVersion $version): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if ($this->isList($value)) {
            return array_map(fn (mixed $item): mixed => $this->normalizeValue($item, $version), $value);
        }

        foreach ($value as $key => $nestedValue) {
            $value[$key] = $this->normalizeValue($nestedValue, $version);
        }

        if (! $this->isSchemaLike($value)) {
            return $value;
        }

        return $version->isOpenApi31()
            ? $this->normalizeOpenApi31Schema($value)
            : $this->normalizeOpenApi30Schema($value);
    }

    protected function normalizeOpenApi30Schema(array $schema): array
    {
        if (array_key_exists('type', $schema) && is_array($schema['type'])) {
            $types = array_values(array_filter(
                $schema['type'],
                static fn (mixed $type): bool => $type !== 'null'
            ));

            if (count($types) !== count($schema['type'])) {
                $schema['nullable'] = true;
            }

            if (count($types) === 1) {
                $schema['type'] = $types[0];
            } elseif (count($types) > 1) {
                $schema['type'] = $types;
            } else {
                unset($schema['type']);
            }
        }

        if (array_key_exists('const', $schema)) {
            $schema['enum'] = [$schema['const']];
            unset($schema['const']);
        }

        if ($this->hasRefSiblings($schema)) {
            $ref = $schema['$ref'];
            unset($schema['$ref']);

            return array_merge(['allOf' => [['$ref' => $ref]]], $schema);
        }

        return $schema;
    }

    protected function normalizeOpenApi31Schema(array $schema): array
    {
        if (($schema['nullable'] ?? false) === true && array_key_exists('$ref', $schema)) {
            $ref = $schema['$ref'];
            unset($schema['$ref'], $schema['nullable']);

            return array_merge([
                'anyOf' => [
                    ['$ref' => $ref],
                    ['type' => 'null'],
                ],
            ], $schema);
        }

        if (($schema['nullable'] ?? false) === true) {
            $types = $schema['type'] ?? null;

            if (is_string($types)) {
                $types = [$types];
            }

            if (is_array($types)) {
                $types[] = 'null';
                $schema['type'] = array_values(array_unique($types));
            }

            unset($schema['nullable']);
        }

        return $schema;
    }

    protected function isSchemaLike(array $value): bool
    {
        if ($this->isPropertiesMap($value)) {
            return false;
        }

        foreach ([
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
        ] as $key) {
            if (array_key_exists($key, $value)) {
                return true;
            }
        }

        return false;
    }

    protected function isList(array $value): bool
    {
        return array_is_list($value);
    }

    protected function isPropertiesMap(array $value): bool
    {
        if ($value === [] || array_is_list($value)) {
            return false;
        }

        foreach ([
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
        ] as $schemaKeyword) {
            if (array_key_exists($schemaKeyword, $value)) {
                return false;
            }
        }

        foreach ($value as $property) {
            if (! is_array($property) || ! $this->isSchemaLike($property)) {
                return false;
            }
        }

        return true;
    }

    protected function hasRefSiblings(array $schema): bool
    {
        return array_key_exists('$ref', $schema) && count($schema) > 1;
    }
}
