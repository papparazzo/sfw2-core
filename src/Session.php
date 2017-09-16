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

namespace SFW2;

class Session {
    #http://de3.php.net/manual/en/session.security.php#87608
    #http://www.php.net/manual/de/function.setcookie.php#94398

    const GLOBAL_SECTION     = 'global';

    protected $path          = null;
    protected $serverName    = '';

    public function __construct($serverName) {
        $this->path = self::GLOBAL_SECTION;
        $this->serverName = $serverName;
        $this->startSession();
    }

    public function __destruct() {
        session_write_close();
    }

    public function regenerateSession() {
        $this->destroySession();
        $this->startSession();
        session_regenerate_id(true);
    }

    public function setPath($path) {
        if(!empty($path)) {
            $this->path = 'p' . $path;
        }
    }

    public function destroySession() {
        $domain = filter_var($this->serverName, FILTER_SANITIZE_URL);
        setcookie(
            session_name(), '', time() - 42000, '/', $domain, true, true
        );
        session_destroy();
        $_SESSION = array();
    }

    public function isPathEntrySet($index) {
        return $this->isEntrySet($index, $this->path);
    }

    public function getPathEntry($index) {
        return $this->getEntry($index, $this->path);
    }

    public function setPathEntry($index, $val) {
        $this->setEntry($index, $val, $this->path);
    }

    public function delPathEntry($index) {
        return $this->delEntry($index, $this->path);
    }

    public function delAllPathEntries() {
        return $this->delAllEntries($this->path);
    }

    public function isGlobalEntrySet($index) {
        return $this->isEntrySet($index, self::GLOBAL_SECTION);
    }

    public function getGlobalEntry($index) {
        return $this->getEntry($index, self::GLOBAL_SECTION);
    }

    public function setGlobalEntry($index, $val) {
        $this->setEntry($index, $val, self::GLOBAL_SECTION);
    }

    public function delGlobalEntry($index) {
        return $this->delEntry($index, self::GLOBAL_SECTION);
    }

    public function delAllGlobalEntries() {
        return $this->delAllEntries(self::GLOBAL_SECTION);
    }

    public function generateToken() {
        $token = md5(microtime() . mt_rand());
        $this->setGlobalEntry('xss_token', $token);
        return $token;
    }

    public function compareToken($rtoken) {
        $token = $this->getGlobalEntry('xss_token');
        if($token == null) {
            return false;
        }
        $this->delGlobalEntry('xss_token');
        return ($rtoken == $token);
    }

    public function getToken() {
        $token = $this->getGlobalEntry('xss_token');
        $this->delGlobalEntry('xss_token');
        return $token;
    }

    protected function startSession() {
        $domain = filter_var($this->serverName, FILTER_SANITIZE_URL);
        ini_set("session.use_only_cookies", "1");
        ini_set("session.cookie_lifetime", "1800");
        ini_set("session.cookie_httponly", "1");
        ini_set("session.bug_compat_42", "0");
        ini_set("session.bug_compat_warn", "0");

        session_set_cookie_params(1800, '/', $domain, false, true);
        session_start();
    }
    protected function isEntrySet($index, $section) {
        if(isset($_SESSION[$section][$index])) {
            return true;
        }
        return false;
    }

    protected function getEntry($index, $section) {
        if(!isset($_SESSION[$section][$index])) {
            return null;
        }
        return unserialize($_SESSION[$section][$index]);
    }

    protected function setEntry($index, $val, $section) {
        $_SESSION[$section][$index] = \serialize($val);
    }

    protected function delEntry($index, $section) {
        if(!isset($_SESSION[$section][$index])) {
            return false;
        }
        unset($_SESSION[$section][$index]);
        return true;
    }

    protected function delAllEntries($section) {
        if(!isset($_SESSION[$section])) {
            return false;
        }
        unset($_SESSION[$section]);
        return true;
    }

    private function __clone() {
    }
}