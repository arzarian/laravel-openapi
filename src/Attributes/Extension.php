<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Attributes;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class Extension
{
    public function __construct(
        public ?string $factory = null,
        public ?string $key = null,
        public ?string $value = null,
    ) {
    }
}
