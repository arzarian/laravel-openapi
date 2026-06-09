<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Illuminate\Support\Facades\App;
use OpenApi\Annotations\Parameter as SwaggerParameter;
use Vyuldashev\LaravelOpenApi\Builders\Parameter as ParameterBuilder;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Generator;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\ParameterComponentNameResolver;

class ParametersBuilder extends Builder
{
    protected ParameterComponentNameResolver $nameResolver;

    /**
     * @param array<int, string> $directories
     */
    public function __construct(array $directories)
    {
        parent::__construct($directories);

        $this->nameResolver = new ParameterComponentNameResolver();
    }

    /**
     * @return array<int, mixed>
     * @param string $collection
     */
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        $classes = $this->getAllClasses($collection)
            ->filter(static fn($class) =>
                    (
                        \is_a($class, ParameterFactory::class, true)
                        || \is_a($class, ParametersFactory::class, true)
                    )
                    && \is_a($class, Reusable::class, true));

        $parameterAliases = $this->parameterAliases($classes);

        $parameterComponents = $classes
            ->filter(static fn($class) => \is_a($class, ParameterFactory::class, true))
            ->mapWithKeys(function ($class) use ($parameterAliases) {
                $instance = App::make($class);

                if (!$instance instanceof ParameterFactory) {
                    return [];
                }

                $parameter = $instance->build();
                $name = $this->nameResolver->forParameterFactory($instance, $parameter);

                return [$name => $this->componentParameter(
                    $parameter,
                    $name,
                    $parameterAliases[$name] ?? null,
                )];
            });

        $listComponents = $classes
            ->filter(static fn($class) => \is_a($class, ParametersFactory::class, true))
            ->flatMap(function ($class) use ($parameterComponents) {
                $instance = App::make($class);

                if (!$instance instanceof ParametersFactory) {
                    return [];
                }

                return collect($instance->build())
                    ->reject(fn(mixed $parameter): bool => $this->nameResolver->isReference($parameter))
                    ->map(fn(mixed $parameter): mixed => $this->componentParameter(
                        $this->referencedComponent($parameter, $parameterComponents->all()) ?? $parameter,
                        $this->nameResolver->forParametersFactory($instance, $parameter),
                    ))
                    ->values()
                    ->all();
            })
            ->values();

        return $parameterComponents
            ->values()
            ->merge($listComponents)
            ->values()
            ->all();
    }

    /**
     * @param iterable<int, class-string> $classes
     * @return array<string, string>
     */
    protected function parameterAliases(iterable $classes): array
    {
        $aliases = [];

        collect($classes)
            ->filter(static fn($class) => \is_a($class, ParametersFactory::class, true))
            ->each(function ($class) use (&$aliases): void {
                $instance = App::make($class);

                if (!$instance instanceof ParametersFactory) {
                    return;
                }

                collect($instance->build())
                    ->filter(fn(mixed $parameter): bool => $this->nameResolver->isReference($parameter))
                    ->each(function (mixed $parameter) use (&$aliases): void {
                        $componentName = $this->nameResolver->referenceComponentName($parameter);
                        $alias = $this->nameResolver->referenceAlias($parameter);

                        if ($componentName === null || $alias === null) {
                            return;
                        }

                        if (isset($aliases[$componentName]) && $aliases[$componentName] !== $alias) {
                            throw new \InvalidArgumentException(\sprintf(
                                'Parameter component [%s] has conflicting aliases [%s] and [%s].',
                                $componentName,
                                $aliases[$componentName],
                                $alias,
                            ));
                        }

                        $aliases[$componentName] = $alias;
                    });
            });

        return $aliases;
    }

    protected function componentParameter(mixed $parameter, string $name, ?string $parameterName = null): mixed
    {
        if ($parameter instanceof ParameterBuilder) {
            $parameter = $parameter->objectId($name);

            if ($parameterName !== null || $parameter->name === null) {
                $parameter = $parameter->name($parameterName ?? $name);
            }

            return $parameter;
        }

        if (\is_array($parameter)) {
            $properties = ['parameter' => $name] + $parameter;
            unset($properties['objectId']);

            if (isset($properties['$ref']) && !isset($properties['ref'])) {
                $properties['ref'] = $properties['$ref'];
                unset($properties['$ref']);
            }

            if ($parameterName !== null || !isset($properties['name'])) {
                $properties['name'] = $parameterName ?? $name;
            }

            return new SwaggerParameter($properties);
        }

        if ($parameter instanceof SwaggerParameter) {
            $parameter = clone $parameter;
            $parameter->parameter = $name;

            if ($parameterName !== null || \OpenApi\Generator::isDefault($parameter->name)) {
                $parameter->name = $parameterName ?? $name;
            }

            return $parameter;
        }

        return $parameter;
    }

    /**
     * @param array<string, mixed> $parameterComponents
     * @param mixed $parameter
     */
    protected function referencedComponent(mixed $parameter, array $parameterComponents): mixed
    {
        $name = $this->nameResolver->referenceComponentName($parameter);

        if ($name === null) {
            return null;
        }

        return $parameterComponents[$name] ?? null;
    }
}
