<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Factories;

use Vyuldashev\LaravelOpenApi\Builders\Server as ServerBuilder;

abstract class ServerFactory
{
    /**
     * @return ServerBuilder|\OpenApi\Annotations\Server|array<string, mixed>
     */
    abstract public function build();
}
