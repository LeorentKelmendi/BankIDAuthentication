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
     * @var mixed
     */
    protected $service;
    /**
     * @param $ssn
     */
    public function __construct($ssn, BankID $bankid)
    {
        $this->service  = $bankid;
        $this->response = $bankid->authenticate($ssn);
    }

    /**
     * @param $orderRef
     */
    public function checkStatus()
    {
        $response = $this->service->collect($this->response['orderRef']);

        var_dump($response);
    }

}

$c = new Controller('195407308575', new BankID);

$c->checkStatus();
