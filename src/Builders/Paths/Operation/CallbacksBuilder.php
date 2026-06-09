<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Vyuldashev\LaravelOpenApi\Attributes\Callback as CallbackAttribute;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class CallbacksBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer
    ) {
    }

    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn (object $attribute) => $attribute instanceof CallbackAttribute)
            ->map(static function (CallbackAttribute $attribute) {
                $factory = app($attribute->factory);
                $pathItem = $factory->build();

                if ($factory instanceof Reusable) {
                    return ['$ref' => '#/components/callbacks/'.$pathItem->name];
                }

                return $this->serializer->toArray($pathItem);
            })
            ->values()
            ->toArray();
    }
}
