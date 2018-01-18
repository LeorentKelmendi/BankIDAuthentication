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

        return $response;
    }
    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}

$c = new Controller('195407308575', new BankID);

$response = $c->checkStatus()['progressStatus'];

while ($response !== BankID::STATUS_COMPLETE && $this->response['orderRef']) {

    $response = $c->checkStatus()['progressStatus'];

}

var_dump($response);exit;
