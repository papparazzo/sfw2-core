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

class ErrorProvider {
    const IS_EMPTY              = '<NAME> darf nicht leer sein.';
    const IS_MISSING            = '<NAME> fehlt.';
    const IS_WRONG              = '<NAME> ist nicht korrekt.';
    const IS_INVALID            = '<NAME> ist ungültig.';
    const IS_EXPIRED            = '<NAME> ist abgelaufen.';
    const IS_NOT_SAVE_ENOUGH    = '<NAME> ist nicht sicher genug.';
    const IS_EQUAL              = '<NAME> darf nicht mit <NAME2> übereinstimmen.';
    const WAS_WRONG_REPEATED    = '<NAME> wurde fehlerhaft wiederholt.';
    const INVALID_DATE          = '<NAME> hat ein ungültiges Format (TT.MM.JJJJ).';
    const DATE_OUT_OF_RANGE     = '<NAME> muss zwischen den Jahren 1902 und 2038 liegen.';
    const DATE_IS_NOT_FUTRE     = '<NAME> muss in der Zukunft liegen.';
    const VAL1_NOT_GREATER      = '<NAME> muss größer als <NAME2> sein.';
    const INVALID_TIME          = '<NAME> hat ein ungültiges Format (hh:mm).';
    const INVALID_CHAR          = '<NAME> enthält ungültige Zeichen.';
    const IS_TO_LONG            = '<NAME> ist zu lang. Es sind maximal <MAX> Zeichen erlaubt.';
    const WRONG_SELECTION       = '<NAME> wurde eine ungültige Auswahl getroffen.';
    const NUM_ONLY              = '<NAME> darf nur aus Zahlen bestehen.';
    const PHON_ONLY             = '<NAME> darf nur aus Zahlen und \'+,-, ,(,),/\' bestehen.';
    const TIME_NOT_EXPIRED      = 'Du hast bereits <AT> einen Eintrag erstellt. Warte bitte <EX>';
    const ERROR_DEL             = '<NAME> konnte nicht gelöscht werden.';
    const DOES_EXISTS           = '<NAME> existiert bereits.';
    const DOES_NOT_EXISTS       = '<NAME> existiert nicht.';
    const INVALID_URL           = '<NAME> hat ein ungültiges Format (http://www.example.de).';
    const SEND_FAILED           = 'Das Versenden der E-Mail schlug fehl!';
    const NO_PERMISSION         = 'Fehlende Berechtigung für die Operation.';
    const NO_FILES              = 'Es wurden keine gültigen Dateien ausgewählt.';
    const NO_FILE               = 'Es wurde keine gültige Datei ausgewählt.';
    const INVALID_IMAGE         = 'Die Datei <NAME> ist keine gültige Bilddatei.';
    const INVALID_CSV           = 'Die Datei <NAME> ist keine gültige CSV-Datei.';
    const INTERN_ERROR          = 'Es ist ein interner Fehler aufgetreten.';
    const UNKNOWN_ERROR         = 'Es ist ein unbekannter Fehler aufgetreten.';
    const IS_NOT_STR            = '<NAME> darf nur aus Zahlen und Buchstaben A-Z bestehen.';
    const IS_NOT_FN             = '<NAME> darf nur \'a-z\', \'A-Z\', \'0-9\', \'.\' und \'_\' bestehen.';
    const IS_NOT_SMP            = '<NAME> darf nur \'a-z\', \'A-Z\' und \'0-9\'bestehen.';
    const INVALID_CHARS_REMOVED = 'Ungültige Zeichen <NAME> wurden entfernt.';

    protected $errors      = [];
    protected $warnings    = [];

    public function addError($errno, $rp = [], $id = null) {
        $keys = array_keys($rp);
        $vals = array_values($rp);
        $this->errors[] = [
            'description' => str_replace($keys, $vals, $errno),
            'elementId'   => $id
        ];
    }

    public function addWarning($errno, $rp = [], $id = null) {
        $keys = array_keys($rp);
        $vals = array_values($rp);
        $this->warnings[] = [
            'description' => str_replace($keys, $vals, $errno),
            'elementId'   => $id
        ];
    }

    public function getContent($clearBuffer = false) {
        if($clearBuffer) {
            $this->clearBuffer();
        }

        return [
            'errors'   => $this->errors,
            'warnings' => $this->warnings
        ];
    }

    public function hasErrors($id = null) {
        return $this->checkArray($this->errors, $id);
    }

    public function hasWarning($id = null) {
        return $this->checkArray($this->warnings, $id);
    }

    public function clearBuffer() {
        $this->errors   = [];
        $this->warnings = [];
    }

    private function checkArray($arr, $id = null) {
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