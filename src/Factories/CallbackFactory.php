<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Callback as CallbackBuilder;
use Vyuldashev\LaravelOpenApi\Concerns\Referencable;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\CallbackDefinition;

abstract class CallbackFactory
{
    use Referencable;

    /**
     * @return CallbackBuilder|CallbackDefinition|array
     */
    abstract public function build();
}
