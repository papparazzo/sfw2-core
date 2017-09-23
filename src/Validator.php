<?php

/**
 *  SFW2 - SimpleFrameWork
 *
 *  Copyright (C) 2017  Stefan Paproth
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

namespace SFW2;

use SFW2\ErrorProvider;

class Validator {
   protected $data = array();

    const REGEX_TEXT_SIMPLE = '#^[A-Za-zäÄöÖüÜß0-9]+$#';
    const REGEX_FILE_NAME   = '#^[A-Za-zäÄöÖüÜß0-9._]+$#';
    const REGEX_NAME        = '#^[A-Za-zäÄöÖüÜß0-9._\- ]+$#';
    const REGEX_ID          = '#^[A-Za-z0-9\-_]+$#';
    const REGEX_ALL         = '#^.*$#m';
    const REGEX_STRICT      = '#^[A-Za-z0-9]+$#';
    const REGEX_HASH        = '#^[A-Fa-f0-9]+$#';
    const REGEX_NUMERIC     = '#^[0-9\-]+$#';
    const REGEX_TIME        = '#^[0-2]?[0-9]:[0-5]?[0-9]$#';
    const REGEX_DATE        = '#^[0-3]?[0-9]\.[0-1]?[0-9]\.(19|20)?[0-9][0-9]$#';
    const REGEX_PHONE       = '#^\(?(\+|00|0)?[1-9]?[0-9 ]{1,9}(/|\))?[0-9 \-]+$#';
    const REGEX_EMAIL_ADDR  = '#^[a-zA-Z0-9._%\+\-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$#';

    /**
     * @var SFW2\ErrorProvider
     */
    protected $errprovider = null;

    public function __construct(Array $data, ErrorProvider $errprovider = null) {
        $this->data        = $data;
        $this->errprovider = $errprovider;
    }

    public function getRawData() {
        return $this->data;
    }

    public function getData(
        $key, $regEx = '', $mustSet = false, $caption = '',
        $errType = ErrorProvider::IS_EMPTY
    ) {
        $data = '';
        if(isset($this->data[$key])) {
            $data = $this->data[$key];
        }

        if(!$mustSet && $data == '') {
            return '';
        }
        if($data == '') {
            $this->errpro->addError(ErrorProvider::IS_EMPTY, ['<NAME>' => $caption], $key);
            return '';
        }
        if($regEx == '') {
            return $data;
        }

        if(!preg_match($regEx, $data)) {
            $this->errpro->addError($errType, ['<NAME>' => $caption], $key);
        }
        $rv = strip_tags(trim($data));
        if($data != $rv) {
            $this->errpro->addWarning(ErrorProvider::INVALID_CHARS_REMOVED, [], $key);
        }

        if($mustSet && $rv == '') {
            $this->errpro->addError(ErrorProvider::IS_EMPTY, ['<NAME>' => $caption], $key);
        }
        return $rv;
    }

    public function getHref($key, $mustSet = false, $caption = '') {
        $rev = $this->getData($key, self::REGEX_ALL, $mustSet, $caption);
        if($rev === '' && !$mustSet) {
            return $rev;
        }

        if(filter_var($rev, FILTER_VALIDATE_URL) === false || !preg_match('#^(http|https)#', $rev)) {
            $this->errpro->addError(ErrorProvider::INVALID_URL, ['<NAME>' => $caption], $key);
        }
        return $rev;
    }

    public function getTime($key, $mustSet = false, $caption = '') {
        $data = $this->getData($key, self::REGEX_TIME, $mustSet, $caption, ErrorProvider::INVALID_TIME);

        if($data == '' || $this->errpro->hasErrors($key)) {
            return $data;
        }

        $frc = explode(':', $data);

        if(intval($frc[0]) < 0 || intval($frc[0]) > 23 || intval($frc[1]) < 0 || intval($frc[1]) > 59) {
            $this->errpro->addError(
                ErrorProvider::INVALID_TIME,
                ['<NAME>' => $caption],
                $key
            );
        }

        $rv = '';
        if(mb_strlen($frc[0]) == 1) {
            $rv = '0';
        }
        $rv .= $frc[0] . ':';

        if(mb_strlen($frc[1]) == 1) {
            $rv .= '0';
        }
        return $rv . $frc[1];
    }

    public function getDate($key, $mustSet = false, $caption = '', $mustFuture = false) {
        $data = $this->getData($key, self::REGEX_DATE, $mustSet, $caption, ErrorProvider::INVALID_DATE);

        if($data == '' || $this->errpro->hasErrors($key)) {
            return $data;
        }

        $frc = explode('.', $data);

        switch(mb_strlen($frc[2])) {
            case 4:
                break;

            case 2:
                $frc[2] = '20' . $frc[2];
                break;

            default:
                $this->errpro->addError(ErrorProvider::INVALID_DATE, ['<NAME>' => $caption], $key);
        }

        if(!checkdate($frc[1], $frc[0], $frc[2])) {
            $this->errpro->addError(ErrorProvider::INVALID_DATE, ['<NAME>' => $caption], $key);
        }

        $ts = mktime(date("H"), date("i"), date("s"), $frc[1], $frc[0], $frc[2]);

        if($mustFuture && $ts !== false && $ts < mktime()) {
            $this->errpro->addError(ErrorProvider::DATE_IS_NOT_FUTRE, ['<NAME>' => $caption], $key);
        } else if($ts === false) {
            $this->errpro->addError(ErrorProvider::DATE_OUT_OF_RANGE, ['<NAME>' => $caption], $key);
        }

        $rv = '';
        if(mb_strlen($frc[0]) == 1) {
            $rv = '0';
        }
        $rv .= $frc[0] . '.';

        if(mb_strlen($frc[1]) == 1) {
            $rv .= '0';
        }
        return $rv . $frc[1] . '.' . $frc[2];
    }

    public function getArrayValue($key, $mustSet = false, $caption = '', Array $vals = []) {
        $data = $this->getData($key, null, $mustSet, $caption);

        if(!$mustSet && $data == '') {
            return '';
        }

        if(!isset($vals[$data]) && !in_array($data, $vals)) {
            $this->errpro->addError(ErrorProvider::IS_INVALID, ['<NAME>' => $caption], $key);
            return array_shift($vals);
        }
        return $data;
    }

    public function getBool($key) {
        if(isset($this->data[$key])) {
            return true;
        }
        return false;
    }

    public function getEMailAddr($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_EMAIL_ADDR, $mustSet, $caption, ErrorProvider::IS_INVALID);
    }

    public function getPhoneNb($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_PHONE, $mustSet, $caption, ErrorProvider::PHON_ONLY);
    }

    public function getName($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_NAME, $mustSet, $caption);
    }

    public function getHash($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_HASH, $mustSet, $caption);
    }

    public function getSimpleText($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_TEXT_SIMPLE, $mustSet, $caption);
    }

    public function getFileName($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_FILE_NAME, $mustSet, $caption);
    }

    public function getNumeric($key, $mustSet = false, $caption = '') {
        return $this->getData($key, self::REGEX_NUMERIC, $mustSet, $caption, ErrorProvider::NUM_ONLY);
    }

    public function getText($key, $mustSet = false, $caption = '', $max = 0) {
        $rev = $this->getData($key, self::REGEX_ALL, $mustSet, $caption);
        if(mb_strlen($rev) > $max && $max > 0) {
            $this->errpro->addError(ErrorProvider::IS_TO_LONG, array('<NAME>' => $caption, '<MAX>' => $max),$key);
        }
        return $rev;
    }

    public function getTitle($key, $mustSet = false, $caption = '', $max = 0) {
        return ucfirst($this->getText($key, $mustSet, $caption, $max));
    }

    public function getId($key = 'id') {
        return $this->getData($key, self::REGEX_ID, true);
    }

    public function getErrorProvider() {
        return $this->errpro;
    }



}