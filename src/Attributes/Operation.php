<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Operation
{
    public ?string $security;

    /**
     * @param string|null $id
     * @param array<int, string> $tags
     * @param string|null $security
     * @param string|null $method
     * @param array<int, class-string>|null $servers
     */
    public function __construct(
        public ?string $id = null,
        public array $tags = [],
        ?string $security = null,
        public ?string $method = null,
        public ?array $servers = null,
    ) {
        if ($security === '') {
            //user wants to turn off security on this operation
            $this->security = $security;

            return;
        }

        if ($security) {
            $this->security = $security;
        }
    }
}
