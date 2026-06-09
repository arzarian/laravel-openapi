<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders;

use Illuminate\Support\Arr;
use OpenApi\Annotations\Contact;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\License;
use Vyuldashev\LaravelOpenApi\Support\OpenApi\SpecificationObjectSerializer;

class InfoBuilder
{
    protected SpecificationObjectSerializer $serializer;
    public function __construct(
        ?SpecificationObjectSerializer $serializer = null,
    ) {
        $this->serializer = $serializer ?? new SpecificationObjectSerializer();
    }

    /**
     * @param array<string, mixed> $config
     */
    public function build(array $config): Info
    {
        $properties = [
            'title' => Arr::get($config, 'title'),
            'description' => Arr::get($config, 'description'),
            'version' => Arr::get($config, 'version'),
        ];

        if (Arr::has($config, 'contact')
            && (
                \array_key_exists('name', $config['contact'])
                || \array_key_exists('email', $config['contact'])
                || \array_key_exists('url', $config['contact'])
            )
        ) {
            $properties['contact'] = $this->buildContact($config['contact']);
        }

        if (Arr::has($config, 'license') && \array_key_exists('name', $config['license'])) {
            $properties['license'] = $this->buildLicense($config['license']);
        }

        $properties['x'] = $this->serializer->toArray($config['extensions'] ?? []);

        return new Info($this->serializer->properties($properties));
    }

    /**
     * @param array<string, mixed> $config
     */
    protected function buildContact(array $config): Contact
    {
        return new Contact($this->serializer->properties([
            'name' => Arr::get($config, 'name'),
            'email' => Arr::get($config, 'email'),
            'url' => Arr::get($config, 'url'),
        ]));
    }

    /**
     * @param array<string, mixed> $config
     */
    protected function buildLicense(array $config): License
    {
        return new License($this->serializer->properties([
            'name' => Arr::get($config, 'name'),
            'url' => Arr::get($config, 'url'),
        ]));
    }
}
