<?php

namespace Examples\Petstore\OpenApi31\Schemas;

use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class PetStatusSchema extends SchemaFactory implements Reusable
{
    public function build()
    {
        return Schema::object('PetStatus')->properties(
            Schema::string('kind')->const('pet'),
            Schema::create('nickname')->nullOr(Schema::TYPE_STRING),
        );
    }
}
