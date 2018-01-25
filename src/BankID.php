<?php

namespace Leo\BankIdAuthentication;

use Exception;
use Leo\BankIdAuthentication\BankidTransformer;
use SoapClient;
use SoapFault;

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

            throw new \Exception("Unable to load your BankID Certificate" . $this->localCert, 2);
        }
        if (!file_exists($this->caCert)) {

            throw new \Exception("Unable to load your BankID Certificate" . $this->caCert, 3);
        }
        if ($this->ssl_context === null) {

            throw new \Exception("Failed to create stream context for communication with bankID server(" . $this->ssl_context . ")");
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

        } catch (\Exception $e) {

            if ($e instanceof \SoapFault) {
                throw Exception($e->getMessage());
            }
            throw new Exception('Something went wrong: ' . $e->getMessage());
        }

        if (!isset($response->orderRef) || !isset($response->autoStartToken)) {

            throw new Exception("Wrong response from BANKID api");
        }

        $parsedResponse = $this->bankIdTransformer->parseResponse($response);

        return $parsedResponse;
    }
    /**
     * @param $orderParams
     * @return mixed
     */
    public function collect($orderRef)
    {

        try {

            $response = $this->soapClient->Collect($orderRef);

            if (!isset($response->progressStatus)) {
                throw new Exception("BankID bad response on collect status");
            }
            $response = $this->bankIdTransformer->transformCollect($response);

            //need to do aditional check if authentication happens in speed of light

            return $this->statusPool($response['progressStatus']);

        } catch (\Exception $e) {

            if ($e instanceof \SoapFault) {
                var_dump($e->getMessage());
            }
            throw new \Exception('Something went wrong: ' . $e->getMessage());
        }

    }

    /**
     * @param $status
     */
    private function statusPool($status)
    {

        switch ($status) {
            case self::STATUS_ALREADY_IN_PROGRESS:
                return 'Action cancelled. Please try again.';
                break;
            case 'COMPLETE':
                return self::STATUS_COMPLETE;
                break;
            case self::STATUS_OUTSTANDING_TRANSACTION:
                return 'Start your BankId App';
                break;
            case self::STATUS_NO_CLIENT:
                return 'Start your BankId App';
                break;
            case self::STATUS_INTERNAL_ERROR:
                return 'Internal error. Please try again. ';
                break;
            case self::STATUS_USER_CANCEL:
                return 'Action cancelled';
                break;
            case self::STATUS_USER_SIGN:
                return 'Enter your security code in the BankID app
                    and select Identify or Sign';
                break;
            case self::STATUS_STARTED:
                return 'Searching for BankID:s, it may take a little
                    while…';
                break;
            case self::STATUS_OUTSTANDING_TRANSACTION:
                return 'A login request has been sent, try again shortly';
                break;
            default:
                return 'Internal Failure. Update BankId and try again';
                break;
        }
    }
    private function config()
    {
        return dirname(__FILE__);
    }

}
