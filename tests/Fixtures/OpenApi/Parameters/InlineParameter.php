<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;

class InlineParameter extends ParameterFactory
{
    public function build(): Parameter
    {
        return Parameter::query()
            ->name('single')
            ->description('Single inline parameter')
            ->schema(Schema::string());
    }
}
