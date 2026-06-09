<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Paths;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use OpenApi\Annotations\Delete;
use OpenApi\Annotations\Get;
use OpenApi\Annotations\Head;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Options;
use OpenApi\Annotations\Patch;
use OpenApi\Annotations\Post;
use OpenApi\Annotations\Put;
use OpenApi\Annotations\Trace;
use phpDocumentor\Reflection\DocBlock;
use Vyuldashev\LaravelOpenApi\Attributes\Operation as OperationAttribute;
use Vyuldashev\LaravelOpenApi\Builders\ExtensionsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\CallbacksBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\ParametersBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\RequestBodyBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\ResponsesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Paths\Operation\SecurityBuilder;
use Vyuldashev\LaravelOpenApi\Factories\ServerFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\CallbackDefinition;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class OperationsBuilder
{
    public function __construct(
        protected CallbacksBuilder $callbacksBuilder,
        protected ParametersBuilder $parametersBuilder,
        protected RequestBodyBuilder $requestBodyBuilder,
        protected ResponsesBuilder $responsesBuilder,
        protected ExtensionsBuilder $extensionsBuilder,
        protected SecurityBuilder $securityBuilder,
        protected SpecificationObjectSerializer $serializer,
    ) {
    }

    /**
     * @param array<int, RouteInformation>|Collection<int, RouteInformation> $routes
     * @return array<int, Operation>
     */
    public function build(array|Collection $routes): array
    {
        $operations = [];

        /** @var RouteInformation[] $routes */
        foreach ($routes as $route) {
            /** @var OperationAttribute|null $operationAttribute */
            $operationAttribute = $route->actionAttributes
                ->first(static fn(object $attribute) => $attribute instanceof OperationAttribute);

            if (!$operationAttribute instanceof OperationAttribute) {
                continue;
            }

            $operationId = optional($operationAttribute)->id;
            $tags = $operationAttribute->tags;
            $servers = collect($operationAttribute->servers ?? [])
                ->filter(static fn(string $server): bool => App::make($server) instanceof ServerFactory)
                ->map(static fn(string $server): mixed => App::make($server)->build())
                ->toArray();

            $parameters = $this->parametersBuilder->build($route);
            $requestBody = $this->requestBodyBuilder->build($route);
            $responses = $this->responsesBuilder->build($route);
            $callbacks = $this->callbacksBuilder->build($route);
            $security = $this->securityBuilder->build($route);

            $properties = [
                'tags' => $tags,
                'deprecated' => $this->isDeprecated($route->actionDocBlock),
                'description' => $route->actionDocBlock?->getDescription()->render() !== '' ? $route->actionDocBlock?->getDescription()->render() : null,
                'summary' => $route->actionDocBlock?->getSummary() !== '' ? $route->actionDocBlock?->getSummary() : null,
                'operationId' => $operationId,
                'parameters' => $parameters,
                'requestBody' => $requestBody,
                'responses' => $responses,
                'callbacks' => $this->callbacksToArray($callbacks),
                'servers' => $servers,
            ];

            $properties = $this->serializer->properties($properties);
            if ($security === [[]]) {
                $properties['security'] = [];
            } elseif ($security !== []) {
                $properties['security'] = $security;
            }

            $operation = $this->makeOperation(
                Str::lower($operationAttribute->method ?? '') ?: $route->method,
                $properties,
            );

            $this->extensionsBuilder->build($operation, $route->actionAttributes);

            $operations[] = $operation;
        }

        return $operations;
    }

    /**
     * @param array<string, mixed> $properties
     * @param string $method
     */
    protected function makeOperation(string $method, array $properties): Operation
    {
        return match ($method) {
            'get' => new Get($properties),
            'post' => new Post($properties),
            'put' => new Put($properties),
            'delete' => new Delete($properties),
            'options' => new Options($properties),
            'head' => new Head($properties),
            'patch' => new Patch($properties),
            'trace' => new Trace($properties),
            default => new Get($properties),
        };
    }

    /**
     * @param array<int, mixed> $callbacks
     * @return array<int|string, mixed>
     */
    protected function callbacksToArray(array $callbacks): array
    {
        $serialized = [];

        foreach ($callbacks as $callback) {
            if ($callback instanceof CallbackDefinition) {
                $serialized[$callback->name] = $this->serializer->toArray($callback);

                continue;
            }

            $callback = $this->serializer->toArray($callback);

            if (\array_key_exists('$ref', $callback)) {
                $serialized[] = $callback;

                continue;
            }

            $name = $callback['path'] ?? $callback['route'] ?? null;
            unset($callback['path'], $callback['route']);

            if ($name !== null) {
                $serialized[$name] = $callback;
            }
        }

        return $serialized;
    }

    protected function isDeprecated(?DocBlock $actionDocBlock): ?bool
    {
        if ($actionDocBlock === null) {
            return null;
        }

        $deprecatedTag = $actionDocBlock->getTagsByName('deprecated');

        if (\count($deprecatedTag) > 0) {
            return true;
        }

        return null;
    }
}
