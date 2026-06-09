<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use Illuminate\Support\Arr;
use OpenApi\Annotations\ExternalDocumentation;
use OpenApi\Annotations\Tag;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class TagsBuilder
{
    protected SpecificationObjectSerializer $serializer;
    public function __construct(
        ?SpecificationObjectSerializer $serializer = null,
    ) {
        $this->serializer = $serializer ?? new SpecificationObjectSerializer();
    }

    /**
     * @param array<int, array<string, mixed>> $config
     * @return array<int, Tag>
     */
    public function build(array $config): array
    {
        return collect($config)
            ->map(static function (array $tag): Tag {
                $externalDocs = null;

                if (Arr::has($tag, 'externalDocs')) {
                    $externalDocs = new ExternalDocumentation(\array_filter([
                        'description' => Arr::get($tag, 'externalDocs.description'),
                        'url' => Arr::get($tag, 'externalDocs.url'),
                    ], static fn(mixed $value): bool => $value !== null && $value !== []));
                }

                return new Tag(\array_filter([
                    'name' => $tag['name'],
                    'description' => Arr::get($tag, 'description'),
                    'externalDocs' => $externalDocs,
                ], static fn(mixed $value): bool => $value !== null && $value !== []));
            })
            ->values()
            ->all();
    }
}
