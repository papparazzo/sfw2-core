<?php

/**
 *  SFW2 - SimpleFrameWork
 *
 *  Copyright (C) 2023  Stefan Paproth
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

declare(strict_types=1);

namespace SFW2\Core\Utils;

use DateTime;
use Exception;
use IntlDateFormatter;

class DateTimeHelper
{
    public const FULL_DATE = 'E, dd. MMM yyyy';

    public function __construct(
        private readonly string $timeZone,
        private readonly string $locale
    )
    {
    }

    /**
     * @throws Exception
     */
    public function getDate(string $pattern, string $date = 'now'): string
    {
        if($date == '') {
            return '';
        }
        $x = new IntlDateFormatter(
            locale: $this->locale,
            dateType: IntlDateFormatter::FULL,
            timeType: IntlDateFormatter::FULL,
            timezone: $this->timeZone,
            pattern: $pattern
        );

        return $x->format($this->getDateTimeObject($date));
    }

    /**
     * @throws Exception
     */
    public function getDateTimeObject(string $date): DateTime
    {
        if(preg_match('/^[0-9]*$/', $date)) {
            return (new DateTime())->setTimestamp((int)$date);
        }
        return new DateTime($date);
    }

    public function getTimeString(string $startTime, string $endTime): string
    {
         # FIXME Check if time == timestamp
        if ($startTime == '' && $endTime == '') {
            return '';
        }

        if($startTime !== '' && $endTime == '') {
            return sprintf('ab %s Uhr', mb_substr($startTime, 0, 5)); # FIXME I18N
        }

        if($startTime !== '') {
            $startTime = sprintf('von %s ', mb_substr($startTime, 0, 5)); # FIXME I18N
        }

        if($endTime !== '') {
            $startTime .= sprintf('bis %s ', mb_substr($endTime, 0, 5)); # FIXME I18N
        }

        return $startTime . 'Uhr'; # FIXME I18N
    }

    /**
     * @throws Exception
     */
    public function getDateString(string $startDate, string $endDate): string
    {
        $startDate = $this->getDate(self::FULL_DATE, $startDate);
        $endDate = $this->getDate(self::FULL_DATE, $endDate);

        if ($startDate != '' && $endDate != '') {
            return sprintf('%s bis %s', $startDate, $endDate); # FIXME I18N
        }

        return "$startDate$endDate";
    }
}