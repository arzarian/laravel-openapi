<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Builders\Server;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;
use Vyuldashev\LaravelOpenApi\Factories\ServerFactory;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\CallbackDefinition;
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

    public function testFactoryBuildMethodsExposeConcreteReturnTypes(): void
    {
        self::assertSame([CallbackDefinition::class, \Vyuldashev\LaravelOpenApi\Builders\Callback::class], $this->unionReturnTypes(CallbackFactory::class, 'build'));
        self::assertSame(Parameter::class, $this->returnType(ParameterFactory::class, 'build'));
        self::assertSame('array', $this->returnType(ParametersFactory::class, 'build'));
        self::assertSame(RequestBody::class, $this->returnType(RequestBodyFactory::class, 'build'));
        self::assertSame(Response::class, $this->returnType(ResponseFactory::class, 'build'));
        self::assertSame(Schema::class, $this->returnType(SchemaFactory::class, 'build'));
        self::assertSame(SecurityScheme::class, $this->returnType(SecuritySchemeFactory::class, 'build'));
        self::assertSame(Server::class, $this->returnType(ServerFactory::class, 'build'));
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

    /**
     * @param class-string $class
     * @param string $method
     * @return list<string>
     */
    private function unionReturnTypes(string $class, string $method): array
    {
        $returnType = new \ReflectionMethod($class, $method)->getReturnType();

        self::assertInstanceOf(\ReflectionUnionType::class, $returnType);

        return \array_map(
            static fn(\ReflectionNamedType $type): string => $type->getName(),
            $returnType->getTypes(),
        );
    }
}
