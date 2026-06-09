<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures;

use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\InlineParameter;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\InlineParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\ReusableMethodParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\SlugParameter;

#[OpenApi\PathItem]
class ReusableParametersController
{
    #[OpenApi\Operation('singleReusableParameter')]
    #[OpenApi\Parameters(SlugParameter::class)]
    public function singleReusableParameter(): void
    {
    }

    #[OpenApi\Operation('listReusableParameters')]
    #[OpenApi\Parameters(ReusableMethodParameters::class)]
    public function listReusableParameters(): void
    {
    }

    #[OpenApi\Operation('inlineParameters')]
    #[OpenApi\Parameters(InlineParameters::class)]
    public function inlineParameters(): void
    {
    }

    #[OpenApi\Operation('inlineParameter')]
    #[OpenApi\Parameters(InlineParameter::class)]
    public function inlineParameter(): void
    {
    }
}
