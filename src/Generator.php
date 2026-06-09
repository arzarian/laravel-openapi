<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi;

use Illuminate\Support\Arr;
use OpenApi\Annotations\OpenApi;
use Vyuldashev\LaravelOpenApi\Builders\ComponentsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\InfoBuilder;
use Vyuldashev\LaravelOpenApi\Builders\PathsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ServersBuilder;
use Vyuldashev\LaravelOpenApi\Builders\TagsBuilder;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\OpenApiFactory;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecVersion;

class Generator
{
    public const string COLLECTION_DEFAULT = 'default';
    public string $version = SpecVersion::DEFAULT;

    /**
     * @param array<string, mixed> $config
     * @param InfoBuilder $infoBuilder
     * @param ServersBuilder $serversBuilder
     * @param TagsBuilder $tagsBuilder
     * @param PathsBuilder $pathsBuilder
     * @param ComponentsBuilder $componentsBuilder
     * @param OpenApiFactory $openApiFactory
     */
    public function __construct(
        protected array $config,
        protected InfoBuilder $infoBuilder,
        protected ServersBuilder $serversBuilder,
        protected TagsBuilder $tagsBuilder,
        protected PathsBuilder $pathsBuilder,
        protected ComponentsBuilder $componentsBuilder,
        protected OpenApiFactory $openApiFactory,
    ) {
    }

    public function generate(string $collection = self::COLLECTION_DEFAULT): OpenApi
    {
        $middlewares = Arr::get($this->config, 'collections.' . $collection . '.middlewares', []);
        $specVersion = SpecVersion::fromConfig(
            Arr::get(
                $this->config,
                'collections.' . $collection . '.openapi',
                Arr::get($this->config, 'collections.' . $collection . '.version'),
            ),
        );

        $info = $this->infoBuilder->build(Arr::get($this->config, 'collections.' . $collection . '.info', []));
        $servers = $this->serversBuilder->build(Arr::get($this->config, 'collections.' . $collection . '.servers', []));
        $tags = $this->tagsBuilder->build(Arr::get($this->config, 'collections.' . $collection . '.tags', []));
        $paths = $this->pathsBuilder->build($collection, Arr::get($middlewares, 'paths', []));
        $components = $this->componentsBuilder->build($collection, Arr::get($middlewares, 'components', []));
        $extensions = Arr::get($this->config, 'collections.' . $collection . '.extensions', []);

        return $this->openApiFactory->create(
            $specVersion,
            $info,
            $servers,
            $tags,
            $paths,
            $components,
            Arr::get($this->config, 'collections.' . $collection . '.security', []),
            $extensions,
        );
    }
}
