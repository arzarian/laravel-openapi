<?php

namespace Vyuldashev\LaravelOpenApi\Builders;

use Illuminate\Support\Arr;
use OpenApi\Annotations\Server;
use OpenApi\Annotations\ServerVariable;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class ServersBuilder
{
    public function __construct(
        ?SpecificationObjectSerializer $serializer = null
    ) {
        $this->serializer = $serializer ?? new SpecificationObjectSerializer();
    }

    protected SpecificationObjectSerializer $serializer;

    /**
     * @param  array  $config
     * @return Server[]
     */
    public function build(array $config): array
    {
        return collect($config)
            ->map(static function (array $server) {
                $variables = collect(Arr::get($server, 'variables'))
                    ->map(function (array $variable, string $key) {
                        return new ServerVariable(array_filter([
                            'serverVariable' => $key,
                            'default' => Arr::get($variable, 'default'),
                            'description' => Arr::get($variable, 'description'),
                            'enum' => is_array(Arr::get($variable, 'enum')) ? Arr::get($variable, 'enum') : null,
                        ], static fn (mixed $value): bool => $value !== null && $value !== []));
                    })
                    ->toArray();

                return new Server(array_filter([
                    'url' => Arr::get($server, 'url'),
                    'description' => Arr::get($server, 'description'),
                    'variables' => $variables,
                ], static fn (mixed $value): bool => $value !== null && $value !== []));
            })
            ->toArray();
    }
}
