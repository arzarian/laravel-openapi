<?php

namespace Vyuldashev\LaravelOpenApi\Support\OpenApi;

use OpenApi\Annotations\OpenApi as SwaggerOpenApi;

class OpenApiFactory
{
    public function __construct(
        protected SpecificationObjectSerializer $serializer,
        protected SchemaNormalizer $schemaNormalizer
    ) {
    }

    public function create(
        SpecVersion $version,
        mixed $info,
        array $servers,
        array $tags,
        array $paths,
        mixed $components,
        array $security,
        array $extensions
    ): SwaggerOpenApi {
        $properties = [
            'openapi' => $version->value,
            'info' => $this->serializer->toArray($info, $version),
            'paths' => $this->pathsToArray($paths, $version),
            'x' => $this->extensionsToArray($extensions),
        ];

        if ($servers !== []) {
            $properties['servers'] = $this->serializer->toArray($servers, $version);
        }

        if ($components !== null) {
            $properties['components'] = $this->serializer->toArray($components, $version);
        }

        if ($security !== []) {
            $properties['security'] = $this->serializer->toArray($security, $version);
        }

        if ($tags !== []) {
            $properties['tags'] = $this->serializer->toArray($tags, $version);
        }

        return new SwaggerOpenApi($this->schemaNormalizer->normalize($properties, $version));
    }

    protected function pathsToArray(array $paths, SpecVersion $version): array
    {
        $serialized = [];

        foreach ($paths as $path) {
            $path = $this->serializer->toArray($path, $version);
            $route = $path['path'] ?? $path['route'] ?? null;
            unset($path['path'], $path['route']);

            $serialized[$route] = $path;
        }

        return $serialized;
    }

    protected function extensionsToArray(array $extensions): array
    {
        $serialized = [];

        foreach ($extensions as $key => $value) {
            $serialized[preg_replace('/^x-/', '', (string) $key)] = $value;
        }

        return $serialized;
    }
}
