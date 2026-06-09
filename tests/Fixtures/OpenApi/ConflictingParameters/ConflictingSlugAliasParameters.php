<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\ConflictingParameters;

use Vyuldashev\LaravelOpenApi\Builders\Parameter;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\SlugParameterWithoutName;

class ConflictingSlugAliasParameters extends ParametersFactory implements Reusable
{
    /**
     * @return array<int, Parameter>
     */
    public function build(): array
    {
        return [
            SlugParameterWithoutName::ref('conflicting_slug'),
        ];
    }
}
