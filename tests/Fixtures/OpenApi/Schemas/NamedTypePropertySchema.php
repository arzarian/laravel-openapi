<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Schemas;

use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class NamedTypePropertySchema extends SchemaFactory implements Reusable
{
    public function build(): Schema
    {
        return Schema::object('NamedTypeProperty')
            ->required(
                'type',
                'title',
            )
            ->properties(
                Schema::string('type')
                    ->description('Тип')
                    ->enum('type1', 'type2', 'type3'),
                Schema::string('title')
                    ->description('Заголовок')
                    ->example('Заголовок'),
            );
    }
}
