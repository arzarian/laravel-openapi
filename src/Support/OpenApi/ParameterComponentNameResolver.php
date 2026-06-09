<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use Illuminate\Support\Str;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class ParameterComponentNameResolver
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer = new SpecificationObjectSerializer(),
    ) {
    }

    public function forParameterFactory(ParameterFactory $factory, mixed $parameter): string
    {
        return $this->explicitId($parameter)
            ?? $this->normalize($this->fallbackName($parameter) ?? \class_basename($factory));
    }

    public function forParametersFactory(ParametersFactory $factory, mixed $parameter): string
    {
        if ($this->isReference($parameter)) {
            return $this->referenceComponentName($parameter) ?? $this->normalize(\class_basename($factory) . 'Parameter');
        }

        $explicitId = $this->explicitId($parameter);

        if ($explicitId !== null) {
            return $explicitId;
        }

        $factoryName = $this->normalize(\class_basename($factory));
        $parameterName = $this->normalize($this->fallbackName($parameter) ?? $this->referenceName($parameter) ?? 'Parameter');

        return $factoryName . $parameterName;
    }

    public function normalize(string $value): string
    {
        $value = (string)Str::of($value)
            ->replaceMatches('/[^A-Za-z0-9]+/', ' ')
            ->trim();

        return Str::studly($value);
    }

    public function explicitId(mixed $parameter): ?string
    {
        return $this->stringProperty($parameter, 'parameter')
            ?? $this->stringProperty($parameter, 'objectId');
    }

    public function fallbackName(mixed $parameter): ?string
    {
        return $this->stringProperty($parameter, 'name');
    }

    public function isReference(mixed $parameter): bool
    {
        return $this->referenceTarget($parameter) !== null;
    }

    public function isDirectReference(mixed $parameter): bool
    {
        return $this->isReference($parameter)
            && $this->explicitId($parameter) === null
            && $this->fallbackName($parameter) === null;
    }

    public function referenceTarget(mixed $parameter): ?string
    {
        return $this->stringProperty($parameter, 'ref')
            ?? $this->stringProperty($parameter, '$ref');
    }

    public function referenceAlias(mixed $parameter): ?string
    {
        if (!$this->isReference($parameter)) {
            return null;
        }

        return $this->explicitId($parameter);
    }

    public function referenceComponentName(mixed $parameter): ?string
    {
        $target = $this->referenceTarget($parameter);

        if ($target === null || $target === '') {
            return null;
        }

        return \basename($target);
    }

    protected function referenceName(mixed $parameter): ?string
    {
        return $this->referenceComponentName($parameter);
    }

    protected function stringProperty(mixed $parameter, string $property): ?string
    {
        $value = $this->serializer->property($parameter, $property);

        if (!\is_string($value) || $value === '') {
            return null;
        }

        return $value;
    }
}
