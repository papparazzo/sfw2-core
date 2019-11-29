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
    protected int $pos = 0;
    protected bool $valid = true;

    protected $var = [];

    public function __construct(array $var = []) {
        $this->var = $var;
    }

    public function __toString() : string {
        return implode(', ', $this->var);
    }

    public function isFirst() : bool {
        return ($this->pos == 0);
    }

    public function isLast() : bool {
        return ($this->pos == count($this->var) - 1);
    }

    public function isMiddle() : bool {
        return !$this->isLast() && !$this->isFirst();
    }

    public function isEven() : bool {
        return !$this->isOdd();
    }

    public function isOdd() : bool {
        return $this->pos % 2;
    }

	public function offsetExists($offset) : bool {
        return isset($this->var[$offset]);
    }

	public function offsetGet($offset) {
        return $this->var[$offset];
    }

	public function offsetSet($offset, $value) : void {
        unset($offset, $value);
        // -#- NOOP
    }

	public function offsetUnset($offset) : void {
        unset($offset);
        // -#- NOOP
    }

    public function count() : int {
        return count($this->var);
    }

	public function current() {
        return current($this->var);
    }

	public function next() : void {
        if(next($this->var) === false) {
            $this->valid = false;
        }
        $this->pos++;
    }

    public function key() {
        return key($this->var);
    }

    public function valid() : bool {
        if(empty($this->var)) {
            return false;
        }
        return $this->valid;
    }

    public function rewind() : void {
        $this->valid = true;
        reset($this->var);
        $this->pos = 0;
    }
}