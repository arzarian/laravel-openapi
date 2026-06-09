<?php

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use InvalidArgumentException;
use OpenApi\Annotations\OpenApi as SwaggerOpenApi;

final class SpecVersion
{
    public const DEFAULT = SwaggerOpenApi::VERSION_3_0_0;

    public function __construct(
        public readonly string $value
    ) {
        self::assertSupported($value);
    }

    public static function fromConfig(mixed $value): self
    {
        if ($value === null || $value === '') {
            return new self(self::DEFAULT);
        }

        return new self((string) $value);
    }

    public static function assertSupported(string $value): void
    {
        if (! preg_match('/^3\.(0|1)\.\d+$/', $value)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported OpenAPI version [%s]. Supported versions: 3.0.x, 3.1.x.',
                $value
            ));
        }

        if (! in_array($value, SwaggerOpenApi::SUPPORTED_VERSIONS, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unsupported OpenAPI version [%s]. Supported versions: %s.',
                $value,
                implode(', ', self::supported())
            ));
        }
    }

    public static function supported(): array
    {
        return array_values(array_filter(
            SwaggerOpenApi::SUPPORTED_VERSIONS,
            static fn (string $version): bool => str_starts_with($version, '3.0.')
                || str_starts_with($version, '3.1.')
        ));
    }

    public function isOpenApi31(): bool
    {
        return str_starts_with($this->value, '3.1.');
    }
}
