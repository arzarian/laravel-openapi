<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use OpenApi\Annotations\RequestBody as SwaggerRequestBody;

/**
 * @property-read ?string $request
 * @property-read ?string $description
 * @property-read list<MediaType> $content
 * @property-read ?bool $required
 */
class RequestBody extends SpecificationBuilder
{
    public function description(?string $description): static
    {
        return $this->set('description', $description);
    }

    public function content(MediaType ...$content): static
    {
        return $this->set('content', $content ?: null);
    }

    public function required(?bool $required = true): static
    {
        return $this->set('required', $required);
    }

    /**
     * @return array<string, mixed>
     */
    #[\Override]
    protected function build(): array
    {
        return \array_merge(parent::build(), [
            'content' => $this->keyedBy('content', 'mediaType') ?: null,
        ]);
    }

    protected function identifierField(): ?string
    {
        return 'request';
    }

    protected function annotationClass(): string
    {
        return SwaggerRequestBody::class;
    }
}
