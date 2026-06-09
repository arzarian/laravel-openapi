<?php

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use JsonSerializable;
use OpenApi\Annotations\PathItem;

class CallbackDefinition implements JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $expression,
        public readonly PathItem|array $pathItem
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            $this->expression => $this->pathItem,
        ];
    }
}
