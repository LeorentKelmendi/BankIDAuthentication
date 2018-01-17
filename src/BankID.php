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
    private $localCert;
    /**
     * @var mixed
     */
    private $caCert;

    /**
     * @var mixed
     */
    protected $wsdl;

    public function __construct()
    {
        $this->localCert = $this->config() . "/certs/certname.pem";

        $this->caCert = $this->config() . "/certs/appapi.test.bankid.com.pem";

        $this->context_options['ssl'] = [
            'local_cert'          => $this->localCert,
            'cafile'              => $this->caCert,
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

        if (!file_exists($this->localCert)) {

            throw new Exception("Unable to load your BankID Certificate" . $this->localCert, 2);
        }
        if (!file_exists($this->caCert)) {

            throw new Exception("Unable to load your BankID Certificate" . $this->caCert, 3);
        }
        if ($this->ssl_context === null) {

            throw new Exception("Failed to create stream context for communication with bankID server(" . $this->ssl_context . ")");
        }
        $this->soapClient = new SoapClient($this->wsdl, [
            'stream_context' => $this->ssl_context,
        ]);

    }

    /**
     * @param $ssn
     */
    public function authenticate($ssn)
    {
        return $this->soapClient->authenticate($ssn);
    }
    private function config()
    {
        return dirname(__FILE__);
    }

}
