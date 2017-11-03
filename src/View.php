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

use DateTime;
use DateTimeZone;

class View {

    protected $vars        = [];
    protected $jsFiles     = [];
    protected $cssFiles    = [];

    protected $templateId  = 0;

    public function __construct($templateId = 0) {
        $this->templateId  = $templateId;
    }

    public function appendJSFiles(Array $files) {
        $this->jsFiles = array_merge($this->jsFiles, $files);
    }

    public function appendJSFile(string $file) {
        $this->jsFiles[] = $file;
    }

    public function appendCSSFiles(Array $files) {
        $this->cssFiles = array_merge($this->cssFiles, $files);
    }

    public function appendCSSFile(string $file) {
        $this->cssFiles[] = $file;
    }

    public function assign(string $name, $val) {
        $this->vars[$name] = $val;
    }

    public function assignArray(Array $values) {
        $this->vars = array_merge($this->vars, $values);
    }

    public function append(string $name, $val) {
        if(!isset($this->vars[$name])) {
            $this->vars[$name] = [];
        }
        $this->vars[$name][] = $val;
    }

    public function __toString() : string {
        return $this->getContent();
    }

    public function __isset(string $name) : bool {
        return isset($this->vars[$name]);
    }

    public function __get(string $name) {
        if(isset($this->vars[$name])) {
            $this->vars[$name] = View\Helper::getViewHelper($this->vars[$name]);
            return $this->vars[$name];
        }
        throw new ViewException(
            'template-var "' . $name . '" not set',
            ViewException::VARIABLE_MISSING
        );
    }

    public function getCSSFiles() : Array {
        return $this->cssFiles;
    }

    public function getJSFiles() : Array {
        return $this->jsFiles;
    }

    public function getTemplateId() : int {
        return $this->templateId;
    }

    public function getContent(string $tpl) : string {
        ob_start();
        $this->showContent($tpl);
        return ob_get_clean();
    }

    public function showContent(string $tplFile) {
        if(file_exists($tplFile) && is_readable($tplFile)) {
            $this->loadTemplateFile($tplFile);
            return;
        }
        throw new ViewException(
            'Could not find template "' . $tplFile . '"',
            ViewException::TEMPLATE_MISSING
        );
    }

   protected function loadTemplateFile($file) {
        if(!isset($this->vars['modiDate']) || $this->vars['modiDate'] == '') {
            $this->vars['modiDate'] = new DateTime(
                '@' . filemtime($file),
                new DateTimeZone('Europe/Berlin')
            );
        }
        include($file);
    }
}
