<?php

require __DIR__ . "/vendor/autoload.php";
use Leo\BankIdAuthentication\BankID;

class Controller
{
    /**
     * @var mixed
     */
    protected $response;

    /**
     * @var mixed
     */
    protected $status;
    /**
     * @param $ssn
     */
    public function __construct($ssn, BankID $bankid)
    {
        $this->response = $bankid->authenticate($ssn);
    }

}
