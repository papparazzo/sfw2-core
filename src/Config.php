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

use SFW2\Core\Config\Exception as ConfigException;

class Config {

    protected $conf = [];
    protected $objects = [];

    public function __construct(string $configFile, string $defaultFile) {
        $this->checkConfigFile($configFile);
        $this->checkConfigFile($defaultFile);

        $this->conf = array_merge(
            require_once $defaultFile,
            require_once $configFile
        );
    }

    protected function checkConfigFile(string $configFile) {
        if(!is_readable($configFile)) {
            throw new ConfigException("config-file <$configFile> is not readable", ConfigException::FILE_NOT_FOUND);
        }
    }

    public function getVal(string $section, string $key, $def = null) {
        if(!isset($this->conf[$section][$key])) {
            return $def;
        }
        return $this->conf[$section][$key];
    }

    public function getSection(string $section) : Array {
        if(!isset($this->conf[$section])) {
            return [];
        }
        return $this->conf[$section];
    }
}