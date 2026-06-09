<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use OpenApi\Annotations\SecurityScheme;

abstract class SecuritySchemeFactory
{
    abstract public function build(): SecurityScheme;
}
