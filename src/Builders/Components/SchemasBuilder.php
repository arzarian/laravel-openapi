<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;
use Vyuldashev\LaravelOpenApi\Generator;

class SchemasBuilder extends Builder
{
    /**
     * @return array<int, mixed>
     * @param string $collection
     */
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        return $this->getAllClasses($collection)
            ->filter(static fn($class) =>
                    \is_a($class, SchemaFactory::class, true)
                    && \is_a($class, Reusable::class, true))
            ->map(static function ($class) {
                /** @var SchemaFactory $instance */
                $instance = App::make($class);

                return $instance->build();
            })
            ->values()
            ->all();
    }
}
