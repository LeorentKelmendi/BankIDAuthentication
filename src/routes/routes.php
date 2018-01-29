<?php

Route::get('/loginBankID', 'Leo\BankIdAuthentication\BankidController@form');
Route::post('/loginBankID', 'Leo\BankIdAuthentication\BankidController@login')->name('login');
Route::post('/login-status', 'Leo\BankIdAuthentication\BankidController@checkStatus')->name('status');

Route::get('/test1', function () {

    dd("dashboard");
});
