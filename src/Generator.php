<?php

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
    public string $version = SpecVersion::DEFAULT;

    public const COLLECTION_DEFAULT = 'default';

    protected array $config;
    protected InfoBuilder $infoBuilder;
    protected ServersBuilder $serversBuilder;
    protected TagsBuilder $tagsBuilder;
    protected PathsBuilder $pathsBuilder;
    protected ComponentsBuilder $componentsBuilder;
    protected OpenApiFactory $openApiFactory;

    public function __construct(
        array $config,
        InfoBuilder $infoBuilder,
        ServersBuilder $serversBuilder,
        TagsBuilder $tagsBuilder,
        PathsBuilder $pathsBuilder,
        ComponentsBuilder $componentsBuilder,
        OpenApiFactory $openApiFactory
    ) {
        $this->config = $config;
        $this->infoBuilder = $infoBuilder;
        $this->serversBuilder = $serversBuilder;
        $this->tagsBuilder = $tagsBuilder;
        $this->pathsBuilder = $pathsBuilder;
        $this->componentsBuilder = $componentsBuilder;
        $this->openApiFactory = $openApiFactory;
    }

    public function generate(string $collection = self::COLLECTION_DEFAULT): OpenApi
    {
        $middlewares = Arr::get($this->config, 'collections.'.$collection.'.middlewares');
        $specVersion = SpecVersion::fromConfig(
            Arr::get(
                $this->config,
                'collections.'.$collection.'.openapi',
                Arr::get($this->config, 'collections.'.$collection.'.version')
            )
        );

        $info = $this->infoBuilder->build(Arr::get($this->config, 'collections.'.$collection.'.info', []));
        $servers = $this->serversBuilder->build(Arr::get($this->config, 'collections.'.$collection.'.servers', []));
        $tags = $this->tagsBuilder->build(Arr::get($this->config, 'collections.'.$collection.'.tags', []));
        $paths = $this->pathsBuilder->build($collection, Arr::get($middlewares, 'paths', []));
        $components = $this->componentsBuilder->build($collection, Arr::get($middlewares, 'components', []));
        $extensions = Arr::get($this->config, 'collections.'.$collection.'.extensions', []);

        return $this->openApiFactory->create(
            $specVersion,
            $info,
            $servers,
            $tags,
            $paths,
            $components,
            Arr::get($this->config, 'collections.'.$collection.'.security', []),
            $extensions
        );
    }
}
