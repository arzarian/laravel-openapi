<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Concerns;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Builders\SpecificationBuilder;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\ParameterComponentNameResolver;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

trait Referencable
{
    /**
     * @return SpecificationBuilder|array<string, string>
     * @param ?string $objectId
     */
    public static function ref(?string $objectId = null): SpecificationBuilder|array
    {
        $instance = App::make(static::class);

        if (! $instance instanceof Reusable) {
            throw new \InvalidArgumentException('"' . static::class . '" must implement "' . Reusable::class . '" in order to be referencable.');
        }

        $baseRef = null;
        $name = null;
        $parameterNameResolver = new ParameterComponentNameResolver();
        $serializer = new SpecificationObjectSerializer();

        if ($instance instanceof CallbackFactory) {
            $baseRef = '#/components/callbacks/';
            $name = $serializer->componentName($instance->build(), 'name');
        } elseif ($instance instanceof ParameterFactory) {
            $baseRef = '#/components/parameters/';
            $parameter = $instance->build();
            $name = $parameterNameResolver->forParameterFactory($instance, $parameter);
        } elseif ($instance instanceof ParametersFactory) {
            $parameters = $instance->build();
            $parameter = $parameters[0] ?? null;

            if ($parameter === null) {
                throw new \UnexpectedValueException('Parameters factory refs require at least one parameter.');
            }

            if ($parameterNameResolver->isDirectReference($parameter)) {
                return Parameter::ref((string)$parameterNameResolver->referenceTarget($parameter), $objectId);
            }

            $baseRef = '#/components/parameters/';
            $name = $parameterNameResolver->forParametersFactory($instance, $parameter);
        } elseif ($instance instanceof RequestBodyFactory) {
            $baseRef = '#/components/requestBodies/';
            $name = $serializer->componentName($instance->build(), 'request');
        } elseif ($instance instanceof ResponseFactory) {
            $baseRef = '#/components/responses/';
            $name = $serializer->componentName($instance->build(), 'response');
        } elseif ($instance instanceof SchemaFactory) {
            $baseRef = '#/components/schemas/';
            $name = $serializer->componentName($instance->build(), 'schema');
        } elseif ($instance instanceof SecuritySchemeFactory) {
            $baseRef = '#/components/securitySchemes/';
            $name = $serializer->componentName($instance->build(), 'securityScheme');
        }

        $ref = $baseRef . $name;

        return match (true) {
            $instance instanceof ParameterFactory => Parameter::ref($ref, $objectId),
            $instance instanceof ParametersFactory => Parameter::ref($ref, $objectId),
            $instance instanceof RequestBodyFactory => RequestBody::ref($ref, $objectId),
            $instance instanceof ResponseFactory => Response::ref($ref, $objectId),
            $instance instanceof SecuritySchemeFactory => SecurityScheme::ref($ref, $objectId),
            $instance instanceof CallbackFactory => ['$ref' => $ref],
            default => Schema::ref($ref, $objectId),
        };
    }
}
