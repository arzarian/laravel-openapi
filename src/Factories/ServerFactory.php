<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Server as ServerBuilder;

abstract class ServerFactory
{
    /**
     * @return ServerBuilder|\OpenApi\Annotations\Server|array
     */
    abstract public function build();
}
