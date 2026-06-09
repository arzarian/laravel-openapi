<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Builders\Components;

use Illuminate\Support\Collection;
use Vyuldashev\LaravelOpenApi\Attributes\Collection as CollectionAttribute;
use Vyuldashev\LaravelOpenApi\ClassMapGenerator;
use Vyuldashev\LaravelOpenApi\Generator;

abstract class Builder
{
    /**
     * @param array<int, string> $directories
     */
    public function __construct(protected array $directories)
    {
    }

    /**
     * @return Collection<int, class-string>
     * @param string $collection
     */
    protected function getAllClasses(string $collection): Collection
    {
        return collect($this->directories)
            ->map(static function (string $directory) {
                $map = ClassMapGenerator::createMap($directory);

                return \array_keys($map);
            })
            ->flatten()
            ->filter(static function (string $class) use ($collection): bool {
                if (!\class_exists($class)) {
                    return false;
                }

                $reflectionClass = new \ReflectionClass($class);
                $collectionAttributes = $reflectionClass->getAttributes(CollectionAttribute::class);

                if (!$collectionAttributes && $collection === Generator::COLLECTION_DEFAULT) {
                    return true;
                }

                if (!$collectionAttributes) {
                    return false;
                }

                /** @var CollectionAttribute $collectionAttribute */
                $collectionAttribute = $collectionAttributes[0]->newInstance();
                $collectionNames = \is_array($collectionAttribute->name)
                    ? $collectionAttribute->name
                    : [$collectionAttribute->name];

                return
                    $collectionNames === ['*']
                    || \in_array($collection, $collectionNames, true);
            })
            ->values();
    }
}
