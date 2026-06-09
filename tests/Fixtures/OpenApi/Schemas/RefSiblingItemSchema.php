<?php

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Schemas;

use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class RefSiblingItemSchema extends SchemaFactory implements Reusable
{
    public function build()
    {
        return Schema::object('RefSiblingItem')->properties(
            Schema::string('name'),
        );
    }
}
