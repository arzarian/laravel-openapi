<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Response as ResponseBuilder;

abstract class ResponseFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    /**
     * @return ResponseBuilder|\OpenApi\Annotations\Response|array<string, mixed>
     */
    abstract public function build();

    public static function ref(?string $objectId = null): ResponseBuilder
    {
        $ref = static::makeRef($objectId);

        if (!$ref instanceof ResponseBuilder) {
            throw new \UnexpectedValueException('Response factory refs must resolve to a response builder.');
        }

        return $ref;
    }
}
