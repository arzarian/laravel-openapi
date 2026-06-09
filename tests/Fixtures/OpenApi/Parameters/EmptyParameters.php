<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters;

use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class EmptyParameters extends ParametersFactory implements Reusable
{
    /**
     * @return array<int, never>
     */
    public function build(): array
    {
        return [];
    }
}
