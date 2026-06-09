<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;

class SlugParameter extends ParameterFactory implements Reusable
{
    public function build(): Parameter
    {
        return Parameter::path('Slug')
            ->name('slug')
            ->required()
            ->description('Slug')
            ->schema(Schema::string()->example('slug'));
    }
}
