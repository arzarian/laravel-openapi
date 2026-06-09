<?php

namespace Vyuldashev\LaravelOpenApi\Tests\Builders;

use OpenApi\Annotations\Get;
use OpenApi\Annotations\Schema;
use Vyuldashev\LaravelOpenApi\Attributes\Extension;
use Vyuldashev\LaravelOpenApi\Builders\ExtensionsBuilder;
use Vyuldashev\LaravelOpenApi\Factories\ExtensionFactory;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class ExtensionsBuilderTest extends TestCase
{
    public function testBuildUsingFactory(): void
    {
        $operation = new Get([]);

        /** @var ExtensionsBuilder $builder */
        $builder = resolve(ExtensionsBuilder::class);
        $builder->build($operation, collect([
            new Extension(factory: FakeExtension::class),
        ]));

        self::assertSame([
            'x-uuid' => ['type' => 'string', 'format' => 'uuid'],
        ], json_decode($operation->toJson(), true));
    }

    public function testBuildUsingKeyValue(): void
    {
        $operation = new Get([]);

        /** @var ExtensionsBuilder $builder */
        $builder = resolve(ExtensionsBuilder::class);
        $builder->build($operation, collect([
            new Extension(key: 'foo', value: 'bar'),
            new Extension(key: 'x-key', value: '1'),
        ]));

        self::assertSame([
            'x-foo' => 'bar',
            'x-key' => '1',
        ], json_decode($operation->toJson(), true));
    }
}

class FakeExtension extends ExtensionFactory
{
    public function key(): string
    {
        return 'uuid';
    }

    /**
     * @return string|null|array
     */
    public function value()
    {
        return new Schema([
            'format' => 'uuid',
            'type' => 'string',
        ]);
    }
}
