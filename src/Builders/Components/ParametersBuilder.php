<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Illuminate\Support\Facades\App;
use Vyuldashev\LaravelOpenApi\Builders\Parameter as ParameterBuilder;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParameterFactory;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;
use Vyuldashev\LaravelOpenApi\Generator;

class ParametersBuilder extends Builder
{
    /**
     * @return array<int, mixed>
     * @param string $collection
     */
    public function build(string $collection = Generator::COLLECTION_DEFAULT): array
    {
        return $this->getAllClasses($collection)
            ->filter(static fn($class) =>
                    (
                        \is_a($class, ParameterFactory::class, true)
                        || \is_a($class, ParametersFactory::class, true)
                    )
                    && \is_a($class, Reusable::class, true))
            ->flatMap(function ($class) {
                $instance = App::make($class);

                if ($instance instanceof ParameterFactory) {
                    return [$this->componentParameter($instance->build())];
                }

                if ($instance instanceof ParametersFactory) {
                    return \array_map(
                        $this->componentParameter(...),
                        $instance->build(),
                    );
                }

                return [];
            })
            ->values()
            ->all();
    }

    protected function componentParameter(mixed $parameter): mixed
    {
        if ($parameter instanceof ParameterBuilder && $parameter->parameter === null && $parameter->name !== null) {
            return $parameter->objectId($parameter->name);
        }

        if (\is_array($parameter) && !isset($parameter['parameter'], $parameter['objectId']) && isset($parameter['name'])) {
            return ['parameter' => $parameter['name']] + $parameter;
        }

        return $parameter;
    }
}
