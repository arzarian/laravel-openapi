<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\Flow;

/**
 * @property-read ?string $flow
 * @property-read ?string $authorizationUrl
 * @property-read ?string $tokenUrl
 * @property-read ?string $refreshUrl
 * @property-read array<string, string> $scopes
 */
class OAuthFlow extends SpecificationBuilder
{
    public const FLOW_IMPLICIT = 'implicit';
    public const FLOW_PASSWORD = 'password';
    public const FLOW_CLIENT_CREDENTIALS = 'clientCredentials';
    public const FLOW_AUTHORIZATION_CODE = 'authorizationCode';

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
