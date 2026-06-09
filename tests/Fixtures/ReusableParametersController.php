<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Fixtures;

use Vyuldashev\LaravelOpenApi\Attributes as OpenApi;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\ArrayObjectIdParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\DirectReferenceParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\ExplicitIdParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\InlineParameter;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\InlineParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\KebabCaseParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\OrderIndexParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\ParamSlugMethodParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\ReusableMethodParameters;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\SlugParameter;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\UserIdParameter;
use Vyuldashev\LaravelOpenApi\Tests\Fixtures\OpenApi\Parameters\UserIndexParameters;

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

    #[OpenApi\Operation('userIndexParameters')]
    #[OpenApi\Parameters(UserIndexParameters::class)]
    public function userIndexParameters(): void
    {
    }

    #[OpenApi\Operation('orderIndexParameters')]
    #[OpenApi\Parameters(OrderIndexParameters::class)]
    public function orderIndexParameters(): void
    {
    }

    #[OpenApi\Operation('kebabCaseParameters')]
    #[OpenApi\Parameters(KebabCaseParameters::class)]
    public function kebabCaseParameters(): void
    {
    }

    #[OpenApi\Operation('explicitIdParameters')]
    #[OpenApi\Parameters(ExplicitIdParameters::class)]
    public function explicitIdParameters(): void
    {
    }

    #[OpenApi\Operation('singleGeneratedNameParameter')]
    #[OpenApi\Parameters(UserIdParameter::class)]
    public function singleGeneratedNameParameter(): void
    {
    }

    #[OpenApi\Operation('directReferenceParameters')]
    #[OpenApi\Parameters(DirectReferenceParameters::class)]
    public function directReferenceParameters(): void
    {
    }

    #[OpenApi\Operation('paramSlugMethodParameters')]
    #[OpenApi\Parameters(ParamSlugMethodParameters::class)]
    public function paramSlugMethodParameters(): void
    {
    }

    #[OpenApi\Operation('arrayObjectIdParameters')]
    #[OpenApi\Parameters(ArrayObjectIdParameters::class)]
    public function arrayObjectIdParameters(): void
    {
    }
}
