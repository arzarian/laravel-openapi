<?php

namespace Vyuldashev\LaravelOpenApi\Http;

use OpenApi\Annotations\OpenApi;
use Vyuldashev\LaravelOpenApi\Generator;

class OpenApiController
{
    public function show(Generator $generator): OpenApi
    {
        return $generator->generate();
    }
}
