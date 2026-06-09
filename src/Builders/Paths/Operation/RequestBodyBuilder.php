<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use OpenApi\Annotations\RequestBody;
use Vyuldashev\LaravelOpenApi\Attributes\RequestBody as RequestBodyAttribute;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class RequestBodyBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer
    ) {
    }

    public function build(RouteInformation $route): mixed
    {
        /** @var RequestBodyAttribute|null $requestBody */
        $requestBody = $route->actionAttributes->first(static fn (object $attribute) => $attribute instanceof RequestBodyAttribute);

        if ($requestBody) {
            /** @var RequestBodyFactory $requestBodyFactory */
            $requestBodyFactory = app($requestBody->factory);

            $requestBody = $requestBodyFactory->build();

            if ($requestBodyFactory instanceof Reusable) {
                $name = $this->serializer->componentName($requestBody, 'request');

                return new RequestBody([
                    'ref' => '#/components/requestBodies/'.$name,
                ]);
            }

            return $this->serializer->toArray($requestBody);
        }

        return $requestBody;
    }
}
