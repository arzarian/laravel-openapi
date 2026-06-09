<?php

namespace Vyuldashev\LaravelOpenApi\Factories;

use OpenApi\Annotations\Server;

abstract class ServerFactory
{
    abstract public function build(): Server;
}
