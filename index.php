<?php

require __DIR__ . "/vendor/autoload.php";

use Leo\BankIdAuthentication\BankID;

$S = new BankID;

var_dump($S->authenticate('199206142324'));
exit;
