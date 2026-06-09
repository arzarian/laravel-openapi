<?php

namespace Vyuldashev\LaravelOpenApi\Concerns;

use InvalidArgumentException;
use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody;
use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Builders\SpecificationBuilder;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

trait Referencable
{
    public static function ref(?string $objectId = null): SpecificationBuilder|array
    {
        $instance = app(static::class);

        if (! $instance instanceof Reusable) {
            throw new InvalidArgumentException('"'.static::class.'" must implement "'.Reusable::class.'" in order to be referencable.');
        }

        $baseRef = null;
        $name = null;
        $serializer = new SpecificationObjectSerializer();

        if ($instance instanceof CallbackFactory) {
            $baseRef = '#/components/callbacks/';
            $name = $instance->build()->name;
        } elseif ($instance instanceof ParametersFactory) {
            $baseRef = '#/components/parameters/';
            $name = $serializer->componentName($instance->build()[0], 'parameter');
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

        $ref = $baseRef.$name;

        return match (true) {
            $instance instanceof ParametersFactory => Parameter::ref($ref, $objectId),
            $instance instanceof RequestBodyFactory => RequestBody::ref($ref, $objectId),
            $instance instanceof ResponseFactory => Response::ref($ref, $objectId),
            $instance instanceof SecuritySchemeFactory => SecurityScheme::ref($ref, $objectId),
            $instance instanceof CallbackFactory => ['$ref' => $ref],
            default => Schema::ref($ref, $objectId),
        };
    }
}
