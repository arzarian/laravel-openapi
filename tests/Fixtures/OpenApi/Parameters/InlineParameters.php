<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Builders\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class InlineParameters extends ParametersFactory
{
    /**
     * @return array<int, Parameter>
     */
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('search')
                ->description('Inline search parameter')
                ->schema(Schema::string()),
        ];
    }
}
