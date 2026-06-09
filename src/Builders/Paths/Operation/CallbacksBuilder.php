<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Attributes\Callback as CallbackAttribute;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\CallbackFactory;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class CallbacksBuilder
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer,
        protected FactoryClassResolver $factoryClassResolver,
    ) {
    }

    /**
     * @param RouteInformation $route
     * @return array<int, mixed>
     */
    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn(object $attribute) => $attribute instanceof CallbackAttribute)
            ->map(function (CallbackAttribute $attribute) {
                /** @var CallbackFactory $factory */
                $factory = App::make($this->factoryClassResolver->callback($attribute->factory));
                $pathItem = $factory->build();

                if ($factory instanceof Reusable) {
                    return ['$ref' => '#/components/callbacks/' . $this->serializer->componentName($pathItem, 'name')];
                }

                return $this->serializer->toArray($pathItem);
            })
            ->values()
            ->all();
    }
}
