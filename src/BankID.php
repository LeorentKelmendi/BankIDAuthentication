<?php

namespace Leo\BankIdAuthentication;

use Leo\BankIdAuthentication\bankIdStatusTransformer;
use SoapClient;

class BankID
{

    /**
     * @var array
     */
    protected $context_options = [];

    /**
     * @var mixed
     */
    protected $ssl_context;

    /**
     * @var mixed
     */
    protected $soapClient;

    /**
     * @var mixed
     */
    protected $bankIdTransformer;

    /**
     * @var mixed
     */
    protected $wsdl;

    public function __construct()
    {

        $this->context_options['ssl'] = [
            'local_cert'          => $this->config() . "/certs/certname.pem",
            'cafile'              => $this->config() . "/certs/appapi.test.bankid.com.pem",
            'verify_peer'         => true,
            'verify_peer_name'    => true,
            'verify_depth'        => 5,
            'peer_name'           => 'appapi.test.bankid.com',
            'disable_compression' => true,
            'SNI_enabled'         => true,
            'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4',
        ];

        $this->wsdl = 'https://appapi.test.bankid.com/rp/v4?wsdl';

        $this->ssl_context = stream_context_create($this->context_options);

        $this->bankIdTransformer = new bankIdStatusTransformer;

        $this->soapClient = new SoapClient($this->wsdl, [
            'stream_context' => $this->ssl_context,
        ]);

        var_dump($this->soapClient->__getFunctions());
    }

    /**
     * @param $ssn
     */
    public function authenticate($ssn)
    {

    }
    private function config()
    {
        return dirname(__FILE__);
    }

}
