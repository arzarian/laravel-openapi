<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class ArrayObjectIdParameters extends ParametersFactory implements Reusable
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function build(): array
    {
        return [
            [
                'objectId' => 'array_object_id',
                'name' => 'array_object_id',
                'in' => 'query',
                'description' => 'Array object id parameter',
                'schema' => [
                    'type' => 'string',
                ],
            ],
        ];
    }
}
