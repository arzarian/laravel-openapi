<?php

namespace Vyuldashev\LaravelOpenApi\Tests;

use InvalidArgumentException;

class GeneratorTest extends TestCase
{
    public function testUsesConfiguredOpenApi30Version(): void
    {
        config()->set('openapi.collections.default.openapi', '3.0.4');

        self::assertSame('3.0.4', $this->generateArray()['openapi']);
    }

    public function testUsesConfiguredOpenApi31Version(): void
    {
        config()->set('openapi.collections.default.openapi', '3.1.2');

        self::assertSame('3.1.2', $this->generateArray()['openapi']);
    }

    public function testRejectsUnsupportedOpenApiVersion(): void
    {
        config()->set('openapi.collections.default.openapi', '3.2.0');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported OpenAPI version [3.2.0]. Supported versions: 3.0.x, 3.1.x.');

        $this->generate();
    }
}
