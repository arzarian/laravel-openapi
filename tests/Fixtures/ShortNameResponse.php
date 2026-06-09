<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures;

use Vyuldashev\LaravelOpenApi\Builders\Response;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ShortNameResponse extends ResponseFactory
{
    public function build(): Response
    {
        return Response::ok('ShortName');
    }
}
