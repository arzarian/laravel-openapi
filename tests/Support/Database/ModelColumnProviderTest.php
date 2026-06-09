<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Tests\Support\Database;

use Illuminate\Database\Eloquent\Model;
use Vyuldashev\LaravelOpenApi\Support\Database\ModelColumnProvider;
use Vyuldashev\LaravelOpenApi\Tests\TestCase;

class ModelColumnProviderTest extends TestCase
{
    public function testTableNameIncludesConfiguredDatabasePrefix(): void
    {
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing.prefix', 'app_');

        $model = new class extends Model {
            protected $table = 'users';
        };

        self::assertSame('app_users', new ModelColumnProvider()->tableName($model));
    }
}
