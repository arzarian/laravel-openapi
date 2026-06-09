<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests;

use Illuminate\Support\Facades\Route;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\ReusableMethodParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\ReusableParametersController;

class ReusableParametersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/single-reusable-parameter', [ReusableParametersController::class, 'singleReusableParameter']);
        Route::get('/list-reusable-parameters', [ReusableParametersController::class, 'listReusableParameters']);
        Route::get('/inline-parameters', [ReusableParametersController::class, 'inlineParameters']);
        Route::get('/inline-parameter', [ReusableParametersController::class, 'inlineParameter']);

        config()->set('openapi.locations.parameters', [
            __DIR__ . '/Fixtures/OpenApi/Parameters',
        ]);
    }

    public function testReusableSingleParameterIsGeneratedAsComponentReference(): void
    {
        // Verifies reusable ParameterFactory is emitted into components and referenced by operations.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/Slug',
            ],
        ], $spec['paths']['/single-reusable-parameter']['get']['parameters']);

        self::assertSame([
            'in' => 'path',
            'name' => 'slug',
            'required' => true,
            'description' => 'Slug',
            'schema' => [
                'type' => 'string',
                'example' => 'slug',
            ],
        ], $spec['components']['parameters']['Slug']);
    }

    public function testReusableParameterListIsGeneratedAsComponentReferences(): void
    {
        // Verifies reusable ParametersFactory emits refs for every built parameter.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/slug',
            ],
            [
                '$ref' => '#/components/parameters/page',
            ],
        ], $spec['paths']['/list-reusable-parameters']['get']['parameters']);

        self::assertSame([
            '$ref' => '#/components/parameters/Slug',
        ], $spec['components']['parameters']['slug']);
        self::assertSame('page', $spec['components']['parameters']['page']['name']);
    }

    public function testNonReusableParametersStayInline(): void
    {
        // Verifies non-reusable factories keep old inline operation behavior.
        $spec = $this->generateArray();

        self::assertSame([
            [
                'name' => 'search',
                'in' => 'query',
                'description' => 'Inline search parameter',
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ], $spec['paths']['/inline-parameters']['get']['parameters']);

        self::assertSame([
            [
                'name' => 'single',
                'in' => 'query',
                'description' => 'Single inline parameter',
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ], $spec['paths']['/inline-parameter']['get']['parameters']);
    }

    public function testReusableParameterListRefKeepsFirstParameterContract(): void
    {
        // Verifies ParametersFactory::ref() stays backward compatible with first parameter refs.
        self::assertSame([
            '$ref' => '#/components/parameters/slug',
        ], ReusableMethodParameters::ref()->toArray());
    }
}
