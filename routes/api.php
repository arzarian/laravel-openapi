<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Vyuldashev\LaravelOpenApi\Http\OpenApiController;

Route::group(['as' => 'openapi.'], static function (): void {
    foreach (Config::get('openapi.collections', []) as $name => $config) {
        $uri = Arr::get($config, 'route.uri');

        if (! $uri) {
            continue;
        }

        Route::get($uri, [OpenApiController::class, 'show'])
            ->name($name . '.specification')
            ->defaults('collection', $name)
            ->middleware(Arr::get($config, 'route.middleware'));
    }
});
