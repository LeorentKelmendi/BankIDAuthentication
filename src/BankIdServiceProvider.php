<?php

namespace Leo\BankIdAuthentication;

use Illuminate\Support\ServiceProvider;

class BankIdServiceProvider extends ServiceProvider
{

    public function boot()
    {
        require __DIR__ . '/routes/routes.php';
    }

    public function register(){


        $this->app->bind('BankID', function (){

            return new BankID;
        })
    }
}
