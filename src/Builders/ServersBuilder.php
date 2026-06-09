<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use Illuminate\Support\Arr;
use OpenApi\Annotations\Server;
use OpenApi\Annotations\ServerVariable;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ServersBuilder
{
    protected SpecificationObjectSerializer $serializer;
    public function __construct(
        ?SpecificationObjectSerializer $serializer = null,
    ) {
        $this->serializer = $serializer ?? new SpecificationObjectSerializer();
    }

    /**
     * @param array<int, array<string, mixed>> $config
     * @return array<int, Server>
     */
    public function build(array $config): array
    {
        return collect($config)
            ->map(static function (array $server): Server {
                $serverVariables = Arr::get($server, 'variables', []);
                $variables = collect(\is_array($serverVariables) ? $serverVariables : [])
                    ->map(static fn(array $variable, string $key) => new ServerVariable(\array_filter([
                        'serverVariable' => $key,
                        'default' => Arr::get($variable, 'default'),
                        'description' => Arr::get($variable, 'description'),
                        'enum' => \is_array(Arr::get($variable, 'enum')) ? Arr::get($variable, 'enum') : null,
                    ], static fn(mixed $value): bool => $value !== null && $value !== [])))
                    ->toArray();

                return new Server(\array_filter([
                    'url' => Arr::get($server, 'url'),
                    'description' => Arr::get($server, 'description'),
                    'variables' => $variables,
                ], static fn(mixed $value): bool => $value !== null && $value !== []));
            })
            ->values()
            ->all();
    }
}
