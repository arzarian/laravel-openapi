<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class FactoryContractsTest extends TestCase
{
    public function testReferencableFactoriesExposeConcreteRefReturnTypes(): void
    {
        self::assertSame('array', $this->returnType(CallbackFactory::class, 'ref'));
        self::assertSame(Parameter::class, $this->returnType(ParameterFactory::class, 'ref'));
        self::assertSame(Parameter::class, $this->returnType(ParametersFactory::class, 'ref'));
        self::assertSame(RequestBody::class, $this->returnType(RequestBodyFactory::class, 'ref'));
        self::assertSame(Response::class, $this->returnType(ResponseFactory::class, 'ref'));
        self::assertSame(Schema::class, $this->returnType(SchemaFactory::class, 'ref'));
        self::assertSame(SecurityScheme::class, $this->returnType(SecuritySchemeFactory::class, 'ref'));
    }

    public function testFactoryBuildMethodsKeepCompatibleReturnSurface(): void
    {
        self::assertNull(new \ReflectionMethod(ParameterFactory::class, 'build')->getReturnType());
        self::assertNull(new \ReflectionMethod(ResponseFactory::class, 'build')->getReturnType());
        self::assertNull(new \ReflectionMethod(RequestBodyFactory::class, 'build')->getReturnType());
        self::assertNull(new \ReflectionMethod(SchemaFactory::class, 'build')->getReturnType());
        self::assertNull(new \ReflectionMethod(SecuritySchemeFactory::class, 'build')->getReturnType());
    }

    /**
     * @param class-string $class
     * @param string $method
     */
    private function returnType(string $class, string $method): string
    {
        $returnType = new \ReflectionMethod($class, $method)->getReturnType();

        self::assertInstanceOf(\ReflectionNamedType::class, $returnType);

        return $returnType->getName();
    }
}
