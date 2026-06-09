<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Components;
use Vyuldashev\LaravelOpenApi\Builders\Components\CallbacksBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\RequestBodiesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\ResponsesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\SchemasBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\SecuritySchemesBuilder;
use Vyuldashev\LaravelOpenApi\Generator;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ComponentsBuilder
{
    protected CallbacksBuilder $callbacksBuilder;
    protected RequestBodiesBuilder $requestBodiesBuilder;
    protected ResponsesBuilder $responsesBuilder;
    protected SchemasBuilder $schemasBuilder;
    protected SecuritySchemesBuilder $securitySchemesBuilder;
    protected SpecificationObjectSerializer $serializer;

    public function __construct(
        CallbacksBuilder $callbacksBuilder,
        RequestBodiesBuilder $requestBodiesBuilder,
        ResponsesBuilder $responsesBuilder,
        SchemasBuilder $schemasBuilder,
        SecuritySchemesBuilder $securitySchemesBuilder,
        SpecificationObjectSerializer $serializer
    ) {
        $this->callbacksBuilder = $callbacksBuilder;
        $this->requestBodiesBuilder = $requestBodiesBuilder;
        $this->responsesBuilder = $responsesBuilder;
        $this->schemasBuilder = $schemasBuilder;
        $this->securitySchemesBuilder = $securitySchemesBuilder;
        $this->serializer = $serializer;
    }

    public function build(
        string $collection = Generator::COLLECTION_DEFAULT,
        array $middlewares = []
    ): ?Components {
        $callbacks = $this->callbacksBuilder->build($collection);
        $requestBodies = $this->requestBodiesBuilder->build($collection);
        $responses = $this->responsesBuilder->build($collection);
        $schemas = $this->schemasBuilder->build($collection);
        $securitySchemes = $this->securitySchemesBuilder->build($collection);

        $properties = [];

        $hasAnyObjects = false;

        if (count($callbacks) > 0) {
            $hasAnyObjects = true;

            $properties['callbacks'] = $this->callbacksToArray($callbacks);
        }

        if (count($requestBodies) > 0) {
            $hasAnyObjects = true;

            $properties['requestBodies'] = $requestBodies;
        }

        if (count($responses) > 0) {
            $hasAnyObjects = true;
            $properties['responses'] = $responses;
        }

        if (count($schemas) > 0) {
            $hasAnyObjects = true;
            $properties['schemas'] = $schemas;
        }

        if (count($securitySchemes) > 0) {
            $hasAnyObjects = true;
            $properties['securitySchemes'] = $securitySchemes;
        }

        if (! $hasAnyObjects) {
            return null;
        }

        $components = new Components($properties);

        foreach ($middlewares as $middleware) {
            app($middleware)->after($components);
        }

        return $components;
    }

    protected function callbacksToArray(array $callbacks): array
    {
        $mapped = [];

        foreach ($callbacks as $callback) {
            $key = $callback->name;
            $value = $this->serializer->toArray($callback);

            if ($key !== null) {
                $mapped[$key] = $value;
            }
        }

        return $mapped;
    }
}
