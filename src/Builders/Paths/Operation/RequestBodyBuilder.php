<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Illuminate\Support\Facades\App;
use OpenApi\Annotations\RequestBody;
use Vyuldashev\LaravelOpenApi\Attributes\RequestBody as RequestBodyAttribute;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class RequestBodyBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer,
        protected FactoryClassResolver $factoryClassResolver,
    ) {
    }

    public function build(RouteInformation $route): mixed
    {
        /** @var RequestBodyAttribute|null $requestBody */
        $requestBody = $route->actionAttributes->first(static fn(object $attribute) => $attribute instanceof RequestBodyAttribute);

        if ($requestBody) {
            /** @var RequestBodyFactory $requestBodyFactory */
            $requestBodyFactory = App::make($this->factoryClassResolver->requestBody($requestBody->factory));

            $requestBody = $requestBodyFactory->build();

            if ($requestBodyFactory instanceof Reusable) {
                $name = $this->serializer->componentName($requestBody, 'request');

                return new RequestBody([
                    'ref' => '#/components/requestBodies/' . $name,
                ]);
            }

            return $this->serializer->toArray($requestBody);
        }

        return $requestBody;
    }
}
