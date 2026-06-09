<?php

namespace Vyuldashev\LaravelOpenApi\Tests\Support\OpenApi;

use PHPUnit\Framework\TestCase;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SchemaNormalizer;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecVersion;

class SchemaNormalizerTest extends TestCase
{
    public function testOpenApi30KeepsNullableAndReplacesNullType(): void
    {
        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Example' => [
                        'type' => ['string', 'null'],
                        'const' => 'published',
                    ],
                ],
            ],
        ], new SpecVersion('3.0.4'));

        self::assertSame([
            'components' => [
                'schemas' => [
                    'Example' => [
                        'type' => 'string',
                        'nullable' => true,
                        'enum' => ['published'],
                    ],
                ],
            ],
        ], $specification);
    }

    public function testOpenApi31ReplacesNullableWithNullType(): void
    {
        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Example' => [
                        'type' => 'string',
                        'nullable' => true,
                        'const' => 'published',
                    ],
                ],
            ],
        ], new SpecVersion('3.1.2'));

        self::assertSame([
            'components' => [
                'schemas' => [
                    'Example' => [
                        'type' => ['string', 'null'],
                        'const' => 'published',
                    ],
                ],
            ],
        ], $specification);
    }

    public function testOpenApi30WrapsSchemaRefSiblingsWithAllOf(): void
    {
        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Wrapper' => [
                        'properties' => [
                            'item' => [
                                '$ref' => '#/components/schemas/Item',
                                'description' => 'Description',
                                'deprecated' => true,
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
            ],
        ], new SpecVersion('3.0.4'));

        self::assertSame([
            'allOf' => [
                ['$ref' => '#/components/schemas/Item'],
            ],
            'description' => 'Description',
            'deprecated' => true,
        ], $specification['components']['schemas']['Wrapper']['properties']['item']);
    }

    public function testOpenApi31KeepsValidSchemaRefSiblings(): void
    {
        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Wrapper' => [
                        'properties' => [
                            'item' => [
                                '$ref' => '#/components/schemas/Item',
                                'description' => 'Description',
                                'deprecated' => true,
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
            ],
        ], new SpecVersion('3.1.2'));

        self::assertSame([
            '$ref' => '#/components/schemas/Item',
            'description' => 'Description',
            'deprecated' => true,
        ], $specification['components']['schemas']['Wrapper']['properties']['item']);
    }

    public function testOpenApi30KeepsNullableRefAsAllOfNullable(): void
    {
        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Wrapper' => [
                        'properties' => [
                            'item' => [
                                '$ref' => '#/components/schemas/Item',
                                'nullable' => true,
                                'description' => 'Description',
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
            ],
        ], new SpecVersion('3.0.4'));

        self::assertSame([
            'allOf' => [
                ['$ref' => '#/components/schemas/Item'],
            ],
            'nullable' => true,
            'description' => 'Description',
        ], $specification['components']['schemas']['Wrapper']['properties']['item']);
    }

    public function testOpenApi31ReplacesNullableRefWithAnyOfNull(): void
    {
        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Wrapper' => [
                        'properties' => [
                            'item' => [
                                '$ref' => '#/components/schemas/Item',
                                'nullable' => true,
                                'description' => 'Description',
                                'deprecated' => true,
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
            ],
        ], new SpecVersion('3.1.2'));

        self::assertSame([
            'anyOf' => [
                ['$ref' => '#/components/schemas/Item'],
                ['type' => 'null'],
            ],
            'description' => 'Description',
            'deprecated' => true,
        ], $specification['components']['schemas']['Wrapper']['properties']['item']);
    }

    public function testPropertyNamedTypeIsNotNormalizedAsSchemaType(): void
    {
        $specification = [
            'components' => [
                'schemas' => [
                    'Example' => [
                        'properties' => [
                            'type' => [
                                'description' => 'Тип',
                                'type' => 'string',
                                'nullable' => true,
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
            ],
        ];

        self::assertSame($specification, $this->normalizer()->normalize(
            $specification,
            new SpecVersion('3.0.4')
        ));

        self::assertSame([
            'components' => [
                'schemas' => [
                    'Example' => [
                        'properties' => [
                            'type' => [
                                'description' => 'Тип',
                                'type' => ['string', 'null'],
                            ],
                        ],
                        'type' => 'object',
                    ],
                ],
            ],
        ], $this->normalizer()->normalize(
            $specification,
            new SpecVersion('3.1.2')
        ));
    }

    public function testPropertyMapWithTypePropertyFromBuilderKeepsPropertyName(): void
    {
        $schema = \Vyuldashev\LaravelOpenApi\Builders\Schema::object('Example')->properties(
            \Vyuldashev\LaravelOpenApi\Builders\Schema::string('type')
                ->nullable()
                ->description('Тип')
        );

        $specification = $this->normalizer()->normalize([
            'components' => [
                'schemas' => [
                    'Example' => $schema->toArray(),
                ],
            ],
        ], new SpecVersion('3.0.4'));

        self::assertSame([
            'description' => 'Тип',
            'type' => 'string',
            'nullable' => true,
        ], $specification['components']['schemas']['Example']['properties']['type']);
    }

    protected function normalizer(): SchemaNormalizer
    {
        return new SchemaNormalizer();
    }
}
