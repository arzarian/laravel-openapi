<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Collection
{
    public function __construct(
        /** @var string|array<string> */
        public array|string $name = 'default',
    ) {
    }
}
