<?php

namespace Quangpv\BatchUpdate;

use Illuminate\Support\ServiceProvider;

class BatchUpdateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('BatchUpdate', function ($app) {
            return $app->make(BatchUpdate::class);
        });
    }
}
