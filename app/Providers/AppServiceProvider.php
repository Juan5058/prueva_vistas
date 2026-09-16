<?php

namespace App\Providers;

use App\Http\Middleware\VerifyUserSessionAndIp;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        config(['database.default' => 'mongodb']);
    }

    public function boot(): void
    {
        View::composer(['layouts.app', 'components.layouts.app', 'auth.login', 'components.layouts.guest'], function ($view) {
            $view->with('currentIp', VerifyUserSessionAndIp::clientIp(request()));
        });
    }
}
