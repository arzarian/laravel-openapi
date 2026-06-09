<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\RequestBody as RequestBodyBuilder;

abstract class RequestBodyFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    /**
     * @return RequestBodyBuilder|\OpenApi\Annotations\RequestBody|array<string, mixed>
     */
    abstract public function build();

    public static function ref(?string $objectId = null): RequestBodyBuilder
    {
        $ref = static::makeRef($objectId);

        if (!$ref instanceof RequestBodyBuilder) {
            throw new \UnexpectedValueException('Request body factory refs must resolve to a request body builder.');
        }

        return $ref;
    }
}
