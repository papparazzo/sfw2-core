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

namespace SFW2\Core;

use SFW2\Core\Image\ImageException;

class Image {

    const JPEG = 'jpg';
    const PNG  = 'png';

    const MAX_IMAGE_COUNT = 10000;

    const DIR_ORIGINAL_RES  = 'high' . DIRECTORY_SEPARATOR;
    const DIR_REGULAR_RES   = 'regular' . DIRECTORY_SEPARATOR;
    const DIR_THUMB_RES     = 'thumb' . DIRECTORY_SEPARATOR;

    const THUMB_SIZE   = 170;
    const REGULAR_SIZE = 335;

    protected $path = '';

    public function __construct(string $path) {
        $this->path = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function storeImage(string $data) : string {
        $this->createDirs();

        $chunk = explode(';', $data);
        $type = explode(':', $chunk[0]);
        $type = $type[1];
        $data = explode(',', $chunk[1]);

        switch($type) {
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/jpg':
                $type = self::JPEG;
                break;

            case 'image/png':
            case 'image/x-png':
                $type = self::PNG;
                break;

            default:
                throw new ImageException(
                    'type "' . $chunk[0] . '" is not a valid image',
                    ImageException::INVALID_IMAGE_TYPE
                );
        }

        $cnt = count(glob($this->path . self::DIR_ORIGINAL_RES . '*'));
        if($cnt >= self::MAX_IMAGE_COUNT) {
            throw new ImageException(
                'more then <' . self::MAX_IMAGE_COUNT . '> images are not allowed',
                ImageException::MAX_IMAGE_COUNT_REACHED
            );
        }

        $filename = str_repeat('0', mb_strlen('' . self::MAX_IMAGE_COUNT) - mb_strlen('' . $cnt)) . ++$cnt . '.' . $type;

        if(!file_put_contents($this->path . self::DIR_ORIGINAL_RES . $filename, base64_decode($data[1]))) {
            throw new ImageException(
                'could not store file "' . $filename . '" in path "' .
                $this->path . self::DIR_ORIGINAL_RES . '"',
                ImageException::COULD_NOT_STORE_IMAGE
            );
        }

        $this->generateThumb(
            $filename,
            self::THUMB_SIZE,
            $this->path . self::DIR_ORIGINAL_RES,
            $this->path . self::DIR_THUMB_RES
        );
        $this->generateThumb(
            $filename,
            self::REGULAR_SIZE,
            $this->path . self::DIR_ORIGINAL_RES,
            $this->path . self::DIR_REGULAR_RES
        );
        return $filename;
    }

    protected function createDirs() {
        if(
            !mkdir($this->path, 0777, true) &&
            !mkdir($this->path . self::DIR_THUMB_RES) &&
            !mkdir($this->path . self::DIR_ORIGINAL_RES) &&
            !mkdir($this->path . self::DIR_REGULAR_RES)
        ) {
            throw new ImageException(
                'could not create path "' . $this->path . '"',
                ImageException::COULD_NOT_CREATE_PATH
            );
        }
    }

    public function getImage($file) : Array {
        return array(
            'thumb'   => DIRECTORY_SEPARATOR . $this->path . self::DIR_THUMB_RES . $file,
            'regular' => DIRECTORY_SEPARATOR . $this->path . self::DIR_REGULAR_RES . $file,
            'high'    => DIRECTORY_SEPARATOR . $this->path . self::DIR_REGULAR_RES . $file
        );
    }

    protected function generateThumb($file, $size, $src, $des) : bool {
        if(!is_file($src . DIRECTORY_SEPARATOR . $file)) {
            return false;
        }

        list($srcWidth, $srcHeight, $srcTyp) = getimagesize($src . DIRECTORY_SEPARATOR . $file);

        if($srcWidth >= $srcHeight) {
            $desWidth = $size;
            $desHeight = $srcHeight / $srcWidth * $size;
        } else {
            $desHeight = $size;
            $desWidth = $srcWidth / $srcHeight * $size;
        }

        switch($srcTyp) {
            case IMAGETYPE_JPEG:
                $old = imagecreatefromjpeg($src . DIRECTORY_SEPARATOR . $file);
                $new = imagecreatetruecolor($desWidth, $desHeight);
                imagecopyresampled(
                    $new, $old, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight
                );
                imagejpeg($new, $des . DIRECTORY_SEPARATOR . $file, 100);
                imagedestroy($old);
                imagedestroy($new);
                return true;

            case IMAGETYPE_PNG:
                $old = imagecreatefrompng($src . DIRECTORY_SEPARATOR . $file);
                $new = imagecreatetruecolor($desWidth, $desHeight);
                imagecopyresampled(
                    $new, $old, 0, 0, 0, 0, $desWidth, $desHeight, $srcWidth, $srcHeight
                );
                imagepng($new, $des . DIRECTORY_SEPARATOR . $file);
                imagedestroy($old);
                imagedestroy($new);
                return true;
        }
    }
}