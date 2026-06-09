<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Schema as SwaggerSchema;

class Schema extends SpecificationBuilder
{
    public const TYPE_ARRAY = 'array';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_NUMBER = 'number';
    public const TYPE_OBJECT = 'object';
    public const TYPE_STRING = 'string';

    public const FORMAT_INT32 = 'int32';
    public const FORMAT_INT64 = 'int64';
    public const FORMAT_FLOAT = 'float';
    public const FORMAT_DOUBLE = 'double';
    public const FORMAT_BYTE = 'byte';
    public const FORMAT_BINARY = 'binary';
    public const FORMAT_DATE = 'date';
    public const FORMAT_DATE_TIME = 'date-time';
    public const FORMAT_PASSWORD = 'password';
    public const FORMAT_UUID = 'uuid';

    public static function array(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_ARRAY);
    }

    public static function boolean(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_BOOLEAN);
    }

    public static function integer(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_INTEGER);
    }

    public static function number(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_NUMBER);
    }

    public static function object(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_OBJECT);
    }

    public static function string(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_STRING);
    }

    public function title(?string $title): static
    {
        return $this->set('title', $title);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function enum(mixed ...$enum): static
    {
        return $this->set('enum', $enum ?: null);
    }

    public function default(mixed $default): static
    {
        return $this->set('default', $default);
    }

    public function format(?string $format): static
    {
        return $this->set('format', $format);
    }

    public function type(string|array|null $type): static
    {
        return $this->set('type', $type);
    }

    public function types(string ...$types): static
    {
        return $this->type($types);
    }

    public function nullOr(string $type): static
    {
        return $this->type([$type, 'null']);
    }

    public function const(mixed $value): static
    {
        return $this->set('const', $value);
    }

    public function items(mixed $items): static
    {
        return $this->set('items', $items);
    }

    public function maxItems(?int $maxItems): static
    {
        return $this->set('maxItems', $maxItems);
    }

    public function minItems(?int $minItems): static
    {
        return $this->set('minItems', $minItems);
    }

    public function uniqueItems(?bool $uniqueItems = true): static
    {
        return $this->set('uniqueItems', $uniqueItems);
    }

    public function pattern(?string $pattern): static
    {
        return $this->set('pattern', $pattern);
    }

    public function maxLength(?int $maxLength): static
    {
        return $this->set('maxLength', $maxLength);
    }

    public function minLength(?int $minLength): static
    {
        return $this->set('minLength', $minLength);
    }

    public function maximum(int|float|null $maximum): static
    {
        return $this->set('maximum', $maximum);
    }

    public function exclusiveMaximum(int|float|bool|null $exclusiveMaximum): static
    {
        return $this->set('exclusiveMaximum', $exclusiveMaximum);
    }

    public function minimum(int|float|null $minimum): static
    {
        return $this->set('minimum', $minimum);
    }

    public function exclusiveMinimum(int|float|bool|null $exclusiveMinimum): static
    {
        return $this->set('exclusiveMinimum', $exclusiveMinimum);
    }

    public function multipleOf(int|float|null $multipleOf): static
    {
        return $this->set('multipleOf', $multipleOf);
    }

    public function required(mixed ...$required): static
    {
        $required = array_map(
            static fn (mixed $item): mixed => $item instanceof self ? $item->objectId : $item,
            $required
        );

        return $this->set('required', $required ?: null);
    }

    public function properties(mixed ...$properties): static
    {
        return $this->set('properties', $properties ?: null);
    }

    public function allOf(mixed ...$schemas): static
    {
        return $this->set('allOf', $schemas ?: null);
    }

    public function anyOf(mixed ...$schemas): static
    {
        return $this->set('anyOf', $schemas ?: null);
    }

    public function oneOf(mixed ...$schemas): static
    {
        return $this->set('oneOf', $schemas ?: null);
    }

    public function not(mixed $schema): static
    {
        return $this->set('not', $schema);
    }

    public function additionalProperties(mixed $additionalProperties): static
    {
        return $this->set('additionalProperties', $additionalProperties);
    }

    public function maxProperties(?int $maxProperties): static
    {
        return $this->set('maxProperties', $maxProperties);
    }

    public function minProperties(?int $minProperties): static
    {
        return $this->set('minProperties', $minProperties);
    }

    public function nullable(?bool $nullable = true): static
    {
        return $this->set('nullable', $nullable);
    }

    public function discriminator(mixed $discriminator): static
    {
        return $this->set('discriminator', $discriminator);
    }

    public function readOnly(?bool $readOnly = true): static
    {
        return $this->set('readOnly', $readOnly);
    }

    public function writeOnly(?bool $writeOnly = true): static
    {
        return $this->set('writeOnly', $writeOnly);
    }

    public function xml(mixed $xml): static
    {
        return $this->set('xml', $xml);
    }

    public function externalDocs(mixed $externalDocs): static
    {
        return $this->set('externalDocs', $externalDocs);
    }

    public function example(mixed $example): static
    {
        return $this->set('example', $example);
    }

    public function deprecated(?bool $deprecated = true): static
    {
        return $this->set('deprecated', $deprecated);
    }

    public function toArray(): array
    {
        if ($this->ref === null) {
            return parent::toArray();
        }

        $properties = $this->filter($this->build());
        unset($properties['schema']);

        if ($properties === []) {
            return ['$ref' => $this->ref];
        }

        return array_merge(['$ref' => $this->ref], $properties);
    }

    protected function build(): array
    {
        return array_merge($this->identifierProperties(), [
            'title' => $this->properties['title'] ?? null,
            'description' => $this->properties['description'] ?? null,
            'maxProperties' => $this->properties['maxProperties'] ?? null,
            'minProperties' => $this->properties['minProperties'] ?? null,
            'required' => $this->properties['required'] ?? null,
            'properties' => $this->keyedBy('properties', 'objectId') ?: null,
            'type' => $this->properties['type'] ?? null,
            'format' => $this->properties['format'] ?? null,
            'items' => $this->properties['items'] ?? null,
            'collectionFormat' => $this->properties['collectionFormat'] ?? null,
            'default' => $this->properties['default'] ?? null,
            'maximum' => $this->properties['maximum'] ?? null,
            'exclusiveMaximum' => $this->properties['exclusiveMaximum'] ?? null,
            'minimum' => $this->properties['minimum'] ?? null,
            'exclusiveMinimum' => $this->properties['exclusiveMinimum'] ?? null,
            'maxLength' => $this->properties['maxLength'] ?? null,
            'minLength' => $this->properties['minLength'] ?? null,
            'pattern' => $this->properties['pattern'] ?? null,
            'maxItems' => $this->properties['maxItems'] ?? null,
            'minItems' => $this->properties['minItems'] ?? null,
            'uniqueItems' => $this->properties['uniqueItems'] ?? null,
            'enum' => $this->properties['enum'] ?? null,
            'multipleOf' => $this->properties['multipleOf'] ?? null,
            'discriminator' => $this->properties['discriminator'] ?? null,
            'readOnly' => $this->properties['readOnly'] ?? null,
            'writeOnly' => $this->properties['writeOnly'] ?? null,
            'xml' => $this->properties['xml'] ?? null,
            'externalDocs' => $this->properties['externalDocs'] ?? null,
            'example' => $this->properties['example'] ?? null,
            'nullable' => $this->properties['nullable'] ?? null,
            'deprecated' => $this->properties['deprecated'] ?? null,
            'allOf' => $this->properties['allOf'] ?? null,
            'anyOf' => $this->properties['anyOf'] ?? null,
            'oneOf' => $this->properties['oneOf'] ?? null,
            'not' => $this->properties['not'] ?? null,
            'additionalProperties' => $this->properties['additionalProperties'] ?? null,
            'const' => $this->properties['const'] ?? null,
            'x' => $this->extensions ?: null,
        ]);
    }

    protected function identifierField(): ?string
    {
        return 'schema';
    }

    protected function annotationClass(): string
    {
        return SwaggerSchema::class;
    }
}
