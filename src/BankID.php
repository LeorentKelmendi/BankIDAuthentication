<?php

namespace Leo\BankIdAuthentication;

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
            'local_cert'          => 'cert',
            'cafile'              => 'ca-cert',
            'verify_peer'         => true,
            'verify_peer_name'    => true,
            'verify_depth'        => 5,
            'peer_name'           => '',
            'disable_compression' => true,
            'SNI_enabled'         => true,
            'ciphers'             => 'ALL!EXPORT!EXPORT40!EXPORT56!aNULL!LOW!RC4',
        ];

        $this->wsdl = 'read from config';

        $this->ssl_context = stream_context_create($this->context_options);

        $this->soapClient = new SoapClient($this->wsdl,[
            'stream_context'=>$this->ssl_context;
        ]);
    }

}
