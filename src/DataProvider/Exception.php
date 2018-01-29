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

namespace SFW2\Core\DataProvider;

use SFW2\Core\SFW2Exception;

class Exception extends SFW2Exception {

    const IS_EMPTY    = 1;
    const IS_MISS     = 2;
    const IS_WRONG    = 3;
    #const IS_NOT_STRONG_ENO = 4; #'<NAME> ist nicht sicher genug.';
    #const IS_EQUAL    = 5;# '<NAME> darf nicht mit <NAME2> übereinstimmen.';
    const INVALID_URL     = 6;
    const INVALID_TIME    = 7;

/*
    const INV_DATE    = '<NAME> hat ein ungültiges Format (TT.MM.JJJJ).';
    const RAN_DATE    = '<NAME> muss zwischen den Jahren 1902 und 2038 liegen.';
    const FUT_DATE    = '<NAME> muss in der Zukunft liegen.';
    const CMP_TIME    = '<NAME> muss größer als <NAME2> sein.';
    '<NAME> hat ein ungültiges Format (hh:mm).';
    const INV_CHAR    = '<NAME> enthält ungültige Zeichen.';
    const INV_STR     = '<NAME> ist ungültig.';
    const EXPIRED     = '<NAME> ist abgelaufen.';
    const TO_LONG     = '<NAME> ist zu lang. Es sind maximal <MAX> Zeichen erlaubt.';
    const WRG_SEL     = '<NAME> wurde eine ungültige Auswahl getroffen.';
    const NUM_ONLY    = '<NAME> darf nur aus Zahlen bestehen.';
    const PHON_ONLY   = '<NAME> darf nur aus Zahlen und \'+,-, ,(,),/\' bestehen.';
    const ERR_DEL     = '<NAME> konnte nicht gelöscht werden.';
    const ERR_UNDEL   = '<NAME> konnte nicht wieder hergestellt werden.';
    const EXISTS      = '<NAME> existiert bereits.';
    const NOT_EXISTS  = '<NAME> existiert nicht.';

    const SEND_FAILED = 'Das Versenden der E-Mail schlug fehl!';
    const NOT_PER     = 'Fehlende Berechtigung für die Operation.';
    const NO_FILES    = 'Es wurden keine gültigen Dateien ausgewählt.';
    const NO_FILE     = 'Es wurde keine gültige Datei ausgewählt.';
    const INV_IMG     = 'Die Datei <NAME> ist keine gültige Bilddatei.';
    const INV_CSV     = 'Die Datei <NAME> ist keine gültige CSV-Datei.';
    const INT_ERR     = 'Es ist ein interner Fehler aufgetreten.';
    const UKN_ERR     = 'Es ist ein unbekannter Fehler aufgetreten.';
    const IS_NOT_STR  = '<NAME> darf nur aus Zahlen und Buchstaben A-Z bestehen.';
    const IS_NOT_FN   = '<NAME> darf nur \'a-z\', \'A-Z\', \'0-9\', \'.\' und \'_\' bestehen.';
    const IS_NOT_SMP  = '<NAME> darf nur \'a-z\', \'A-Z\' und \'0-9\'bestehen.';
    const IN_CHAR_RE = 'Ungültige Zeichen <NAME> wurden entfernt.';
 *
 */
}
