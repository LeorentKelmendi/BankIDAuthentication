<?php
namespace Leo\BankIdAuthentication;

use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{

    public function register()
    {

        $this->commands([
            Commands\MakeBankID::class,
        ]);
    }

    public function boot()
    {

    }
}
