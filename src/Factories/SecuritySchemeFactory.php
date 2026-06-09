<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme as SecuritySchemeBuilder;

abstract class SecuritySchemeFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    abstract public function build(): SecuritySchemeBuilder;

    public static function ref(?string $objectId = null): SecuritySchemeBuilder
    {
        $ref = static::makeRef($objectId);

        if (!$ref instanceof SecuritySchemeBuilder) {
            throw new \UnexpectedValueException('Security scheme factory refs must resolve to a security scheme builder.');
        }

        return $ref;
    }
}
