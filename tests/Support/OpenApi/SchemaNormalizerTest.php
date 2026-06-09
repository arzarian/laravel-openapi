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

    protected function normalizer(): SchemaNormalizer
    {
        return new SchemaNormalizer();
    }
}
