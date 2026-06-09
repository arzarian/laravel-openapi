<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Console;

use Examples\Petstore\PetController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class GenerateCommandTest extends TestCase
{
    protected function setUp(): void
    {
        \putenv('APP_URL=http://petstore.swagger.io/v1');

        parent::setUp();

        Route::get('/pets', [PetController::class, 'index']);
    }

    public function testOutputsGeneratedSpecification(): void
    {
        $exitCode = Artisan::call('openapi:generate');

        $spec = \json_decode(Artisan::output(), true);

        self::assertSame(0, $exitCode);
        self::assertSame('3.0.0', $spec['openapi']);
        self::assertSame('http://petstore.swagger.io/v1', $spec['servers'][0]['url']);
        self::assertArrayHasKey('/pets', $spec['paths']);
        self::assertArrayHasKey('Pet', $spec['components']['schemas']);
    }

    public function testWritesGeneratedSpecificationToOutputFile(): void
    {
        $path = \tempnam(\sys_get_temp_dir(), 'laravel-openapi-');

        try {
            $exitCode = Artisan::call('openapi:generate', [
                '--output' => $path,
            ]);

            $spec = \json_decode(\file_get_contents($path), true);

            self::assertSame(0, $exitCode);
            self::assertStringContainsString('OpenAPI specification generated successfully.', Artisan::output());
            self::assertSame('3.0.0', $spec['openapi']);
            self::assertArrayHasKey('/pets', $spec['paths']);
            self::assertArrayHasKey('ErrorValidation', $spec['components']['responses']);
        } finally {
            if (\is_string($path) && \file_exists($path)) {
                \unlink($path);
            }
        }
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('openapi.locations.schemas', [
            __DIR__ . '/../../examples/petstore/OpenApi/Schemas',
        ]);
        $app['config']->set('openapi.locations.responses', [
            __DIR__ . '/../../examples/petstore/OpenApi/Responses',
        ]);
    }
}
