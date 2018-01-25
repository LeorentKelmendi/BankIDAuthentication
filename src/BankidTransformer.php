<?php
namespace Leo\BankIdAuthentication;

class BankidTransformer
{

    /**
     * @param $response
     */
    public function parseResponse($response)
    {

        return [
            'orderRef'       => $response->orderRef,
            'autoStartToken' => $response->autoStartToken,
        ];
    }
    /**
     * @param $response
     */
    public function transformCollect($response)
    {

        return [
            'progressStatus' => $response->progressStatus,

        ];
    }

    /**
     * @param $ssn
     */
    public function makeAuthenticateParams($ssn)
    {

        return [
            'personalNumber'          => $ssn,
            'endUserInfo'             => [
                'type'  => 'IP_ADDR',
                'value' => '192.168.0.1',
            ],
            'requirementAlternatives' => [
                'requirement' => [
                    'condition' => [
                        'key'   => 'AllowFingerprint',
                        'value' => 'no',
                    ],
                ],
            ],
        ];
    }
}
