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

class Helper {

    public static function getRandomInt() {
        list($usec, $sec) = explode(' ', microtime());
        $seed = (float) $sec + ((float) $usec * 100000);
        mt_srand($seed);
        return mt_rand();
    }

    public static function generatePassword($length = 10) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $other = '1234567890!+$%&/()=,.-;:_#';
        $pwd   = '';
        for($i = 0; $i < $length; $i++) {
            $j = self::getRandomInt() % 79;
            if($j < 27) {
                $pwd .= mb_substr($chars, $j, 1);
            } else if($j < 53) {
                $pwd .= mb_strtolower(mb_substr($chars, $j - 27, 1));
            } else {
                $pwd .= mb_substr($other, $j - 53, 1);
            }
        }
        return $pwd;
    }
}