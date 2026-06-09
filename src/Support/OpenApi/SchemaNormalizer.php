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
                static fn (string $type): bool => $type !== 'null'
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

        return $schema;
    }

    protected function normalizeOpenApi31Schema(array $schema): array
    {
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
}
