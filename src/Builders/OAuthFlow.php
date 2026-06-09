<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Flow;

class OAuthFlow extends SpecificationBuilder
{
    public const string FLOW_IMPLICIT = 'implicit';
    public const string FLOW_PASSWORD = 'password';
    public const string FLOW_CLIENT_CREDENTIALS = 'clientCredentials';
    public const string FLOW_AUTHORIZATION_CODE = 'authorizationCode';

    public function flow(?string $flow): static
    {
        return $this->withObjectId($flow);
    }

    public function authorizationUrl(?string $authorizationUrl): static
    {
        return $this->set('authorizationUrl', $authorizationUrl);
    }

    public function tokenUrl(?string $tokenUrl): static
    {
        return $this->set('tokenUrl', $tokenUrl);
    }

    public function refreshUrl(?string $refreshUrl): static
    {
        return $this->set('refreshUrl', $refreshUrl);
    }

    /**
     * @param array<string, string> $scopes
     */
    public function scopes(array $scopes): static
    {
        return $this->set('scopes', $scopes);
    }

    protected function identifierField(): ?string
    {
        return 'flow';
    }

    protected function annotationClass(): string
    {
        return Flow::class;
    }
}
