<?php

namespace Examples\Petstore\OpenApi\Responses;

use OpenApi\Annotations\AdditionalProperties;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\MediaType;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Response;
use OpenApi\Annotations\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorValidationResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        $response = new Schema([
            'type' => 'object',
            'properties' => [
                new Property([
                    'property' => 'message',
                    'type' => 'string',
                    'example' => 'The given data was invalid.',
                ]),
                new Property([
                    'property' => 'errors',
                    'type' => 'object',
                    'additionalProperties' => new AdditionalProperties([
                        'type' => 'array',
                        'items' => new Items([
                            'type' => 'string',
                        ]),
                    ]),
                    'example' => ['field' => ['Something is wrong with this field!']],
                ]),
            ],
        ]);

        return new Response([
            'response' => 'ErrorValidation',
            'description' => 'Validation errors',
            'content' => [
                new MediaType([
                    'mediaType' => 'application/json',
                    'schema' => $response,
                ]),
            ],
        ]);
    }
}
