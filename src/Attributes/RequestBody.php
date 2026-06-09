<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class RequestBody
{
    public function __construct(
        public string $factory,
    ) {
    }
}
