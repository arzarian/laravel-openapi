<?php

declare(strict_types=1);

namespace Vyuldashev\LaravelOpenApi\Support\Database;

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class ModelColumnProvider
{
    /**
     * @param Model $model
     * @return list<Column>
     */
    public function columns(Model $model): array
    {
        $table = $this->tableName($model);
        $connection = $model->getConnection();

        return \array_values(collect(Schema::connection($model->getConnectionName())->getColumnListing($table))
            ->map(
                /** @phpstan-ignore-next-line Laravel connection keeps this method when doctrine/dbal is installed for supported versions. */
                static fn(string $column): Column => $connection->getDoctrineColumn($table, $column),
            )
            ->values()
            ->all());
    }

    public function tableName(Model $model): string
    {
        return Config::get('database.connections.' . Config::get('database.default') . '.prefix', '') . $model->getTable();
    }
}
