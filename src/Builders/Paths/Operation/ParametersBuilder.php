<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Schema;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Vyuldashev\LaravelOpenApi\Attributes\Parameters;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\SchemaHelpers;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\ParameterComponentNameResolver;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ParametersBuilder
{
    protected ParameterComponentNameResolver $nameResolver;

    public function __construct(
        protected SpecificationObjectSerializer $serializer,
        protected FactoryClassResolver $factoryClassResolver,
    ) {
        $this->nameResolver = new ParameterComponentNameResolver($serializer);
    }

    /**
     * @return array<int, Parameter>
     * @param RouteInformation $route
     */
    public function build(RouteInformation $route): array
    {
        $pathParameters = $this->buildPath($route);
        $attributedParameters = $this->buildAttribute($route);

        return $pathParameters->merge($attributedParameters)->values()->all();
    }

    /**
     * @return Collection<int, Parameter>
     * @param RouteInformation $route
     */
    protected function buildPath(RouteInformation $route): Collection
    {
        return $route->parameters
            ->map(static function (array $parameter) use ($route): ?Parameter {
                $schema = new Schema(['type' => 'string']);

                /** @var \ReflectionParameter|null $reflectionParameter */
                $reflectionParameter = collect($route->actionParameters)
                    ->first(static fn(\ReflectionParameter $reflectionParameter) => $reflectionParameter->name === $parameter['name']);

                if ($reflectionParameter) {
                    // The reflected param has no type, so ignore (should be defined in a ParametersFactory instead)
                    if ($reflectionParameter->getType() === null) {
                        return null;
                    }

                    if ($reflectionParameter->getType() instanceof \ReflectionNamedType) {
                        $schema = SchemaHelpers::guessFromReflectionType($reflectionParameter->getType());
                    }
                }

                if (\is_null($route->actionDocBlock)) {
                    throw new \Exception('Missing docblock for route: ' . $route->uri);
                }

                /** @var Param $description */
                $description = collect($route->actionDocBlock->getTagsByName('param'))
                    ->first(static fn($param): bool => $param instanceof Param
                        && $param->getVariableName() !== null
                        && Str::snake($param->getVariableName()) === Str::snake($parameter['name']));

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

    /**
     * @return Collection<int, Parameter>
     * @param RouteInformation $route
     */
    protected function buildAttribute(RouteInformation $route): Collection
    {
        /** @var Parameters|null $parameters */
        $parameters = $route->actionAttributes->first(static fn(object $attribute) => $attribute instanceof Parameters);

        if ($parameters) {
            /** @var ParameterFactory|ParametersFactory $parametersFactory */
            $parametersFactory = App::make($this->factoryClassResolver->parameters($parameters->factory));
            $builtParameters = $this->factoryParameters($parametersFactory);

            if ($parametersFactory instanceof Reusable) {
                return collect($builtParameters)
                    ->map(fn(mixed $parameter) => new Parameter([
                        'ref' => $this->reference($parametersFactory, $parameter),
                    ]));
            }

            $serialized = $this->serializer->toArray($builtParameters);

            $parameters = collect(\is_array($serialized) ? $serialized : [])
                ->map(fn(array $parameter) => new Parameter($this->serializer->properties($parameter)))
                ->all();
        }

        return collect($parameters);
    }

    /**
     * @return array<int, mixed>
     * @param ParameterFactory|ParametersFactory $parametersFactory
     */
    protected function factoryParameters(ParameterFactory|ParametersFactory $parametersFactory): array
    {
        if ($parametersFactory instanceof ParameterFactory) {
            return [$parametersFactory->build()];
        }

        return $parametersFactory->build();
    }

    protected function reference(ParameterFactory|ParametersFactory $parametersFactory, mixed $parameter): ?string
    {
        if ($parametersFactory instanceof ParameterFactory) {
            return '#/components/parameters/' . $this->nameResolver->forParameterFactory($parametersFactory, $parameter);
        }

        if ($this->nameResolver->isDirectReference($parameter)) {
            return $this->nameResolver->referenceTarget($parameter);
        }

        return '#/components/parameters/' . $this->nameResolver->forParametersFactory($parametersFactory, $parameter);
    }
}
