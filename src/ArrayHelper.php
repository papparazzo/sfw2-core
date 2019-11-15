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

use ArrayAccess;
use Iterator;
use Countable;

class ArrayHelper implements ArrayAccess, Iterator, Countable {
    protected $pos = 0;
    protected $valid = true;

    protected $var = [];

    public function __construct(array $var = []){
        $this->var = $var;
    }

    public function __toString() {
        return implode(', ', $this->var);
    }

    public function isFirst() {
        return ($this->pos == 0);
    }

    public function isLast() {
        return ($this->pos == count($this->var) - 1);
    }

    public function isMiddle() {
        return !$this->isLast() && !$this->isFirst();
    }

    public function isEven() {
        return !$this->isOdd();
    }

    public function isOdd() {
        return $this->pos % 2;
    }

	public function offsetExists($offset) {
        return isset($this->var[$offset]);
    }

	public function offsetGet($offset) {
        return $this->var[$offset];
    }

	public function offsetSet($offset, $value) {
        unset($offset, $value);
        // -#- NOOP
    }

	public function offsetUnset($offset) {
        unset($offset);
        // -#- NOOP
    }

    public function count() {
        return count($this->var);
    }

	public function current() {
        return current($this->var);
    }

	public function next() {
        if(next($this->var) === false) {
            $this->valid = false;
        }
        $this->pos++;
    }

    public function key() {
        return key($this->var);
    }

    public function valid() {
        if(empty($this->var)) {
            return false;
        }
        return $this->valid;
    }

    public function rewind() {
        $this->valid = true;
        reset($this->var);
        $this->pos = 0;
    }
}