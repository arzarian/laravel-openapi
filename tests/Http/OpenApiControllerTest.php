<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Http;

use Vyuldashev\LaravelOpenApi\Generator;
use Vyuldashev\LaravelOpenApi\Http\OpenApiController;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class OpenApiControllerTest extends TestCase
{
    public function testControllerGeneratesRequestedCollection(): void
    {
        $controller = new OpenApiController();
        $generator = $this->app[Generator::class];

        $default = \json_decode($controller->show($generator)->toJson(), true);
        $internal = \json_decode($controller->show($generator, 'internal')->toJson(), true);

        self::assertSame('Default API', $default['info']['title']);
        self::assertSame('3.0.4', $default['openapi']);
        self::assertSame('Internal API', $internal['info']['title']);
        self::assertSame('3.1.2', $internal['openapi']);
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('openapi.collections', [
            'default' => [
                'openapi' => '3.0.4',
                'info' => [
                    'title' => 'Default API',
                    'version' => '1.0.0',
                ],
                'route' => [
                    'uri' => '/default-openapi',
                    'middleware' => [],
                ],
            ],
            'internal' => [
                'openapi' => '3.1.2',
                'info' => [
                    'title' => 'Internal API',
                    'version' => '1.0.0',
                ],
                'route' => [
                    'uri' => '/internal-openapi',
                    'middleware' => [],
                ],
            ],
        ]);
    }
}
