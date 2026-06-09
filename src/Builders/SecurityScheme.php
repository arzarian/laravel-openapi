<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\SecurityScheme as SwaggerSecurityScheme;

class SecurityScheme extends SpecificationBuilder
{
    public const string TYPE_API_KEY = 'apiKey';
    public const string TYPE_HTTP = 'http';
    public const string TYPE_OAUTH2 = 'oauth2';
    public const string TYPE_OPEN_ID_CONNECT = 'openIdConnect';

    public const string IN_QUERY = 'query';
    public const string IN_HEADER = 'header';
    public const string IN_COOKIE = 'cookie';

    public static function oauth2(?string $objectId = null): static
    {
        return static::create($objectId)->type(static::TYPE_OAUTH2);
    }

    public function type(?string $type): static
    {
        return $this->set('type', $type);
    }

    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function name(?string $name): static
    {
        return $this->set('name', $name);
    }

    public function in(?string $in): static
    {
        return $this->set('in', $in);
    }

    public function scheme(?string $scheme): static
    {
        return $this->set('scheme', $scheme);
    }

    public function bearerFormat(?string $bearerFormat): static
    {
        return $this->set('bearerFormat', $bearerFormat);
    }

    public function flows(mixed ...$flows): static
    {
        return $this->set('flows', $flows ?: null);
    }

    public function openIdConnectUrl(?string $openIdConnectUrl): static
    {
        return $this->set('openIdConnectUrl', $openIdConnectUrl);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function build(): array
    {
        return \array_merge(parent::build(), [
            'flows' => $this->keyedBy('flows', 'flow') ?: null,
        ]);
    }

    protected function identifierField(): ?string
    {
        return 'securityScheme';
    }

    protected function annotationClass(): string
    {
        return SwaggerSecurityScheme::class;
    }
}
