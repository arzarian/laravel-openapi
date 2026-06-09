<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Illuminate\Support\Facades\App;
use OpenApi\Annotations\Response;
use Vyuldashev\LaravelOpenApi\Attributes\Response as ResponseAttribute;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ResponsesBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer,
        protected FactoryClassResolver $factoryClassResolver,
    ) {
    }

    /**
     * @return array<int, Response>
     * @param RouteInformation $route
     */
    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn(object $attribute) => $attribute instanceof ResponseAttribute)
            ->map(function (ResponseAttribute $attribute) {
                /** @var ResponseFactory $factory */
                $factory = App::make($this->factoryClassResolver->response($attribute->factory));
                $response = $factory->build();

                if ($factory instanceof Reusable) {
                    $name = $this->serializer->componentName($response, 'response');

                    return new Response($this->serializer->properties([
                        'ref' => '#/components/responses/' . $name,
                        'response' => $attribute->statusCode,
                        'description' => $attribute->description,
                    ]));
                }

                $response = $this->serializer->properties(
                    \array_merge($this->serializer->toArray($response), [
                        'response' => $attribute->statusCode,
                    ]),
                );

                return new Response($response);
            })
            ->values()
            ->all();
    }
}
