<?php

namespace Examples\Petstore\OpenApi\Parameters;

use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class ListPetsParameters extends ParametersFactory
{
    /**
     * @return Parameter[]
     */
    public function build(): array
    {
        return [

            new Parameter([
                'name' => 'limit',
                'in' => 'query',
                'description' => 'How many items to return at one time (max 100)',
                'required' => false,
                'schema' => new Schema([
                    'format' => 'int32',
                    'type' => 'integer',
                ]),
            ]),

        ];
    }
}
