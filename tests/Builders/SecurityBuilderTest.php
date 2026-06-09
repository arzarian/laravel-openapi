<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Builders;

use phpDocumentor\Reflection\DocBlock;
use Vyuldashev\LaravelOpenApi\Attributes\Operation as AttributesOperation;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\SecurityBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\OperationsBuilder;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class SecurityBuilderTest extends TestCase
{
    /**
     * We're just making sure we're getting the expected output.
     */
    public function testWeCanBuildUpTheSecurityScheme(): void
    {
        $securityFactory = resolve(JwtSecurityScheme::class);
        $testJwtScheme = $securityFactory->build();

        self::assertSame([
            'securityScheme' => 'JWT',
            'name' => 'TestScheme',
            'type' => 'http',
            'in' => 'header',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ], \json_decode($testJwtScheme->toJson(), true));
    }

    /**
     * We're just verifying that the builder is capable of
     * adding security information to the operation.
     */
    public function testWeCanAddOperationSecurityUsingBuilder(): void
    {
        $routeInfo = new RouteInformation();
        $routeInfo->action = 'get';
        $routeInfo->name = 'test route';
        $routeInfo->actionAttributes = collect([
            new AttributesOperation(security: JwtSecurityScheme::class),
        ]);
        $routeInfo->uri = '/example';

        /** @var SecurityBuilder */
        $builder = resolve(SecurityBuilder::class);

        self::assertSame([
            [
                'JWT' => [],
            ],
        ], $builder->build($routeInfo));
    }

    /**
     * He's the main part of the PR. It's not possible to turn
     * off security for an operation.
     */
    public function testWeCanAddTurnOffOperationSecurityUsingBuilder(): void
    {
        $routeInfo = new RouteInformation();
        $routeInfo->parameters = collect();
        $routeInfo->action = 'foo';
        $routeInfo->method = 'get';
        $routeInfo->name = 'test route';
        $routeInfo->actionDocBlock = new DocBlock('Test');
        $routeInfo->actionAttributes = collect([
            /**
             * we can set secuity to null to turn it off, as
             * that's the default value. So '' is next best
             * option?
             */
            new AttributesOperation(security: ''),
        ]);

        /** @var OperationsBuilder */
        $operationsBuilder = resolve(OperationsBuilder::class);

        $operations = $operationsBuilder->build([$routeInfo]);

        self::assertSame([
            'summary' => 'Test',
            'security' => [],
        ], \json_decode((string)$operations[0]->toJson(), true));
    }
}

class JwtSecurityScheme extends SecuritySchemeFactory
{
    public function build(): SecurityScheme
    {
        return SecurityScheme::create('JWT')
            ->name('TestScheme')
            ->type(SecurityScheme::TYPE_HTTP)
            ->in(SecurityScheme::IN_HEADER)
            ->scheme('bearer')
            ->bearerFormat('JWT');
    }
}
