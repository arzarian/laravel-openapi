<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\Factories\ExtensionFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;

class FactoryClassResolver
{
    /**
     * @param string $factory
     * @return class-string<CallbackFactory>
     */
    public function callback(string $factory): string
    {
        return $this->resolve($factory, 'OpenApi\\Callbacks\\', CallbackFactory::class, 'Factory class must be instance of CallbackFactory');
    }

    /**
     * @param string $factory
     * @return class-string<ExtensionFactory>
     */
    public function extension(string $factory): string
    {
        return $this->resolve($factory, 'OpenApi\\Extensions\\', ExtensionFactory::class, 'Factory class must be instance of ExtensionFactory');
    }

    /**
     * @param string $factory
     * @return class-string<ParametersFactory>
     */
    public function parameters(string $factory): string
    {
        return $this->resolve($factory, 'OpenApi\\Parameters\\', ParametersFactory::class, 'Factory class must be instance of ParametersFactory');
    }

    /**
     * @param string $factory
     * @return class-string<RequestBodyFactory>
     */
    public function requestBody(string $factory): string
    {
        return $this->resolve($factory, 'OpenApi\\RequestBodies\\', RequestBodyFactory::class, 'Factory class must be instance of RequestBodyFactory');
    }

    /**
     * @param string $factory
     * @return class-string<ResponseFactory>
     */
    public function response(string $factory): string
    {
        return $this->resolve($factory, 'OpenApi\\Responses\\', ResponseFactory::class, 'Factory class must be instance of ResponseFactory');
    }

    /**
     * @param string $factory
     * @return class-string<SecuritySchemeFactory>
     */
    public function securityScheme(string $factory): string
    {
        return $this->resolve(
            $factory,
            'OpenApi\\SecuritySchemes\\',
            SecuritySchemeFactory::class,
            \sprintf('Security class is either not declared or is not an instance of %s', SecuritySchemeFactory::class),
        );
    }

    /**
     * @template T of object
     *
     * @param string $factory
     * @param string $namespace
     * @param class-string<T> $expected
     * @param string $message
     * @return class-string<T>
     */
    private function resolve(string $factory, string $namespace, string $expected, string $message): string
    {
        $class = \class_exists($factory) ? $factory : App::getNamespace() . $namespace . $factory;

        if (!\is_a($class, $expected, true)) {
            throw new \InvalidArgumentException($message);
        }

        return $class;
    }
}
