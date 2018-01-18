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

        $userInfo = $response->userInfo;
        return [
            'progressStatus' => $response->progressStatus,
            'signature'      => $response->signature,
            'userInfo'       => [
                'name'           => $userInfo['givenName'],
                'surname'        => $userInfo['surname'],
                'fullname'       => $userInfo['name'],
                'personalNumber' => $userInfo['personalNumber'],

            ],
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
