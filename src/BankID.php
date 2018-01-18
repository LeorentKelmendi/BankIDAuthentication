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

    const STATUS_ALREADY_IN_PROGRESS     = 'ALREADY_IN_PROGRESS';
    const STATUS_COMPLETE                = 'COMPLETE';
    const STATUS_OUTSTANDING_TRANSACTION = 'OUTSTANDING_TRANSACTION';
    const STATUS_NO_CLIENT               = 'NO_CLIENT';
    const STATUS_RETRY                   = 'RETRY';
    const STATUS_INTERNAL_ERROR          = 'INTERNAL_ERROR';
    const STATUS_USER_CANCEL             = 'USER_CANCEL';
    const STATUS_EXPIRED_TRANSACTION     = 'EXPIRED_TRANSACTION';
    const STATUS_USER_SIGN               = 'USER_SIGN';
    const STATUS_CLIENT_ERR              = 'CLIENT_ERR';
    const STATUS_STARTED                 = 'STARTED';
    const STATUS_START_FAILED            = 'START_FAILED';
    const STATUS_INVALID_PARAMETERS      = 'INVALID_PARAMETERS';
    const STATUS_CANCELLED               = 'USER_CANCEL';

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

            $status = $this->bankIdTransformer->transformCollect($response);

            if ($status['progressStatus'] === self::STATUS_COMPLETE) {

                //Redirect authenticated user
                $this->redirect();
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
    private function redirect()
    {

        header("Location: ", "/dashboard.php");
    }

}
