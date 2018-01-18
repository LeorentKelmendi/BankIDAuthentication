<?php

namespace Leo\BankIdAuthentication;

use Exception;
use Leo\BankIdAuthentication\BankidTransformer;
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

        $this->bankIdTransformer = new BankidTransformer;

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
        try {

            $params = $this->bankIdTransformer->makeAuthenticateParams($ssn);

            $response = $this->soapClient->Authenticate($params);

        } catch (Exception $e) {

            var_dump($e->getMessage());exit;
        }

        if (!isset($response->orderRef) || !isset($response->autoStartToken)) {

            throw new Exception("Wrong response from BANKID api");
        }

        return $this->bankIdTransformer->parseResponse($response);
    }
    /**
     * @param $orderParams
     * @return mixed
     */
    public function collect($orderRef)
    {

        try {
            $response = $this->soapClient->Collect($orderRef);

            if (!isset($response->progressStatus) || !isset($response->signature)) {
                throw new Exception("BankID bad response on collect status");
            }

            return $this->bankIdTransformer->transformCollect($response);

        } catch (Exception $e) {
            var_dump($e->getMessage());exit;
        }

        return $response;
    }
    private function config()
    {
        return dirname(__FILE__);
    }

}
