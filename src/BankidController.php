<?php

namespace Leo\BankIdAuthentication;

use App\Http\Controllers\Controller;
use Leo\BankIdAuthentication\BankID;

class BankidController extends Controller
{
    /**
     * @var mixed
     */
    private $bankId;

    public function __construct()
    {

        $this->bankId = new BankID;

    }
    public function form()
    {
        return view('loginbankID');
    }

    public function login()
    {
        $ssn = request()->input('ssn');

        $this->validate(request(), [
            'ssn' => 'required|min:10|max:12|ssn',
        ]);

        $response = $this->bankId->authenticate($ssn);

        return response()->json(['data' => $response['orderRef']]);
    }

    public function checkStatus()
    {

        $orderRef = request()->get('order');

        $message = $this->bankId->collect($orderRef);

        return response()->json(['message' => $message]);
    }
}
