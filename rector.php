<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use RectorLaravel\Rector\PropertyFetch\ReplaceFakerInstanceWithHelperRector;
use RectorLaravel\Set\LaravelLevelSetList;

return RectorConfig::configure()
    ->withParallel(
        timeoutSeconds: 180,
        maxNumberOfProcess: 4,
        jobSize: 16,
    )
    ->withCache(
        cacheClass: FileCacheStorage::class,
    )
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withPhpSets()
    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
    ])
    ->withConfiguredRule(
        rectorClass: ClassPropertyAssignToConstructorPromotionRector::class,
        configuration: [
            ClassPropertyAssignToConstructorPromotionRector::RENAME_PROPERTY => false,
        ],
    )
    ->withSkip([
        RemoveExtraParametersRector::class,
        ReplaceFakerInstanceWithHelperRector::class,
    ]);
