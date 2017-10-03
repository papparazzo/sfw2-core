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

use SFW2\Core\View\ViewException;

class View {

    protected $vars        = array();
    protected $jsfiles     = array();
    protected $cssfiles    = array();

    public function appendJSFiles(Array $files) {
        $this->jsfiles = array_merge($this->jsfiles, $files);
    }

    public function appendJSFile($file) {
        $this->jsfiles[] = $file;
    }

    public function appendCSSFiles(Array $files) {
        $this->cssfiles = array_merge($this->cssfiles, $files);
    }

    public function appendCSSFile($file) {
        $this->cssfiles[] = $file;
    }

    public function assign($name, $val) {
        $this->vars[$name] = $val;
    }

    public function assignArray(Array $values) {
        $this->vars = array_merge($this->vars, $values);
    }

    public function append($name, $val) {
        if(!isset($this->vars[$name])) {
            $this->vars[$name] = [];
        }
        $this->vars[$name][] = $val;
    }

    public function __isset($name) {
        return isset($this->vars[$name]);
    }



}
