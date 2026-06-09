<?php

namespace Vyuldashev\LaravelOpenApi\Tests;

use OpenApi\Annotations\OpenApi;
use Vyuldashev\LaravelOpenApi\Generator;
use Vyuldashev\LaravelOpenApi\OpenApiServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            OpenApiServiceProvider::class,
        ];
    }

    protected function generate(): OpenApi
    {
        return $this->app[Generator::class]->generate();
    }

    protected function generateArray(): array
    {
        return json_decode($this->generate()->toJson(), true);
    }
}
