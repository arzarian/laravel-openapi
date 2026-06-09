<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Vyuldashev\LaravelOpenApi\Builders\Components\CallbacksBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\RequestBodiesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\ResponsesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\SchemasBuilder;
use Vyuldashev\LaravelOpenApi\Builders\Components\SecuritySchemesBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ComponentsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\InfoBuilder;
use Vyuldashev\LaravelOpenApi\Builders\PathsBuilder;
use Vyuldashev\LaravelOpenApi\Builders\ServersBuilder;
use Vyuldashev\LaravelOpenApi\Builders\TagsBuilder;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\OpenApiFactory;

class OpenApiServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/openapi.php',
            'openapi',
        );

        $this->app->bind(CallbacksBuilder::class, fn() => new CallbacksBuilder($this->getPathsFromConfig('callbacks')));

        $this->app->bind(RequestBodiesBuilder::class, fn() => new RequestBodiesBuilder($this->getPathsFromConfig('request_bodies')));

        $this->app->bind(ResponsesBuilder::class, fn() => new ResponsesBuilder($this->getPathsFromConfig('responses')));

        $this->app->bind(SchemasBuilder::class, fn() => new SchemasBuilder($this->getPathsFromConfig('schemas')));

        $this->app->bind(SecuritySchemesBuilder::class, fn() => new SecuritySchemesBuilder($this->getPathsFromConfig('security_schemes')));

        $this->app->singleton(static function (Application $app): Generator {
            $config = Config::get('openapi');

            if (!\is_array($config)) {
                $config = [];
            }

            return new Generator(
                $config,
                $app->make(InfoBuilder::class),
                $app->make(ServersBuilder::class),
                $app->make(TagsBuilder::class),
                $app->make(PathsBuilder::class),
                $app->make(ComponentsBuilder::class),
                $app->make(OpenApiFactory::class),
            );
        });

        $this->commands([
            Console\GenerateCommand::class,
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\CallbackFactoryMakeCommand::class,
                Console\ExtensionFactoryMakeCommand::class,
                Console\ParametersFactoryMakeCommand::class,
                Console\RequestBodyFactoryMakeCommand::class,
                Console\ResponseFactoryMakeCommand::class,
                Console\SchemaFactoryMakeCommand::class,
                Console\SecuritySchemeFactoryMakeCommand::class,
            ]);
        }
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/openapi.php' => config_path('openapi.php'),
            ], 'openapi-config');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }

    /**
     * @return array<int, string>
     * @param string $type
     */
    private function getPathsFromConfig(string $type): array
    {
        $directories = Config::get('openapi.locations.' . $type, []);

        if (!\is_array($directories)) {
            return [];
        }

        foreach ($directories as &$directory) {
            if (!\is_string($directory)) {
                $directory = [];

                continue;
            }

            $directory = \glob($directory, \GLOB_ONLYDIR) ?: [];
        }

        return new Collection($directories)
            ->flatten()
            ->unique()
            ->filter(static fn(mixed $path): bool => \is_string($path))
            ->values()
            ->all();
    }
}
