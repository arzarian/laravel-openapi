<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Parameter as ParameterBuilder;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;

abstract class ParameterFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    abstract public function build(): ParameterBuilder;

    public static function ref(?string $objectId = null): ParameterBuilder
    {
        $ref = static::makeRef($objectId);

        if (!$ref instanceof ParameterBuilder) {
            throw new \UnexpectedValueException('Parameter factory refs must resolve to a parameter builder.');
        }

        return $ref;
    }
}
