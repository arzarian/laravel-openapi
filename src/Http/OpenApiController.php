<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Http;

use OpenApi\Annotations\OpenApi;
use Vyuldashev\LaravelOpenApi\Generator;

class OpenApiController
{
    public function show(Generator $generator, string $collection = Generator::COLLECTION_DEFAULT): OpenApi
    {
        return $generator->generate($collection);
    }
}
