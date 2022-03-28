<?php

namespace Laravel9\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthAPIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
    }
}
