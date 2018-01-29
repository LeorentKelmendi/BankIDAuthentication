<?php

namespace Leo\BankIdAuthentication\validator;

use Illuminate\Validation\Validator;

class Ssn extends Validator
{

    const MAX_LENGTH = 12;

    const MIN_LENGTH = 10;

    const INVALID = 'ssnInvalid';

    const INCORRECT_LENGTH = 'ssnIncorrectLength';

    const NO_MATCH = 'ssnNoMathc';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::NO_MATCH         => 'Wrong personal number.',
        self::INCORRECT_LENGTH => 'Personal number should be 10 or 12 digits long.',
        self::INVALID          => 'Invalid personal number.',
    ];
    /**
     * @var string
     */
    protected $pattern = '/^(\d{6}(\d{2})?)\-?(\d{4})$/';
    /**
     * @param $rule
     * @param $value
     * @return mixed
     */
    public function validateSsn($rule, $value)
    {
        if (!$this->luhnAlgorithm($value)) {
            return false;
        }
        return $this->isValid($value);
    }

    /**
     * @param $value
     */
    public function isValid($value)
    {
        $value = $this->cleanSsn($value);

        $length = strlen($value);

        if (!in_array($length, [self::MAX_LENGTH, self::MIN_LENGTH])) {

            $this->errors()->add(self::INCORRECT_LENGTH, $this->messageTemplates[self::INCORRECT_LENGTH]);
        }
        if (!preg_match($this->pattern, $value, $matches)) {

            $this->errors()->add(self::NO_MATCH, $this->messageTemplates[self::NO_MATCH]);

            return false;
        }

        foreach ($matches as $match) {

            if (0 === (int) $match) {

                $this->errors()->add(self::NO_MATCH, $this->messageTemplates[self::NO_MATCH]);

                return false;
            }
        }
        return true;
    }

    /**
     * @param $ssnnumbers
     */
    private function luhnAlgorithm($ssnNumbers)
    {
        $ssnNumbers = array_reverse(str_split($ssnNumbers));
        $sum        = 0;

        foreach ($ssnNumbers as $key => $numbers) {

            if ($key % 2) {
                $numbers = $numbers * 2;
            }

            $sum += ($numbers > 10 ? $numbers - 9 : $numbers);

        }

        return ($sum % 10 === 0);
    }

    /**
     * @param $ssn
     */
    private function cleanSsn($ssn)
    {

        $ssn = preg_replace("/[^0-9]/", "", $ssn);

        $split = substr($ssn, 0, 2);

        if ($split != 19 && $split > date('y')) {
            $ssn = '19' . $ssn;
        }
        if ($split != 20 && $split <= date('y')) {
            $ssn = '20' . $ssn;
        }

        return $ssn;
    }
}
