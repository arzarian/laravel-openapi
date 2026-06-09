<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class UserIndexParameters extends ParametersFactory implements Reusable
{
    /**
     * @return array<int, Parameter>
     */
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('page')
                ->description('User page parameter')
                ->schema(Schema::integer()),
        ];
    }
}
