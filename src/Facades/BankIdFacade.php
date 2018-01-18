<?php
namespace Leo\BankIdAuthentication\Facades;

use Illuminate\Support\Facades\Facade;

class BankIdFacade extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'BankID';
    }
}
