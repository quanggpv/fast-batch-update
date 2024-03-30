<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Quangpv\BatchUpdate\DatabaseConnectors\MariaDB;
use Quangpv\BatchUpdate\DatabaseConnectors\Mysql;
use Quangpv\BatchUpdate\DatabaseConnectors\PostgreSql;
use Quangpv\BatchUpdate\Exceptions\InvalidValuesLengthException;
use Quangpv\BatchUpdate\Exceptions\MissingDatabaseDriverException;

class BatchUpdate
{
    /**
     * @param Model $model
     * @param array $values
     * @param array $indexes
     * @param array $updateFields
     * @return int
     * @throws MissingDatabaseDriverException|InvalidValuesLengthException
     */
    public function execute(Model $model, array $values, array $indexes = ['id'], array $updateFields = [])
    {
        $connection = $model->getConnectionName() ?? config('database.default');

        $driver = config("database.connections.{$connection}.driver");

        $database = \DB::connection($connection);

        switch ($driver) {
            case 'mysql':
                // detect mysql or mariadb
                $databaseVersion = $database->getConfig('version') ?? $database->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
                $isMariaDb = false;
                if (Str::of($databaseVersion)->contains('MariaDB')) {
                    $isMariaDb = true;
                }
                if ($isMariaDb) {
                    $executor = App::make(MariaDB::class);
                } else {
                    $executor = App::make(Mysql::class);
                }
                break;
            case 'pgsql':
                $executor = App::make(PostgreSql::class);
                break;
            default:
                // Please check your database driver again
                throw new MissingDatabaseDriverException();
        }

        return $executor->execute($model, $values, $indexes, $updateFields);
    }
}
