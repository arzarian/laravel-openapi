<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Support;

use Examples\Petstore\OpenApi\Responses\ErrorValidationResponse;
use Vyuldashev\LaravelOpenApi\Attributes\Response as ResponseAttribute;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\ShortNameResponse;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class FactoryClassResolverTest extends TestCase
{
    public function testResolvesFactoryFqcn(): void
    {
        $resolver = new FactoryClassResolver();

        self::assertSame(ErrorValidationResponse::class, $resolver->response(ErrorValidationResponse::class));
    }

    public function testResolvesFactoryShortNameFromApplicationNamespace(): void
    {
        $resolver = new FactoryClassResolver();
        $class = $this->app->getNamespace() . 'OpenApi\\Responses\\ShortNameResponse';

        if (!\class_exists($class)) {
            \class_alias(ShortNameResponse::class, $class);
        }

        self::assertSame($class, $resolver->response('ShortNameResponse'));
    }

    public function testRejectsFactoryWithWrongContract(): void
    {
        $resolver = new FactoryClassResolver();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Factory class must be instance of ResponseFactory');

        $resolver->response(self::class);
    }

    public function testAttributesKeepRawFactoryValue(): void
    {
        $attribute = new ResponseAttribute('ShortNameResponse');

        self::assertSame('ShortNameResponse', $attribute->factory);
    }
}
