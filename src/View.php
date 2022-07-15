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

use SFW2\Core\View\Exception as ViewException;

class View {

    protected array $vars = [];
    protected string $template;

    public function __construct(string $template = '') {
        $this->template = $template;
    }

    public function assign(string $name, $val) : void {
        $this->vars[$name] = $val;
    }

    public function assignArray(array $values) : void {
        $this->vars = array_merge($this->vars, $values);
    }

    public function append(string $name, $val) : void {
        if(!isset($this->vars[$name])) {
            $this->vars[$name] = [];
        }
        $this->vars[$name][] = $val;
    }

    public function appendArray(string $name, array $values) : void {
        if(!isset($this->vars[$name])) {
            $this->vars[$name] = [];
        }
        $this->vars[$name] = array_merge($this->vars[$name], $values);
    }

    /**
     * @throws \SFW2\Core\View\Exception
     */
    public function __toString() : string {
        return $this->getContent();
    }

    public function __isset(string $name) : bool {
        return isset($this->vars[$name]);
    }

    /**
     * @throws \SFW2\Core\View\Exception
     */
    public function __get(string $name) {
        if(isset($this->vars[$name])) {
            return $this->vars[$name];
        }
        throw new ViewException("template-var <$name> not set", ViewException::VARIABLE_MISSING);
    }

    /**
     * @throws \SFW2\Core\View\Exception
     */
    public function getContent() : string {
        ob_start();
        $this->showContent();
        return ob_get_clean();
    }

    /**
     * @throws \SFW2\Core\View\Exception
     * @throws \Exception
     */
    protected function showContent() : void {
        if(!file_exists($this->template) || !is_readable($this->template)) {
            throw new ViewException("Could not find template <{$this->template}>", ViewException::TEMPLATE_MISSING);
        }

        include($this->template);
    }

}
