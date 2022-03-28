<?php

namespace Laravel9\Auth\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Laravel9\Auth\Http\Middleware\JwtMiddleware;

class AuthAPIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->aliasMiddleware('jwt.verify', JwtMiddleware::class);
        $this->loadRoutesFrom(__DIR__ . '/../Http/routes.php');
    }
}
