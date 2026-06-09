<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Support\OpenApi\CallbackDefinition;

abstract class CallbackFactory
{
    abstract public function build(): CallbackDefinition;
}
