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

use Exception;

class SFW2Exception extends Exception {
    const UNKNOWN = 0;

    protected $timeStamp = '';
    protected $identifier = '';

    public function __construct(string $msg, int $code = self::UNKNOWN, $prev = null) {
        $this->timeStamp = date('d.m.Y H:i:s');
        $this->identifier = strtoupper(md5(microtime() . mt_rand()));
        $msg = wordwrap($msg, 150) . PHP_EOL;
        parent::__construct($msg, $code, $prev);
    }

    public function getTimeStamp() : string {
        return $this->timeStamp;
    }

    public function getIdentifier() : string {
        return $this->identifier;
    }
}