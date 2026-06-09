<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;
use Vyuldashev\LaravelOpenApi\Generator;

class SecuritySchemesBuilder extends Builder
{
    /**
     * @return array<int, mixed>
     * @param string $collection
     */
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        return $this->getAllClasses($collection)
            ->filter(static fn($class) => \is_a($class, SecuritySchemeFactory::class, true))
            ->map(static function ($class) {
                /** @var SecuritySchemeFactory $instance */
                $instance = App::make($class);

                return $instance->build();
            })
            ->values()
            ->all();
    }
}
