<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Schema;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use ReflectionParameter;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\SchemaHelpers;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ParametersBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer
    ) {
    }

    public function build(RouteInformation $route): array
    {
        $pathParameters = $this->buildPath($route);
        $attributedParameters = $this->buildAttribute($route);

        return $pathParameters->merge($attributedParameters)->toArray();
    }

    protected function buildPath(RouteInformation $route): Collection
    {
        return collect($route->parameters)
            ->map(static function (array $parameter) use ($route) {
                $schema = new Schema(['type' => 'string']);

                /** @var ReflectionParameter|null $reflectionParameter */
                $reflectionParameter = collect($route->actionParameters)
                    ->first(static fn (ReflectionParameter $reflectionParameter) => $reflectionParameter->name === $parameter['name']);

                if ($reflectionParameter) {
                    // The reflected param has no type, so ignore (should be defined in a ParametersFactory instead)
                    if ($reflectionParameter->getType() === null) {
                        return null;
                    }

                    if ($reflectionParameter->getType() instanceof \ReflectionNamedType) {
                        $schema = SchemaHelpers::guessFromReflectionType($reflectionParameter->getType());
                    }
                }

                if (is_null($route->actionDocBlock)) {
                    throw new \Exception('Missing docblock for route: '.$route->uri);
                }
                /** @var Param $description */
                $description = collect($route->actionDocBlock->getTagsByName('param'))
                    ->first(static fn (Param $param) => Str::snake($param->getVariableName()) === Str::snake($parameter['name']));

                return new Parameter([
                    'name' => $parameter['name'],
                    'in' => 'path',
                    'required' => true,
                    'description' => optional(optional($description)->getDescription())->render(),
                    'schema' => $schema,
                ]);
            })
            ->filter();
    }

    protected function buildAttribute(RouteInformation $route): Collection
    {
        /** @var Parameters|null $parameters */
        $parameters = $route->actionAttributes->first(static fn ($attribute) => $attribute instanceof Parameters, []);

        if ($parameters) {
            /** @var ParametersFactory $parametersFactory */
            $parametersFactory = app($parameters->factory);

            $parameters = collect($this->serializer->toArray($parametersFactory->build()))
                ->map(fn (array $parameter) => new Parameter($this->serializer->properties($parameter)))
                ->toArray();
        }

        return collect($parameters);
    }
}
