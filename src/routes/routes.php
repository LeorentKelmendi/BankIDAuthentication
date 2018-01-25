<?php
use Leo\BankIdAuthentication\BankID;

Route::get('/loginBankID', function () {

    return view('loginbankID');
});
Route::post('/loginBankID', function () {

    $ssn = request()->input('ssn');

    $login = new BankID;

    $response = $login->authenticate($ssn);

    return response()->json(['data' => $response['orderRef']]);

})->name('login');

Route::post('/login-status', function () {

    $login = new BankID;

    $orderRef = request()->get('order');

    $message = $login->collect($orderRef);

    return response()->json(['message' => $message]);
})->name('status');

Route::get('/test1', function () {

    dd("dashboard");
});
