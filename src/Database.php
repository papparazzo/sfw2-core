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

use SFW2\Database\DatabaseException;

class Database {

    protected $db = null;

    public function __construct($host, $usr, $pwd, $db) {
        $this->db = new \mysqli($host, $usr, $pwd, $db);
        if(mysqli_connect_error()) {
            throw new DatabaseException(
                "Could not connect to database",
                DatabaseException::CON_FAILED
            );
        }
        $this->query("set names 'utf8';");
    }

    public function delete($stmt, Array $params = []) {
        return $this->update($stmt, $params);
    }

    public function update($stmt, Array $params = []) {
        $params = $this->escape($params);
        $stmt = vsprintf($stmt, $params);
        $this->query($stmt);
        return $this->db->affected_rows;
    }

    public function insert($stmt, Array $params = []) {
        $params = $this->escape($params);
        $stmt = vsprintf($stmt, $params);
        $this->query($stmt);
        return $this->db->insert_id;
    }

    public function select(
        $stmt, Array $params = [], $offset = -1, $count = -1
    ) {
        $params = $this->escape($params);
        $stmt  = vsprintf($stmt, $params);
        $stmt .= $this->addLimit($offset, $count);

        $res = $this->query($stmt);
        $rv = array();
        while(($row = $res->fetch_assoc())) {
            $rv[] = $row;
        }
        $res->close();
        return $rv;
    }

    public function selectRow($stmt, Array $params = array(), $row = 0) {
        $res = $this->select($stmt, $params, $row, 1);
        if(empty($res)) {
            return array();
        }
        return array_shift($res);
    }

    public function selectSingle($stmt, Array $params = array()) {
        $res = $this->selectRow($stmt, $params);
        if(empty($res)) {
            return null;
        }
        return array_shift($res);
    }

    public function selectKeyValue(
        $key, $value, $table, $where = "", Array $params = array()
    ) {
        $key = $this->escape($key);
        $value = $this->escape($value);
        $table = $this->escape($table);
        $params = $this->escape($params);

        $stmt =
            "SELECT `" . $key . "` AS `k`, `" . $value . "` AS `v` " .
            "FROM `" . $table . "` ";

        if($where != "") {
            $stmt .= "WHERE " . $where;
        }

        $stmt  = vsprintf($stmt, $params);
        $res = $this->query($stmt);
        $rv = array();
        while(($row = $res->fetch_assoc())) {
            $rv[$row['k']] = $row['v'];
        }
        $res->close();
        return $rv;
    }

    public function selectKeyValues(
        $key, Array $values, $table, $where = "", Array $params = array()
    ) {
        $key = $this->escape($key);
        $table = $this->escape($table);
        $values = $this->escape($values);
        $params = $this->escape($params);

        $stmt =
            "SELECT `" . $key . "` AS `k`, `" .
            implode("`, `", $values) . "` " .
            "FROM `" . $table . "` ";

        if($where != "") {
            $stmt .= "WHERE " . $where;
        }

        $stmt  = vsprintf($stmt, $params);
        $res = $this->query($stmt);
        $rv = array();
        while(($row = $res->fetch_assoc())) {
            $key = $row['k'];
            unset($row['k']);
            $rv[$key] = $row;
        }
        $res->close();
        return $rv;
    }

    public function selectCount(
        $table, Array $where = array(), Array $params = array(), $join = 'AND'
    ) {
        $stmt =
            "SELECT COUNT(*) AS `cnt` " .
            "FROM `" . $table . "` ";

        if(\count($where) > 0) {
            $stmt .= "WHERE " . \implode(' ' . $join . ' ', $where);
        }
        return $this->selectSingle($stmt, $params);
    }

    public function entryExists($table, $column, $content) {
        $where = array();
        $where[] = '`' . $column . '` = \''. $this->escape($content) . '\'';
        if($this->selectCount($table, $where) == 0) {
            return false;
        }
        return true;
    }

    public function escape($data) {
        if(!is_array($data)) {
            return $this->db->escape_string($data);
        }
        $rv = array();
        foreach($data as $v) {
            $rv[] = $this->escape($v);
        }
        return $rv;
    }

    public function convertFromMysqlDate($date) {
        if(empty($date)) {
            return '';
        }
        list($y, $m, $d) = \explode("-", $date);
        return $d . '.' . $m . '.' . $y;
    }

    public function convertToMysqlDate($date) {
        if(empty($date)) {
            return '0000-00-00';
        }

        $date = \explode('.', $date);
        return $date[2] . '-' . $date[1] . '-' . $date[0];
    }

    private function query($stmt) {
        $res = $this->db->query($stmt);
        if($res === false) {
            throw new DatabaseException(
                "query <" . $stmt . "> failed! \n\n" . $this->db->error,
                DatabaseException::QUERY_FAILED
            );
        }
        return $res;
    }

    private function addLimit($offset, $count) {
        $offset = \preg_replace('/[^0-9-]/', '', $offset);
        $count = \preg_replace('/[^0-9-]/', '', $count);

        if($offset == -1 || $count == -1) {
            return "";
        }
        if($offset == "" || $count == "") {
            $offset = 0;
            $count = 10;
        }
        return " LIMIT " . $offset . ", " . $count;
    }
}