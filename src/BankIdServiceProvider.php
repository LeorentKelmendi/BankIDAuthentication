<?php

namespace Leo\BankIdAuthentication;

use Illuminate\Support\ServiceProvider;
use Leo\BankIdAuthentication\validator\Ssn;
use Validator;

class BankIdServiceProvider extends ServiceProvider
{

    public function boot()
    {
        require __DIR__ . '/routes/routes.php';

        $this->publishes([
            __DIR__ . '/view/login-bankid.blade.php' => 'resources/views/login-bankid.blade.php',
        ], 'views');

        $this->app->make('Leo\BankIdAuthentication\BankidController');

        Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new Ssn($translator, $data, $rules, $messages);
        });
    }

    public function register()
    {

        $this->app->bind('BankID', function () {

            return new BankID;
        });
    }
}
