<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Response
{
    public function __construct(
        public string $factory,
        public ?int $statusCode = null,
        public ?string $description = null,
    ) {
    }
}
