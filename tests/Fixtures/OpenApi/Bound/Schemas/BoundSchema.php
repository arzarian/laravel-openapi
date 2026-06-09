<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Bound\Schemas;

use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class BoundSchema extends SchemaFactory implements Reusable
{
    public function __construct(
        private readonly string $schemaName,
    ) {
    }

    public function build(): Schema
    {
        return Schema::string($this->schemaName);
    }
}
