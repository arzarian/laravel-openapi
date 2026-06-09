<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Callback as CallbackBuilder;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\CallbackDefinition;

abstract class CallbackFactory
{
    use Referencable {
        ref as protected makeRef;
    }

    /**
     * @return CallbackBuilder|CallbackDefinition|array<string, mixed>
     */
    abstract public function build(): array|CallbackDefinition|CallbackBuilder;

    /**
     * @param string|null $objectId
     * @return array<string, string>
     */
    public static function ref(?string $objectId = null): array
    {
        $ref = static::makeRef($objectId);

        if (!\is_array($ref)) {
            throw new \UnexpectedValueException('Callback factory refs must resolve to an array reference.');
        }

        return $ref;
    }
}
