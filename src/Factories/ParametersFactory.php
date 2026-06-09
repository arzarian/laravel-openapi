<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Parameter as ParameterBuilder;

abstract class ParametersFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    /**
     * @return array<int, ParameterBuilder>
     */
    abstract public function build(): array;

    /**
     * Reference the first parameter built by this factory for backward compatibility.
     * @param ?string $objectId
     * @return ParameterBuilder
     */
    public static function ref(?string $objectId = null): ParameterBuilder
    {
        $ref = static::makeRef($objectId);

        if (!$ref instanceof ParameterBuilder) {
            throw new \UnexpectedValueException('Parameters factory refs must resolve to a parameter builder.');
        }

        return $ref;
    }
}
