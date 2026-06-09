<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;

class UserIdParameter extends ParameterFactory implements Reusable
{
    public function build(): Parameter
    {
        return Parameter::query()
            ->name('user_id')
            ->description('User id parameter')
            ->schema(Schema::string());
    }
}
