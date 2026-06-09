<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests;

use Illuminate\Support\Facades\Route;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\EmptyParameters;
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
        Route::get('/user-index-parameters', [ReusableParametersController::class, 'userIndexParameters']);
        Route::get('/order-index-parameters', [ReusableParametersController::class, 'orderIndexParameters']);
        Route::get('/kebab-case-parameters', [ReusableParametersController::class, 'kebabCaseParameters']);
        Route::get('/explicit-id-parameters', [ReusableParametersController::class, 'explicitIdParameters']);
        Route::get('/single-generated-name-parameter', [ReusableParametersController::class, 'singleGeneratedNameParameter']);
        Route::get('/direct-reference-parameters', [ReusableParametersController::class, 'directReferenceParameters']);
        Route::get('/param-slug-method-parameters', [ReusableParametersController::class, 'paramSlugMethodParameters']);
        Route::get('/array-object-id-parameters', [ReusableParametersController::class, 'arrayObjectIdParameters']);

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
                '$ref' => '#/components/parameters/Slug',
            ],
            [
                '$ref' => '#/components/parameters/ReusableMethodParametersPage',
            ],
        ], $spec['paths']['/list-reusable-parameters']['get']['parameters']);

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
        self::assertSame('page', $spec['components']['parameters']['ReusableMethodParametersPage']['name']);
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
            '$ref' => '#/components/parameters/Slug',
        ], ReusableMethodParameters::ref()->toArray());
    }

    public function testReusableParameterListScopesGeneratedFallbackNames(): void
    {
        // Verifies same parameter names in different reusable lists do not collide.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/UserIndexParametersPage',
            ],
        ], $spec['paths']['/user-index-parameters']['get']['parameters']);
        self::assertSame([
            [
                '$ref' => '#/components/parameters/OrderIndexParametersPage',
            ],
        ], $spec['paths']['/order-index-parameters']['get']['parameters']);

        self::assertSame('page', $spec['components']['parameters']['UserIndexParametersPage']['name']);
        self::assertSame('page', $spec['components']['parameters']['OrderIndexParametersPage']['name']);
    }

    public function testGeneratedFallbackNamesArePascalCase(): void
    {
        // Verifies fallback names from snake/kebab-case parameter names are normalized.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/KebabCaseParametersXRequestId',
            ],
        ], $spec['paths']['/kebab-case-parameters']['get']['parameters']);
        self::assertSame('x-request-id', $spec['components']['parameters']['KebabCaseParametersXRequestId']['name']);

        self::assertSame([
            [
                '$ref' => '#/components/parameters/UserId',
            ],
        ], $spec['paths']['/single-generated-name-parameter']['get']['parameters']);
        self::assertSame('user_id', $spec['components']['parameters']['UserId']['name']);
    }

    public function testExplicitParameterIdsArePreserved(): void
    {
        // Verifies explicit object ids are not scoped or normalized.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/user_id',
            ],
        ], $spec['paths']['/explicit-id-parameters']['get']['parameters']);
        self::assertSame('user_id', $spec['components']['parameters']['user_id']['name']);
    }

    public function testDirectParameterReferencesStayDirect(): void
    {
        // Verifies full refs without local aliases are not rewritten into scoped aliases.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/Slug',
            ],
        ], $spec['paths']['/direct-reference-parameters']['get']['parameters']);
        self::assertArrayNotHasKey('DirectReferenceParametersSlug', $spec['components']['parameters']);
    }

    public function testParameterFactoryReferenceAliasSetsParameterName(): void
    {
        // Verifies ref object id in a reusable ParametersFactory acts like the parameter name alias.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/SlugParameter',
            ],
        ], $spec['paths']['/param-slug-method-parameters']['get']['parameters']);

        self::assertSame([
            'in' => 'path',
            'required' => true,
            'description' => 'Slug',
            'schema' => [
                'type' => 'string',
                'example' => 'slug',
            ],
            'name' => 'param_slug',
        ], $spec['components']['parameters']['SlugParameter']);
    }

    public function testParameterObjectIdDoesNotBecomeReference(): void
    {
        // Verifies parameter objectId does not masquerade as a ref target.
        $spec = $this->generateArray();

        self::assertSame([
            [
                '$ref' => '#/components/parameters/array_object_id',
            ],
        ], $spec['paths']['/array-object-id-parameters']['get']['parameters']);

        self::assertSame([
            'in' => 'query',
            'name' => 'array_object_id',
            'description' => 'Array object id parameter',
            'schema' => [
                'type' => 'string',
            ],
        ], $spec['components']['parameters']['array_object_id']);
    }

    public function testEmptyReusableParametersFactoryRefFailsClearly(): void
    {
        // Verifies empty reusable parameter lists fail with a domain exception.
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Parameters factory refs require at least one parameter.');

        EmptyParameters::ref();
    }

    public function testParameterAliasConflictsFailClearly(): void
    {
        // Verifies conflicting aliases for one parameter component are not silently overwritten.
        config()->set('openapi.locations.parameters', [
            __DIR__ . '/Fixtures/OpenApi/Parameters',
            __DIR__ . '/Fixtures/OpenApi/ConflictingParameters',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Parameter component [SlugParameter] has conflicting aliases [param_slug] and [conflicting_slug].');

        $this->generateArray();
    }
}
