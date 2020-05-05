<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $auth = explode(' ', $request->header('Authorization'));
                $apiKey = empty($auth[1]) ? $auth[0] : $auth[1];
                return User::where('api_key', $apiKey)->first();
            }
            if ($request->header('X-User-Id')) {
                $userId = (int)$request->header('X-User-Id')[0];
                return User::find($userId);
            }
        });
    }
}
