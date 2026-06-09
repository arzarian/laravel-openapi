<?php

namespace Vyuldashev\LaravelOpenApi\Builders\Paths\Operation;

use Vyuldashev\LaravelOpenApi\Attributes\Operation as OperationAttribute;
use Vyuldashev\LaravelOpenApi\RouteInformation;

class SecurityBuilder
{
    public function build(RouteInformation $route): array
    {
        return $route->actionAttributes
            ->filter(static fn (object $attribute) => $attribute instanceof OperationAttribute)
            ->filter(static fn (OperationAttribute $attribute) => isset($attribute->security))
            ->map(static function (OperationAttribute $attribute) {
                // return a null scheme if the security is set to ''
                if ($attribute->security === '') {
                    return [];
                }
                $security = app($attribute->security);
                $scheme = $security->build();

                return [$scheme->securityScheme => []];
            })
            ->values()
            ->toArray();
    }
}
