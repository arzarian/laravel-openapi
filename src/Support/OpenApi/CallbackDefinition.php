<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use OpenApi\Annotations\PathItem;

readonly class CallbackDefinition implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $expression,
        /** @var PathItem|array<string, mixed> */
        public PathItem|array $pathItem,
    ) {
    }

    /**
     * @return array<string, PathItem|array<string, mixed>>
     */
    public function jsonSerialize(): array
    {
        return [
            $this->expression => $this->pathItem,
        ];
    }
}
