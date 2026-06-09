<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use Vyuldashev\LaravelOpenApi\Builders\PathItem;

readonly class CallbackDefinition implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $expression,
        public PathItem $pathItem,
    ) {
    }

    /**
     * @return array<string, PathItem>
     */
    public function jsonSerialize(): array
    {
        return [
            $this->expression => $this->pathItem,
        ];
    }
}
