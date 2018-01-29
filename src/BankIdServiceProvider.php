<?php

namespace Leo\BankIdAuthentication;

use Illuminate\Support\ServiceProvider;

class BankIdServiceProvider extends ServiceProvider
{

    public function boot()
    {
        require __DIR__ . '/routes/routes.php';

        $this->publishes([
            __DIR__ . '/view/login-bankid.blade.php' => 'resources/views/login-bankid.blade.php',
        ], 'views');

        $this->app->make('Leo\BankIdAuthentication\BankidController');
    }

    public function register()
    {

        $this->app->bind('BankID', function () {

            return new BankID;
        });
    }
}
