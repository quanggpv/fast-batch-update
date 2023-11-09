<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Support\ServiceProvider;
use Quangpv\BatchUpdate\DatabaseConnectors\MariaDB;
use Quangpv\BatchUpdate\DatabaseConnectors\Mysql;
use Quangpv\BatchUpdate\DatabaseConnectors\PostgreSql;

class BatchUpdateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $connection = config('database.default');

        $driver = config("database.connections.{$connection}.driver");

        switch ($driver) {
            case 'mysql':
                // detect mysql or mariadb
                $results = \DB::select("SHOW VARIABLES LIKE '%version%';");
                $isMySql = false;
                foreach ($results as $row) {
                    if (preg_match("/mysql/i", $row->Value)) {
                        $isMySql = true;
                        break;
                    }
                }

                if ($isMySql) {
                    $this->app->singleton(BatchUpdateInterface::class, Mysql::class);
                } else {
                    $this->app->singleton(BatchUpdateInterface::class, MariaDB::class);
                }
                break;
            case 'pgsql':
                $this->app->singleton(BatchUpdateInterface::class, PostgreSql::class);
                break;
            default:
                $this->app->singleton(BatchUpdateInterface::class, Mysql::class);
                break;
        }

        $this->app->singleton('BatchUpdate', function ($app) use ($driver) {
            return $app->make(BatchUpdate::class);
        });
    }
}
