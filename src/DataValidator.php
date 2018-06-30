<?php

/**
 *  SFW2 - SimpleFrameWork
 *
 *  Copyright (C) 2018  Stefan Paproth
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */

namespace SFW2\Core;

use SFW2\Core\DataValidator\Exception as DataValidatorException;
use BadMethodCallException;

class DataValidator {

    protected $rulesets = [];

    protected $errorProvider = null;

    public function __construct(array $ruleset) {
        $this->errorProvider = new ErrorProvider();
        $this->rulesets = $ruleset;
    }

    public function validate(array $input, array &$output) : bool {

        $hasErrors = false;
        $output = [];

        foreach($this->rulesets as $field => $ruleset) {
            if(!isset($input[$field])) {
                $input[$field] = '';
            }

            try {
                $output[$field]['hint'] = '';
                $output[$field]['value'] = $input[$field];
                $this->validateItem($ruleset, $input[$field]);
            } catch (DataValidatorException $ex) {
                $output[$field]['hint'] = $ex->getMessage();
                $hasErrors = true;
            }
        }
        return !$hasErrors;
    }

    protected function validateItem(array $ruleset, string $value) {
        foreach($ruleset as $rule) {
            $tokens = explode(':', $rule);
            $method = $tokens[0];
            $params = $tokens[1] ?? '';
            $this->validateCheckMethod($method);
            $this->$method($value, explode(',', $params));
        }
    }

    protected function validateCheckMethod($method) {
        if(!substr($method, 0, strlen('is')) == 'is') {
            throw new BadMethodCallException('"' . $method .'" does not start with "is"');
        }

        if(!is_callable([$this, $method])) {
            throw new BadMethodCallException('"' . $method .'" is not a callable methode');
        }
    }

    protected function isNotEmpty(string $value, array $params) {
        if($value == '') {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::IS_EMPTY));
        }
    }

    protected function isNumeric(string $value, array $params) {
        if(!preg_match('#^[0-9\-]+$#', $value)) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::NUM_ONLY));
        }
    }

    protected function isOneOf(string $value, array $params) {
        if(!in_array($value, $params)) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::INVALID_VALUE));
        }
    }

    protected function isDate(string $value, array $params) {
        return;
        if(!preg_match('#^[0-3]?[0-9]\.[0-1]?[0-9]\.(19|20)?[0-9][0-9]$#', $value)) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::INV_DATE));
        }

        $frc = explode('.', $value);

        switch(mb_strlen($frc[2])) {
            case 4:
                break;

            case 2:
                $frc[2] = '20' . $frc[2];
                break;

            default:
                throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::RAN_DATE));
        }

        if(!checkdate($frc[1], $frc[0], $frc[2])) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::INV_DATE));
        }

        $ts = mktime(date("H"), date("i"), date("s"), $frc[1], $frc[0], $frc[2]);

        if($ts === false) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::INV_DATE));
        }

        if($params['futureOnly'] && $ts < mktime()) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::FUT_DATE));
        }

        if($params['pastOnly'] && $ts > mktime()) {
            throw new DataValidatorException($this->errorProvider->getErrorMessage(ErrorProvider::PAS_DATE));
        }

    }

    protected function isAlphaNumeric(string $value, string &$hint, array $params) : bool {

    }








        #['required', 'checkAlphaNumeric', 'checkRange:0,100', 'checkMinLen:6']
        #checkEmail
        #checkMaxLen
        #checkLen
        #checkIsOneOf




    #protected function











    const REQUIRED    = 'REQUIRED';
    const MAX_LEN     = 'MAX_LEN';
    const MIN_LEN     = 'MIN_LEN';
    const EXACT_LEN   = 'EXACT_LEN';
    const MATCH_REGEX = 'MATCH_REGEX';
    const IS_IN_LIST  = 'IS_IN_LIST'; // Liste mit Elementen

    const VALID_URL   = 'VALID_URL';









    const REGEX_TEXT_SIMPLE = '#^[A-Za-zäÄöÖüÜß0-9]+$#';
    const REGEX_FILE_NAME   = '#^[A-Za-zäÄöÖüÜß0-9._]+$#';
    const REGEX_NAME        = '#^[A-Za-zäÄöÖüÜß0-9._\- ]+$#';
    const REGEX_ID          = '#^[A-Za-z0-9\-_]+$#';
    const REGEX_STRICT      = '#^[A-Za-z0-9]+$#';
    const REGEX_HASH        = '#^[A-Fa-f0-9]+$#';
    const REGEX_NUMERIC     = '#^[0-9\-]+$#';
    const REGEX_TIME        = '#^[0-2]?[0-9]:[0-5]?[0-9]$#';
    const REGEX_PHONE       = '#^\(?(\+|00|0)?[1-9]?[0-9 ]{1,9}(/|\))?[0-9 \-]+$#';
    const REGEX_EMAIL_ADDR  = '#^[a-zA-Z0-9._%\+\-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$#';


    public function getData(string $key, string $regEx = '', bool $mustSet = false) {
        $data = '';
        if(isset($this->data[$key])) {
            $data = $this->data[$key];
        }
        if(!$mustSet && $data == '') {
            return $this->getReturn();
        }
        if($data == '') {
            return $this->getReturn('', self::IS_EMPTY);
        }
        if($regEx == '') {
            return $this->getReturn($data);
        }

        if(preg_match($regEx, $data)) {
            return $this->getReturn($data);
        }
        return $this->getReturn($data, self::IS_INVALID);
    }

    public function getHref(string $key, bool $mustSet = false) {
        $data = $this->getData($key, self::REGEX_ALL, $mustSet);

        if(filter_var($data, FILTER_VALIDATE_URL) !== false && preg_match('#^(http|https)#', $data)) {
            return $data;
        }
        throw new DataValidatorException('"' . $data .'" is no valid url', DataValidatorException::INVALID_URL);
    }

    public function getTime(string $key, bool $mustSet = false) {
        $data = $this->getData($key, self::REGEX_TIME, $mustSet);

        if(!$mustSet && $data == '') {
            return $data;
        }

        $frc = explode(':', $data);

        if(intval($frc[0]) < 0 || intval($frc[0]) > 23 || intval($frc[1]) < 0 || intval($frc[1]) > 59) {
            throw new DataValidatorException('"' . $data .'" is not a valid time', DataValidatorException::INVALID_TIME);
        }

        $rv = '';
        if(strlen($frc[0]) == 1) {
            $rv = '0';
        }
        $rv .= $frc[0] . ':';

        if(strlen($frc[1]) == 1) {
            $rv .= '0';
        }
        return $rv . $frc[1];
    }

    public function getArrayValue($key, $mustSet = false, array $vals = []) {
        $data = $this->getData($key, null, $mustSet);

        if(!$mustSet && $data == '') {
            return '';
        }

        if(!isset($vals[$data]) && !in_array($data, $vals)) {
            throw new DataValidatorException(
                'key "' . $data . '" does not exist in array',
                DataValidatorException::NOT_SET
            );
        }
        return $data;
    }

    public function getBool($key) {
        if(isset($this->data[$key])) {
            return true;
        }
        return false;
    }

    public function getEMailAddr($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_EMAIL_ADDR, $mustSet);
    }

    public function getPhoneNb($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_PHONE, $mustSet);
    }

    public function getName($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_NAME, $mustSet);
    }

    public function getHash($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_HASH, $mustSet);
    }

    public function getSimpleText($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_TEXT_SIMPLE, $mustSet);
    }

    public function getFileName($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_FILE_NAME, $mustSet);
    }

    public function getNumeric($key, $mustSet = false) {
        return $this->getData($key, self::REGEX_NUMERIC, $mustSet);
    }

    public function getText($key, $mustSet = false, $max = 0) {
        $rev = $this->getData($key, self::REGEX_ALL, $mustSet);
        if(mb_strlen($rev) > $max && $max > 0) {
            throw new DataValidatorException(
                'text is to long. Max ' . $max . ' chars allowed',
                DataValidatorException::TO_LONG
            );
        }
        return $rev;
    }

    public function getTitle($key, $mustSet = false, $max = 0) {
        return ucfirst($this->getText($key, $mustSet, $max));
    }

    public function getId($key = 'id') {
        return $this->getData($key, self::REGEX_ID, true);
    }
}
