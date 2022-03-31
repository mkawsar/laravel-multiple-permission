<?php

namespace Laravel9\Survey\Providers;

use Illuminate\Support\ServiceProvider;

class SurveyAppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Databases/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
    }
}
