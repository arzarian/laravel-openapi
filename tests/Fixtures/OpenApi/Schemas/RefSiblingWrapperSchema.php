<?php

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Schemas;

use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class RefSiblingWrapperSchema extends SchemaFactory implements Reusable
{
    public function build()
    {
        return Schema::object('RefSiblingWrapper')->properties(
            RefSiblingItemSchema::ref('refItem')
                ->deprecated()
                ->nullable()
                ->description('Description'),
        );
    }
}
