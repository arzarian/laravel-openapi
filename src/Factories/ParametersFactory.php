<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Builders\Parameter as ParameterBuilder;

abstract class ParametersFactory
{
    use Referencable;

    /**
     * @return array<ParameterBuilder|\OpenApi\Annotations\Parameter|array>
     */
    abstract public function build(): array;
}
