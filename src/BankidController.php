<?php

namespace Leo\BankIdAuthentication;

use App\Http\Controllers\Controller;
use Leo\BankIdAuthentication\BankID;

class BankidController extends Controller
{

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

        $login = new BankID;

        $response = $login->authenticate($ssn);

        return response()->json(['data' => $response['orderRef']]);
    }

    public function checkStatus()
    {
        $login = new BankID;

        $orderRef = request()->get('order');

        $message = $login->collect($orderRef);

        return response()->json(['message' => $message]);
    }
}
