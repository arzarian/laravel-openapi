<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use OpenApi\Annotations\Response;
use Vyuldashev\LaravelOpenApi\Attributes\Response as ResponseAttribute;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ResponsesBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer
    ) {
    }

    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn (object $attribute) => $attribute instanceof ResponseAttribute)
            ->map(function (ResponseAttribute $attribute) {
                $factory = app($attribute->factory);
                $response = $factory->build();

                if ($factory instanceof Reusable) {
                    $name = $this->serializer->componentName($response, 'response');

                    return new Response($this->serializer->properties([
                        'ref' => '#/components/responses/'.$name,
                        'response' => $attribute->statusCode,
                        'description' => $attribute->description,
                    ]));
                }

                $response = $this->serializer->properties(
                    array_merge($this->serializer->toArray($response), [
                        'response' => $attribute->statusCode,
                    ])
                );

                return new Response($response);
            })
            ->values()
            ->toArray();
    }
}
