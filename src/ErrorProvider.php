<?php

/**
 *  SFW2 - SimpleFrameWork
 *
 *  Copyright (C) 2018  Stefan Paproth
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

class ErrorProvider {
    const IS_EMPTY    = 'Das Feld darf nicht leer sein.';
    const IS_EQUAL    = 'Der Inhalt darf nicht mit <NAME2> übereinstimmen.';
    const WAS_WRG_REP = 'Der Inhalt wurde fehlerhaft wiederholt.';
    const INV_DATE    = 'Das Datum hat ein ungültiges Format (TT.MM.JJJJ).';
    const RAN_DATE    = 'Das Jahr muss zwischen den Jahren 1902 und 2038 liegen.';
    const FUT_DATE    = 'Das Datum muss in der Zukunft liegen.';
    const CMP_TIME    = 'Der Wert muss größer als <NAME2> sein.';
    const INV_TIME    = 'Die Uhrzeit hat ein ungültiges Format (hh:mm).';
    const TO_LONG     = 'Der Inhalt ist zu lang. Es sind maximal <MAX> Zeichen erlaubt.';
    const WRG_SEL     = 'Es wurde eine ungültige Auswahl getroffen.';
    const NUM_ONLY    = 'Der Inhalt darf nur aus Zahlen bestehen.';
    const PHON_ONLY   = 'Der Inhalt darf nur aus Zahlen und \'+,-, ,(,),/\' bestehen.';
    const EXISTS      = 'Der Inhalt existiert bereits.';
    const NOT_EXISTS  = 'Der Inhalt existiert nicht.';
    const INV_URL     = 'Die URL hat ein ungültiges Format (http://www.example.de).';
    const NO_FILES    = 'Es wurden keine gültigen Dateien ausgewählt.';
    const NO_FILE     = 'Es wurde keine gültige Datei ausgewählt.';
    const INV_IMG     = 'Die Datei ist keine gültige Bilddatei.';
    const INV_CSV     = 'Die Datei ist keine gültige CSV-Datei.';
    const IS_NOT_STR  = 'Der Inhalt darf nur aus Zahlen und Buchstaben A-Z bestehen.';
    const IS_NOT_FN   = 'Der Inhalt darf nur \'a-z\', \'A-Z\', \'0-9\', \'.\' und \'_\' bestehen.';
    const IS_NOT_SMP  = 'Der Inhalt darf nur \'a-z\', \'A-Z\' und \'0-9\'bestehen.';


    const IS_NOT_SAVE = '<NAME> ist nicht sicher genug.';
    const INV_CHAR    = 'Das Feld enthält ungültige Zeichen.';
    const INV_STR     = '<NAME> ist ungültig.';
    const EXPIRED     = '<NAME> ist abgelaufen.';

    private $errors = [];

    public function addUserError(string $str, $id = null) {
        $this->errors[] = array(
            'description' => $str,
            'elementId'   => $id
        );
    }

    public function addError(string $errno, array $rp = [], $id = null) {
        $keys = array_keys($rp);
        $vals = array_values($rp);
        $this->errors[] = array(
            'description' => str_replace($keys, $vals, $errno),
            'elementId'   => $id
        );
    }

    public function hasErrors($id = null) : bool {
        return $this->checkArray($this->errors, $id);
    }

    private function checkArray($arr, $id = null) : bool {
        if($id == null) {
            return !empty($arr);
        }

        foreach($arr as $v) {
            if($v['elementId'] == $id) {
                return true;
            }
        }
        return false;
    }
}