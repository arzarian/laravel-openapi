<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Attributes\Operation as OperationAttribute;
use Vyuldashev\LaravelOpenApi\RouteInformation;
use Vyuldashev\LaravelOpenApi\Support\FactoryClassResolver;

class SecurityBuilder
{
    public function __construct(
        protected FactoryClassResolver $factoryClassResolver,
    ) {
    }

    /**
     * @return array<int, array<string, array<int, string>>>
     * @param RouteInformation $route
     */
    public function build(RouteInformation $route): array
    {
        $factoryClassResolver = $this->factoryClassResolver;

        return $route->actionAttributes
            ->filter(static fn(object $attribute) => $attribute instanceof OperationAttribute)
            ->filter(static fn(OperationAttribute $attribute) => isset($attribute->security))
            ->map(static function (OperationAttribute $attribute) use ($factoryClassResolver): array {
                // return a null scheme if the security is set to ''
                if ($attribute->security === '') {
                    return [];
                }

                if ($attribute->security === null) {
                    return [];
                }

                $security = App::make($factoryClassResolver->securityScheme($attribute->security));
                $scheme = $security->build();

                return [$scheme->securityScheme => []];
            })
            ->values()
            ->all();
    }
}
