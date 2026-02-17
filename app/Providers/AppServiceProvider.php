<?php

namespace App\Providers;

use App\Http\Middleware\Admin;
use App\Http\Middleware\Vender;
use App\Http\Middleware\VerifiedAccount;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('vender', Vender::class);
        $router->aliasMiddleware('admin', Admin::class);
        $router->aliasMiddleware('verified.account', VerifiedAccount::class);
    }
}
