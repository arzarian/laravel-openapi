<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Server as ServerBuilder;

abstract class ServerFactory
{
    abstract public function build(): ServerBuilder;
}
