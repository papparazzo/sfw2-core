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

use SFW2\Core\Helper\Exception as HelperException;

class Helper {

    public function createFolder($path, $folderName, $i = 0) {
        if($folderName == '') {
            $folderName = md5(mt_rand());
        } else if($i == 0) {
            $folderName = self::getSimplifiedName($folderName);
        }

        if($i > 0) {
            $app = '_' . $i;
        }

        $tmp = $path . DIRECTORY_SEPARATOR . $folderName . $app;

        if(is_file($tmp) || is_dir($tmp)) {
            return self::createFolder($path, $folderName, ++$i);
        }
        if(mkdir($tmp)) {
            return $folderName . $app;
        }
        throw new HelperException(
            'could not create Path "' . $tmp . '"',
            HelperException::COULD_NOT_CREATE_PATH
        );
    }

    public static function getSimplifiedName($name) {
        $name = trim($name);
        $name = str_replace(' ', '_', $name);
        $name = iconv("UTF-8", "ASCII//TRANSLIT", $name); // converts umlauts see http://www.interessante-zeiten.de/webdesign/ae-zu-ae-umlaute-mit-php-umwandeln-312.html for details
        $name = preg_replace('/[^A-Za-z0-9_]/', '', $name);
        return mb_strtolower($name);
    }

    public static function getImageFileName($path, $firstname, $lastname) {
        $file =
            self::getSimplifiedName($firstname) . '_' .
            self::getSimplifiedName($lastname) . '.png';

        $path = trim($path, DIRECTORY_SEPARATOR);
        if(file_exists($path . DIRECTORY_SEPARATOR . $file)) {
            return $file;
        }
        return 'unknown.png';
    }

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
                $pwd .= mb_strtolower(\mb_substr($chars, $j - 27, 1));
            } else {
                $pwd .= mb_substr($other, $j - 53, 1);
            }
        }
        return $pwd;
    }
}