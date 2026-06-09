<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\SecurityScheme as SecuritySchemeBuilder;

abstract class SecuritySchemeFactory
{
    use Referencable;

    /**
     * @return SecuritySchemeBuilder|\OpenApi\Annotations\SecurityScheme|array
     */
    abstract public function build();
}
