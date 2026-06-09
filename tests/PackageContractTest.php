<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests;

use Examples\Petstore\OpenApi\Responses\ErrorValidationResponse;
use Examples\Petstore\PetController;
use Illuminate\Support\Facades\Route;
use OpenApi\Annotations\Response;
use Vyuldashev\LaravelOpenApi\Attributes\Response as ResponseAttribute;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\ResponsesBuilder;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware\FirstComponentMiddleware;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware\FirstPathMiddleware;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware\MiddlewareLog;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware\SecondComponentMiddleware;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\Middleware\SecondPathMiddleware;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Bound\Schemas\BoundSchema;

class PackageContractTest extends TestCase
{
    public function testComponentFactoriesUseContainerBindings(): void
    {
        $this->app->bind(BoundSchema::class, static fn(): BoundSchema => new BoundSchema('BoundFromContainer'));
        config()->set('openapi.locations.schemas', [
            __DIR__ . '/Fixtures/OpenApi/Bound/Schemas',
        ]);

        $spec = $this->generateArray();

        self::assertArrayHasKey('BoundFromContainer', $spec['components']['schemas']);
    }

    public function testPathMiddlewaresRunInConfiguredOrder(): void
    {
        MiddlewareLog::reset();
        Route::get('/pets', [PetController::class, 'index']);
        config()->set('openapi.collections.default.middlewares.paths', [
            FirstPathMiddleware::class,
            SecondPathMiddleware::class,
        ]);

        $this->generateArray();

        self::assertSame([
            'first-path-before',
            'second-path-before',
            'first-path-after',
            'second-path-after',
        ], MiddlewareLog::$events);
    }

    public function testComponentMiddlewaresRunInConfiguredOrder(): void
    {
        MiddlewareLog::reset();
        config()->set('openapi.locations.schemas', [
            __DIR__ . '/Fixtures/OpenApi/Schemas',
        ]);
        config()->set('openapi.collections.default.middlewares.components', [
            FirstComponentMiddleware::class,
            SecondComponentMiddleware::class,
        ]);

        $this->generateArray();

        self::assertSame([
            'first-component-after',
            'second-component-after',
        ], MiddlewareLog::$events);
    }

    public function testResponseAttributesResolveFqcnFactories(): void
    {
        $routeInformation = new RouteInformation();
        $routeInformation->actionAttributes = collect([
            new ResponseAttribute(ErrorValidationResponse::class, 422),
        ]);

        /** @var ResponsesBuilder $builder */
        $builder = resolve(ResponsesBuilder::class);
        $responses = $builder->build($routeInformation);

        self::assertInstanceOf(Response::class, $responses[0]);
    }

    public function testResponseAttributesResolveShortFactoryNames(): void
    {
        $class = $this->app->getNamespace() . 'OpenApi\\Responses\\ShortNameResponse';

        if (!\class_exists($class)) {
            \class_alias(Fixtures\ShortNameResponse::class, $class);
        }

        $routeInformation = new RouteInformation();
        $routeInformation->actionAttributes = collect([
            new ResponseAttribute('ShortNameResponse', 200),
        ]);

        /** @var ResponsesBuilder $builder */
        $builder = resolve(ResponsesBuilder::class);
        $responses = $builder->build($routeInformation);

        self::assertInstanceOf(Response::class, $responses[0]);
    }
}
