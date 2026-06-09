<?php

namespace Examples\Petstore\OpenApi31\Schemas;

use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class PetStatusSchema extends SchemaFactory implements Reusable
{
    public function build(): Schema
    {
        return new Schema([
            'schema' => 'PetStatus',
            'type' => 'object',
            'properties' => [
                new Property([
                    'property' => 'kind',
                    'type' => 'string',
                    'const' => 'pet',
                ]),
                new Property([
                    'property' => 'nickname',
                    'type' => ['string', 'null'],
                ]),
            ],
        ]);
    }
}
